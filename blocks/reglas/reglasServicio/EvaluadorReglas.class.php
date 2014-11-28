<?php

namespace reglas\reglasServicio;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Mensaje.class.php");
include_once ("Tipos.class.php");
include_once (dirname(__FILE__)."/class/evalmath.class.php");
include_once ("ConstructorReglas.class.php");
include_once ("GestorFuncion.class.php");
include_once ("GestorParametro.class.php");
include_once ("GestorVariable.class.php");

class EvaluadorReglas{
    
	

	private $parametros ;
    public  $mensaje;
    private $evaluador;
    private $usuario;
    
    function __construct(){
    	$this->evaluador = new \EvalMath();
    	$this->mensaje = new Mensaje();
    	$this->usuario = $_REQUEST['usuario'];
    	
    }
    
    private function evaluar($valor){
    	return $this->evaluador->e($valor);
    }
    
    private function getErrorEvaluador(){
    	return $this->evaluador->last_error;
    }
    
    public function evaluarParametro($valor = '', $tipo = ''){
    	if(Tipos::evaluarTipo($valor,$tipo))  	return $valor;
    	return false;
    	
    }
    
    public function evaluarVariable($valor = '', $tipo = '' , $rango = ''){
    	if(Tipos::evaluarTipo($valor,$tipo)&&Rango::evaluarRango($valor , $tipo , $rango))
    		  	return $this->evaluar($valor);
    	return false;
    	
    }
    
    /*
     * reemplaza en la cadena los parametros
     */
    private function procesarParametros($cadena = '' ){

    	
    	$needle = "_";
    	$lastPos = 0;
    	$positions = array();
    	
    	while (($lastPos = strpos($cadena, $needle, $lastPos))!== false) {
    		$positions[] = $lastPos;
    		$lastPos = $lastPos + strlen($needle);
    	}	
    	$ListaParametros =  $this->getListaParametros();
    	if(!$ListaParametros||count($positions)%2!=0){
    		
    		$this->mensaje->addMensaje("101","errorCadenaMalFormada",'error');
    		return false;
    	}
    	
    	foreach ($ListaParametros as $parametro){
    		$cadena = str_replace("_".$parametro['nombre']."_", $parametro['valor'], $cadena);
    	}
    	
    	return $cadena;
    	
    }
    
    /*
     * reemplaza en la cadena las variables
    */
    private function procesarVariables($cadena = '' , $valores = ''){
    
    	$listaVariables = $this->getListaVariables();
    	
    	if(is_array($listaVariables)){
    		
    		

    		foreach ($valores as $valor){
    			foreach ($listaVariables as $variable){
    				if($variable['nombre']==$valor[0]&&Rango::evaluarRango($valor[1],$variable['tipo'],$variable['rango']))$cadena = str_replace($valor[0], $valor[1], $cadena);
    			}
    		}
    	}
    	
    	 
    	return $cadena;
    	 
    }
    
    /*
     * reemplaza en la cadena las funciones
    */
    public function procesarFunciones($cadena = ''){
    
    	$listaFunciones = $this->getListaFunciones();
    	 
    	if(is_array($listaFunciones)){
    
        	foreach ($listaFunciones as $funcion){
    			
        		while(strpos($cadena,$funcion['nombre'])!==false){
        			$posiscionNombre = strpos($cadena,$funcion['nombre'])+strlen($funcion['nombre']);
        			$longitudNombre = strlen(strlen($funcion['nombre']));
        			$cadenaArray =  str_split($cadena);
        			
        			$entradaFuncion = '';
        			$aReemplazar = $funcion['nombre'];
        			for($i = $posiscionNombre ; $i<strlen($cadena);$i++){
        				$entradaFuncion .=$cadena[$i];
        				$aReemplazar .=$cadena[$i];
        				if($cadena[$i]==")")break;
        			}

        			$entradaFuncion =  trim($entradaFuncion);
        			
        			$entradaFuncion = str_replace("(", "", $entradaFuncion);
        			$entradaFuncion = str_replace(")", "", $entradaFuncion);
        			
        			$listaVariablesFuncion = $this->arrayVariablesFuncion($funcion['valor'] , $entradaFuncion);
        			$valores =  $listaVariablesFuncion;
        			
        			$funcionEvaluada = $this->evaluarFuncion($funcion['valor'], $valores, $funcion['tipo'] , $funcion['rango']);
        			
        			
        			$cadena = str_replace($aReemplazar, $funcionEvaluada, $cadena);
        		}
        		
    				
    			}
    		
    	}
    	 
    
    	return $cadena;
    
    }
    
    private function arrayVariablesFuncion($cadena = '' , $valores = ''){
    	
    	$listaVariables = $this->getListaVariables();
    	$listaValores = explode(",",$valores);
    	if(!$listaValores&&$valores!='') $listaValores = array($valores);
    	$resultado = array();
    	$nombres = array();
    	
    	if(is_array($listaVariables)&&is_array($listaValores)){
    		
    		
    		foreach ($listaVariables as $variable){
                
    			if(strpos($cadena,$variable['nombre'])!==false)
    					$nombres[] = $variable['nombre'];
    		
    
    	    } 
    	    if(count($nombres)==count($listaValores)){

    	    	for ($i = 0 ; $i<count($listaValores) ; $i++){
    	    		$resultado[] = array($nombres[$i],$listaValores[$i]);
    	    	}
    	    }
    	    
    		
    	}
    	
    	return $resultado;
    
    }
    
    
    
    
    public function evaluarFuncion($cadena = '', $valores = '', $tipo = '' , $rango = ''){
    	
    	//1. Procesa los parametros y los Reemplaza
    		$cadena = $this->procesarParametros($cadena  );
    	
    	//2. Reemplaza las variables y las evalua
    	if(is_array($valores)){
    		$cadena = $this->procesarVariables($cadena , $valores );
    	}
    	
    	//3. Evalua toda la funcion
    	$valor = $this->evaluar($cadena);
    	 
    	if(!$valor) return $this->getErrorEvaluador();
    	
    	//4. valida el tipo
    	if(Tipos::evaluarTipo($valor,$tipo)&&Rango::evaluarRango($valor , $tipo , $rango))  	return $valor;
    	
    	return false;
    	 
    }
    
    private function getDatosRegla($idRegla){
    	
    	$Oreglas =  new ConstructorReglas();
    	$datosRegla = $Oreglas->consultarRegla($idRegla,'','','','','',1);
    	 
    	if(!$datosRegla) {
    		$this->mensaje = &$Oreglas->mensaje;
    		return false;;
    	}
    	unset($Oreglas);
    	return $datosRegla;
    	
    	 
    }
    
    private function getListaFunciones(){
    	
    	$funcion  =  new GestorFuncion();
    	$listaFunciones = $funcion->consultarFuncion('','','','','','',1);
    	
    	if(!$listaFunciones) {
    		$this->mensaje = &$funcion->mensaje;
    		unset($funcion);
    		return false;
    	}
    	unset($funcion);
    	return $listaFunciones;
    }
    
    private function getListaVariables(){
    	 
    	$variable  =  new GestorVariable();
    	$listaVariables = $variable->consultarVariable('','','','','','',1);
    	if(!$listaVariables) {
    		$this->mensaje = &$variable->mensaje;
    		unset($variable);
    		return false;
    	}
    	unset($variable);
    	return $listaVariables;
    }
    
    private function getListaParametros(){
    
    	$parametro  =  new GestorParametro();
    	$listaParametro = $parametro->consultarParametro('','','','','','',1);
    	if(!$listaParametro) {
    		$this->mensaje = &$parametro->mensaje;
    		unset($parametro);
    		return false;
    	}
    	unset($parametro);
    	return $listaParametro;
    }
    
    public function evaluarRegla($idRegla = '', $valores = '', $idProceso = '' ){
    	
    	
    	
    	
    	//consulta la regla
    	$datosRegla = $this->getDatosRegla($idRegla);
    	
    	
    	//asigna datos de la regla
    	$idRegla = $datosRegla[0]['id'];
    	$nombreRegla = $datosRegla[0]['nombre'];
    	$procesoRegla = $datosRegla[0]['proceso'];
    	$tipoRegla = $datosRegla[0]['tipo'];
    	$valorRegla = $datosRegla[0]['valor'];
    	
    	$cadena = $valorRegla;
    	
    	
    	//1. Procesa los parametros y los Reemplaza
    	$cadena = $this->procesarParametros($cadena  );
    	
    	
    	 
    	//2. Reemplaza las variables y las evalua
    	if(is_array($valores)){
    		$cadena = $this->procesarVariables($cadena , $valores );
    	
    	}
    	
    	
    	
    	//3. Reemplaza funciones y las evalua
    	 $cadena = $this->procesarFunciones($cadena);
    	 
    	 //4. Evalua toda la regla
    	 $valor = $this->evaluar($cadena);
    	 
    	 if(!$valor) return $this->getErrorEvaluador();
    	 $valor =  (bool)$valor; 

    	 if(Tipos::evaluarTipo($valor,$tipoRegla))  	return $valor;
    	 
    	 return false;
    	
    }
    
        


}

?>
