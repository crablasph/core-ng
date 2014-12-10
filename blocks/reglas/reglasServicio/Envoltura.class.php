<?php
namespace reglas;


if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}



include_once ("Mensaje.class.php");

include_once ("GestorUsuariosComponentes.class.php");



include_once ("Registrador.class.php");
include_once ("GestorParametro.class.php");
include_once ("GestorFuncion.class.php");
include_once ("GestorVariable.class.php");
include_once ("ConstructorReglas.class.php");
include_once ("EvaluadorReglas.class.php");



class Envoltura {
    
    
    private $objetos;
    private $nombreObjetoSeleccionado ;
    private $objetoSeleccionado;
    private $miConfigurador;
    public $mensaje;
    
    function __construct() {
    	
    	$this->miConfigurador = \Configurador::singleton ();
    	$this->objetos['GestorUsuariosComponentes'] = 'GestorUsuariosComponentes';
    	
    	//$this->objetos['EstructurarReglas'] = 'EstructurarReglas';
        
    	$this->objetos['Registrador'] = 'Registrador';
    	$this->objetos['GestorParametro'] = 'GestorParametro';
    	$this->objetos['GestorFuncion'] = 'GestorFuncion';
    	$this->objetos['GestorVariable'] = 'GestorVariable';
    	$this->objetos['ConstructorReglas'] = 'ConstructorReglas';
    	$this->objetos['EvaluadorReglas'] = 'EvaluadorReglas';
    	 
    	
    	$this->mensaje = New Mensaje();
    
    }
    
    private function validarMetodo($method_name){
    	
    	foreach ($this->objetos as $idx ){
    		
    		$idx = __NAMESPACE__."\\".$idx;
    		$obj =  New $idx;
    		
    		if(method_exists($obj, $method_name)){
    			$this->nombreObjetoSeleccionado = $idx;
    			$this->objetoSeleccionado = $obj;
    			$this->mensaje = &$obj->mensaje;
    			return true;
    		}
    		unset($obj);	
    		 
    	}
    	
    	$this->mensaje->addMensaje("101","metodoNoExiste",'error');
    	return false;
    	
    	 
    }
    
    private function validarAcceso(){
    	
    	if(isset($_SERVER['HTTP_USUARIO'])||isset($_SERVER['PHP_AUTH_USER'])){
    		if(isset($_SERVER['PHP_AUTH_USER'])) $_REQUEST['usuario'] = $_SERVER['PHP_AUTH_USER'];
    		else	$_REQUEST['usuario'] = $_SERVER['HTTP_USUARIO'];
    	}else $_REQUEST['usuario'] = 'anonimo';
    	 
    	$validarAcceso =  new GestorUsuariosComponentes($_REQUEST['usuario']);
    	 
    	if(!$validarAcceso->habilitarServicio()){
    	
    		$this->mensaje = &$validarAcceso->mensaje;
    		return false;
    		
    	}
    	unset($validarAcceso);
    	return true;
    	 
    }
    
    public function __call($method_name, $arguments){
    	$variableSoap =  $this->miConfigurador->getVariableConfiguracion ( "soapVariable");
    	
      if($this->validarAcceso()){	

	    	if($this->validarMetodo($method_name)){
	    		
	    		$ejecucion = call_user_func_array(array($this->objetoSeleccionado , $method_name), $arguments);
	    		unset($this->objetoSeleccionado);
	    		if(!$ejecucion){
	    			if(isset($_REQUEST[$variableSoap]))		return $this->mensaje->getLastMensaje("soap");
	    			else 		return $this->mensaje->getLastMensaje();
	    		}		
	    		return $ejecucion ;
	    	}
	    	
	    	return false;
    	
       }
     
       if(isset($_REQUEST[$variableSoap]))		return $this->mensaje->getLastMensaje("soap");
	    			else 		return $this->mensaje->getLastMensaje();
    }
    
    
	
        
    
    
    

}

?>
