<?php
namespace reglas\reglasServicio;


if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}



include_once ("Mensaje.class.php");

include_once ("GestorUsuariosComponentes.class.php");

//include_once ("EstructurarReglas.class.php");

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
   
    public $mensaje;
    
    function __construct() {
    	
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
    
    public function __call($method_name, $arguments){
    	
    	if($this->validarMetodo($method_name)){
    		
    		$ejecucion = call_user_func_array(array($this->objetoSeleccionado , $method_name), $arguments);
    		unset($this->objetoSeleccionado);
    		if(!$ejecucion){
    			return $this->mensaje->getLastMensaje("soap");
    		}		
    		return $ejecucion ;
    	}
    	return false;
    }
    
    
	
        
    
    
    

}

?>
