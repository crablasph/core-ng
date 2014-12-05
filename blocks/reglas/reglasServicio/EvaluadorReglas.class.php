<?php

namespace reglas;

use \SoapClient as SoapClient;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Mensaje.class.php");
include_once ("Tipos.class.php");
include_once ("Rango.class.php");
include_once ("Persistencia.class.php");
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
    private $listaOperadorLogico;
    private $listaOperadorComparacion;
    
    function __construct(){
    	$this->evaluador = new \EvalMath();
    	$this->mensaje = new Mensaje();
    	$this->usuario = $_REQUEST['usuario'];
    	$this->listaOperadorLogico = array('&','|','^','~');
    	$this->listaOperadorComparacion = array('===','!==','==','<>','>=','<=','>','<');
    }
    
    private function evaluarFuncionBD($valor = '',$ruta = ''){

    	$persistencia = new Persistencia();
    	$query = $valor;
    	$persistencia->setQuery($query);
    	
    	$consulta = $persistencia->ejecutar("busqueda") ;

    	if($consulta ===  false){
    		$this->mensaje = &$persistencia->mensaje;
    		return false;
    	}
    	unset ($persistencia);
    	return $consulta[0][0]; 
    	
    }
    
    private function evaluarFuncionSoap($valor = '',$ruta = ''){
    	
    	try {
    		$options = array(
    				
    				'exception'=>true,
    				'trace'=>1,
    				'login' => $this->usuario,
    				'password' => '123456',
    				'cache_wsdl'=>WSDL_CACHE_NONE
    		);
    		
    		$client = new SoapClient($ruta, $options);
    		
    		//procesa Valor para se llamado como metodo
    		
    		$argumentos =  array();
    		
    		$pp = strpos ($valor,'(');
    		$pl = strrpos ($valor,')');
    		$longitud = $pl - $pp;
            $cadenaArgumentos = substr($valor,$pp,$longitud+1);
            $metodo = str_replace ($cadenaArgumentos,'',$valor);
            
    		$argumentos =  explode(",",$cadenaArgumentos);
    		
    		if(!is_array($argumentos)) $argumentos = array($cadenaArgumentos);
    		
    		$ejecucion = $client->__soapCall(  $metodo , $argumentos );
    		
    		return $ejecucion; 
    	
    	} catch (\Exception $e) {
    		$this->mensaje->addMensaje("1000","errorSoapCall",'error');
    		
    		return false;
    		
    		
    	}
    	
    	return false;
    	 
    }
    
    
    
    private function procesarOperadoresComparacion($valor=''){
    	
    	foreach ($this->listaOperadorComparacion as $operador){
    		
    		if(strpos($valor,$operador)!==false){
    			$lista =  explode($operador,$valor);
    			$izquierda = $lista[0];
    			$derecha = $lista[1];
    			switch ($operador){
    				case '===':
    					$valor = ($izquierda === $derecha);
    					break;
    				case '==':
    					$valor = ($izquierda == $derecha);
    					break;
    				case '!==':
    					$valor = ($izquierda !== $derecha);
    					break;
    				case '<>':
    					$valor = ($izquierda <> $derecha);
    					break;
    				case '>=':
    					$valor = ($izquierda >= $derecha);
    					break;
    				case '<=':
    					$valor = ($izquierda <= $derecha);
    					break;
    				case '>':
    					$valor = ($izquierda > $derecha);
    					break;
    				case '<':
    					$valor = ($izquierda < $derecha);
    					break;
    				default:
    					break;
    			}
    		}
    	}
    	
    	return $valor;
    	
    }
    
    private function evaluar($valor = '', $categoria = '',$ruta = ''){
    	
    	switch ($categoria){
    		case '1':
    			$valor = $this->procesarOperadoresComparacion($valor);
    			return $this->evaluador->e($valor);
    			break;
    		case '2':
    			return $this->evaluarFuncionBD($valor,$ruta);
    			break;
    		case '3':
    			return $this->evaluarFuncionSoap($valor,$ruta);
    			break;
    		default:
    			$valor = $this->procesarOperadoresComparacion($valor);
    			return $this->evaluador->e($valor);
    			break;	
    	}
    	
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
    	
    	//if(!$ListaParametros||count($positions)%2!=0){
    	if(!$ListaParametros){	
    		$this->mensaje->addMensaje("101","errorCadenaMalFormada",'error');
    		return false;
    	}
    	
    	foreach ($ListaParametros as $parametro){
    		$cadena = str_replace("_".$parametro['nombre']."_", base64_decode($parametro['valor']), $cadena);
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
        		$funcion['ruta'] = base64_decode($funcion['ruta']);
        		$funcion['valor'] = base64_decode($funcion['valor']);
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
        			
        			$funcionEvaluada = $this->evaluarFuncion($funcion['valor'], $valores, $funcion['tipo'] , $funcion['rango'],$funcion['categoria'],$funcion['ruta']);
        			
        			
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
    
    
    
    
    public function evaluarFuncion($cadena = '', $valores = '', $tipo = '' , $rango = '',$categoria = '',$ruta = ''){
    	
    	//1. Procesa los parametros y los Reemplaza
    		$cadena = $this->procesarParametros($cadena  );
    	
    	//2. Reemplaza las variables y las evalua
    	if(is_array($valores)){
    		$cadena = $this->procesarVariables($cadena , $valores );
    	}
    	
    	//3. Evalua toda la funcion
    	$valor = $this->evaluar($cadena,$categoria,$ruta);
    	
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
    
    private function procesarSentencias($valorRegla = ''){
    	
    	if($valorRegla=='') return false;
        
    	$cadenaArray =  str_split($valorRegla);
    	
    	$resultado = array();
    	$cadena = '';
    	
    	
    	foreach ($cadenaArray as $elemento){
    		
    		if(in_array($elemento , $this->listaOperadorLogico)){
    			$resultado[] = array($elemento,$cadena);
    			$cadena = '';
    		}else	$cadena .= $elemento;
  	
    	}
    	
    	if(count($resultado) == 0){
    		$resultado[] = array('',$cadena);
    	} 
    	
    	return $resultado;
    		
    		
    	
    }
    
    private function evaluarResultados($lista){
    	
    	$valor =  true;
    	
    	foreach ($lista as $elemento){
    	  $operador = $elemento[0];
    	  $cadena = (bool) $elemento[1];
    	  
    	  switch ($operador){
    	  	case '&':
    	  		$valor = $valor & $cadena;
    	        break;
    	  	case '|':
    	  		$valor = $valor | $cadena;
    	  	    break;
    	  	case '^':
    	  		$valor = $valor ^ $cadena;
    	  		break;
    	  	case '~':
    	  		$valor = ~ $cadena;
    	  		break;
    	  		
    	  	default:
    	  		$valor = $valor & $cadena;
    	  	break;
    	  }
    	  
    	  
    		
    	}
    	
    	return (bool) $valor;
    	
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
    	
    	
    	
    	//0. Procesa Sentencias
    	$listaSentencias = $this->procesarSentencias($valorRegla);
    	$listaResultados = array();
        
    	if(is_array($listaSentencias)){
    		
    		foreach ($listaSentencias as $sentencia){
                 
    			$operador = $sentencia[0];
    			$cadena = trim($sentencia[1]);
    			
    			//1. Procesa los parametros y los Reemplaza
    			$cadena = $this->procesarParametros($cadena);
    			 
    			//2. Reemplaza las variables y las evalua
    			if(is_array($valores)){
    				$cadena = $this->procesarVariables($cadena , $valores );
    			}
    			
    			//3. Reemplaza funciones y las evalua
    			$cadena = $this->procesarFunciones($cadena);
    			
    			//4. Evalua toda la regla
    			$valor = $this->evaluar($cadena);
    			$valor =  (bool) $valor;
    			  			
    			
    			if(Tipos::evaluarTipo($valor,$tipoRegla))  $listaResultados[] = array($operador,$valor)	;//return $valor;
    			
    			
    		}
    		
    		return  $this->evaluarResultados($listaResultados);
    		
    	}
    	    	 
    	 return false;
    	
    }
    
        


}

?>
