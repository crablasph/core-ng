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
include_once ("GestorUsuariosComponentes.class.php");

class EvaluadorReglas{
    
	

	private $parametros ;
    public  $mensaje;
    private $evaluador;
    private $usuario;
    private $listaOperadorLogico;
    private $listaOperadorAritmetico;
    private $listaOperadorComparacion;
    private $pasosEvaluacionSentenciasRegla;
    private $verificadorAcceso;
    
    function __construct(){
    	$this->evaluador = new \EvalMath();
    	$this->mensaje = new Mensaje();
    	$this->usuario = $_REQUEST['usuario'];
    	
    	//esto debe cambairse por los valores dela bd
    	$this->listaOperadorLogico = array('&','|','^','~');
    	$this->listaOperadorComparacion = array('===','!==','==','<>','>=','<=','>','<');
    	$this->listaOperadorAritmetico = array( "+","-","*","/","%","**");
    	
    	$this->verificadorAcceso = new  GestorUsuariosComponentes();
  
    }
    
    public function getFuncionesPredefinidas(){
    	$funcionesPredefinidas =  array( // calc functions emulation
    			'promedio'=>-1,
    			'mediana'=>-1,
    			'moda'=>-1, 'rango'=>-1,
    			'maximo'=>-1,	  'minimo'=>-1,
    			'modulo'=>2,	  'pi'=>0,
    			'log'=> 2,
    			'redondear'=> 2,
    			'number_format'=> 2 , 'number_format_eu'=>2,
    			'suma'=>-1,	 'producto'=>-1,
    			'rand_int'=>2, 'rand_float'=>0,
    			'arctan2'=>2,  'atan2'=>2,
    			'if'=>3,
    			'sin'=>-1,'sinh'=>-1,'arcsin'=>-1,'asin'=>-1,'arcsinh'=>-1,'asinh'=>-1,
    			'cos'=>-1,'cosh'=>-1,'arccos'=>-1,'acos'=>-1,'arccosh'=>-1,'acosh'=>-1,
    			'tan'=>-1,'tanh'=>-1,'arctan'=>-1,'atan'=>-1,'arctanh'=>-1,'atanh'=>-1,
    			'sqrt'=>-1,'abs'=>-1,'ln'=>-1,'log10'=>-1, 'exp'=>-1,'floor'=>-1,'ceil'=>-1
    	);
    	
    	$resultado = array();
    	
    	foreach ($funcionesPredefinidas as $nombre=> $element ){
    		$resultado[] = array(
    				'id'=>99,
    				'nombre'=> $nombre,
    				'descripcion' =>'función Predefinida',
    				'proceso'=>0,
    				'tipo'=>3,
    				'rango'=>'-999999999999999999999999999,999999999999999999999999999',
    				'categoria'=>1,
    				'ruta','',
    				'valor'=>'',
    				'estado'=>'1',
    				'fecha_creacion','01/01/2014'
    				
    		
    		); 
    	}
    	unset($funcionesPredefinida);
    	return $resultado;
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
    
    private function evaluarFuncionSoapProxy($valor = '',$ruta = ''){
    	 
    	try {
    		$options = array(
    
    				'exception'=>true,
    				'trace'=>1,
    				'login' => $this->usuario,
    				'password' => '123456',
    				'proxy_host'=> 'proxy.udistrital.edu.co',
    				'proxy_port'=> '3128',
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
    			$izquierda = trim($lista[0]);
    			if(Tipos::validar_fecha($izquierda)){
    				$izquierda = \DateTime::createFromFormat('d/m/Y', $izquierda);
                	
    			}
    			$derecha = trim($lista[1]);
    			if(Tipos::validar_fecha($derecha)){
    				$derecha = \DateTime::createFromFormat('d/m/Y', $derecha);
    			}
    			
    			switch ($operador){
    				case '===':
    					$valor = (bool) ($izquierda === $derecha);
    					break;
    				case '==':
    					$valor = (bool) ($izquierda == $derecha);
    					break;
    				case '!==':
    					$valor = (bool) ($izquierda !== $derecha);
    					break;
    				case '<>':
    					$valor = (bool) ($izquierda <> $derecha);
    					break;
    				case '>=':
    					$valor = (bool) ($izquierda >= $derecha);
    					break;
    				case '<=':
    					$valor = (bool) ($izquierda <= $derecha);
    					break;
    				case '>':
    					$valor = (bool) ($izquierda > $derecha);
    					break;
    				case '<':
    					$valor = (bool) ($izquierda < $derecha);
    					break;
    				default:
    					break;
    			}
    		}
    	}
    	
    	return $valor;
    	
    }
    
    private function tieneOperaciones($texto){
    	foreach ($this->listaOperadorAritmetico as $operador){
    		if(strpos($texto,$operador)!==false) return true;
    	}
    	return false;
    }
    
    private function evaluarValor($valor = '',$tipo=''){
    	

    	//es fecha
    	if(Tipos::validar_fecha($valor))
    	    return $valor;
    	
    	if($this->tieneOperaciones($valor)!==false)
    		return @Tipos::evaluarTipo($this->evaluador->e($valor),$tipo);
    	 
    	
    	if(Tipos::validarTipo($valor,$tipo))
    		return @Tipos::evaluarTipo($valor,$tipo);
    	
    	
    	if($tipo=='') return @$this->evaluador->e($valor);
    	
    	
    	
    }
    
    private function evaluar($valor = '', $categoria = '',$ruta = '',$tipo=''){
    	
    	switch ($categoria){
    		case '1':
    			
    			$valor =  $this->procesarOperadoresComparacion($valor);
    			 
    			return $this->evaluarValor($valor,$tipo);
    			break;
    		case '2':
    			return $this->evaluarFuncionBD($valor,$ruta);
    			break;
    		case '3':
    			return $this->evaluarFuncionSoap($valor,$ruta);
    			break;
    		case '4':
    			return $this->evaluarFuncionSoapProxy($valor,$ruta);
    			break;
    		default:
    			$valor =  $this->procesarOperadoresComparacion($valor);
    			return $this->evaluarValor($valor,$tipo);
    			break;	
    	}
    	
    }
    
    private function getErrorEvaluador(){
    	return @$this->evaluador->last_error;
    }
    
    public function evaluarParametroTexto($valor = '', $tipo = ''){
    	if(Tipos::validarTipo($valor,$tipo))  	return is_null(Tipos::evaluarTipo($valor,$tipo))?'nulo':Tipos::evaluarTipo($valor,$tipo);
    	return false;
    	
    }
    
    public function evaluarParametro($id = ''){
    	$parametro = $this->getParametro($id);
    	if(!is_array($parametro)) return false;
    	$valor = base64_decode($parametro[0]['valor']);
    	$tipo = $parametro[0]['tipo'];
    	
    	return $this->evaluarParametroTexto($valor, $tipo);
    	 
    }
    
    public function evaluarVariable($id = '',$valor=''){
    	$variable = $this->getVariable($id);

    	if(!is_array($variable)) return false;
    	if($valor == '')$valor = base64_decode($variable[0]['valor']);
    	$tipo = $variable[0]['tipo'];
    	$rango = $variable[0]['rango'];
    	$restriccion = $variable[0]['restriccion'];
    	
    	return $this->evaluarVariableTexto($valor, $tipo, $rango,$restriccion);
    
    }
    
    public function evaluarVariableTexto($valor = '', $tipo = '' , $rango = '',$restriccion = ''){
    	
    	if(Tipos::validarTipo($valor,$tipo)&&Rango::validarRango($valor , $tipo , $rango,$restriccion))
    		return is_null(Tipos::evaluarTipo($valor,$tipo))?'nulo':Tipos::evaluarTipo($valor,$tipo);
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
    		$cadena = str_replace("_".$parametro['nombre']."_", $this->evaluarValor(base64_decode($parametro['valor']),$parametro['tipo']), $cadena);
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
    				if($variable['nombre']==$valor[0]&&Rango::validarRango($valor[1],$variable['tipo'],$variable['rango'],$variable['restriccion']))$cadena = str_replace($valor[0], $this->evaluarValor($valor[1],$variable['tipo']), $cadena);
    				
    			}
    		}
    	}
    	
    	 
    	return $cadena;
    	 
    }
    
    /*
     * obtiene una lista de las variables en la cadena que se pasa en orden
    */
    public function getVariablesListaDelTexto($texto = ''){
    	$listaVariables = $this->getListaVariables();
    	$lista = array();
    	if(is_array($listaVariables)){
    	
    	foreach ($listaVariables as $variable){
    			
    	   
    		if(strpos($texto,$variable['nombre']." ")!==false)
    			$lista[]= array($variable['nombre'], $variable['tipo']);
    			}
    		
    	}
    	 
    	
    	return $lista;
    	 
    	
    }
    
    /*
     * obtiene una lista de las variables en la cadena que se pasa en orden
    */
    public function getVariablesListaDelTextoTodo($texto = ''){
    	$listaVariables = $this->getListaVariables();
    	$lista = array();
    	if(is_array($listaVariables)){
    		 
    		foreach ($listaVariables as $variable){
    			 
    
    			if(strpos($texto,$variable['nombre']." ")!==false)
    				$lista[]= array($variable['nombre'], $variable['tipo'],$variable['rango'],base64_decode($variable['valor']));
    		}
    
    	}
    
    	 
    	return $lista;
    
    	 
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
        			
        			$funcionEvaluada = $this->evaluarFuncionTexto($funcion['valor'], $valores, $funcion['tipo'] , $funcion['rango'],$funcion['categoria'],$funcion['ruta']);
        			
        			
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
    
    
    public function evaluarFuncion($id = '',$valores=''){
    	$funcion = $this->getFuncion($id);
    	
    	
    	if(!is_array($funcion)) return false;
    	$cadena = base64_decode($funcion[0]['valor']);
    	$tipo = $funcion[0]['tipo'];
    	$rango = $funcion[0]['rango'];
    	$categoria = $funcion[0]['categoria'];
    	$ruta = base64_decode($funcion[0]['ruta']);
    	$restriccion = $funcion[0]['restriccion'];
    	
    	return $this->evaluarFuncionTexto($cadena, $valores, $tipo , $rango,$categoria,$ruta,$restriccion);
    }
    
    public function evaluarFuncionTexto($cadena = '', $valores = '', $tipo = '' , $rango = '',$categoria = '',$ruta = '',$restriccion=''){
    	
    	//1. Procesa los parametros y los Reemplaza
    		$cadena = $this->procesarParametros($cadena  );
    	
    	//2. Reemplaza las variables y las evalua
    	if(is_array($valores)){
    		$cadena = $this->procesarVariables($cadena , $valores );
    	}
    	
    	//revisa si hay funciones y las evalua nuevamente
    	//procesa las funciones internas
    	    $cadena =  $this->procesarFunciones($cadena);
    	
    	//3. Evalua toda la funcion
    	$valor = $this->evaluar($cadena,$categoria,$ruta,$tipo);
    	
    	if(!$valor) return $this->getErrorEvaluador();
    	
    	
    	
    	
    	//4. valida el tipo
    	if(@Tipos::validarTipo($valor,$tipo)&&@Rango::validarRango($valor , $tipo , $rango,$restriccion))  return (is_array($valor)||is_object($valor))?serialize($valor):$valor;
    	
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
    
    private function getFuncion($id=0){
    	 
    	$funcion  =  new GestorFuncion();
    	$listaFunciones = $funcion->consultarFuncion($id,'','','','','',1);
    	 
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
    
    private function getVariable($id=0){
    
    	$variable  =  new GestorVariable();
    	$listaVariables = $variable->consultarVariable($id,'','','','','',1);
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
    
    private function getParametro($id=0){
    
    	$parametro  =  new GestorParametro();
    	$listaParametro = $parametro->consultarParametro($id,'','','','','',1);
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
    	
    	$sentenciaNumero= 0;
    	$operador='';
    	$anteriorCadena = '';
    	$anteriorOperador = '';
    	foreach ($cadenaArray as $elemento){
    		
    		if(in_array($elemento , $this->listaOperadorLogico)){
    			$sentenciaNumero++;
    			
    			if($sentenciaNumero==1){
    				$resultado[] = array('',$cadena);
    				//$operador = $elemento;
    			}else $resultado[] = array($anteriorOperador,$cadena);
    			
    			$anteriorCadena= $cadena;
    			$anteriorOperador = $elemento;
    			$cadena = '';
    		}else	$cadena .= $elemento;
  	
    	}$resultado[] = array($anteriorOperador,$cadena);
    	
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
    	$valorRegla = base64_decode($datosRegla[0]['valor']);
    	
    	
    	
    	//0. Procesa Sentencias
    	$listaSentencias = $this->procesarSentencias($valorRegla);
    	$listaResultados = array();
        $cadenas =  array();
        $valorez =  array();
        $pasosSentencias =  array();
    	if(is_array($listaSentencias)){
    		
    		foreach ($listaSentencias as $sentencia){
                 
    			$operador = $sentencia[0];
    			$cadena = trim($sentencia[1]);
    			$cadenas[]=$cadena;
    			
    			//1. Procesa los parametros y los Reemplaza
    			$cadena = $this->procesarParametros($cadena);
    			$cadenas[]=$cadena;
    			//2. Reemplaza las variables y las evalua
    			if(is_array($valores)){
    				$cadena = $this->procesarVariables($cadena , $valores );
    				$cadenas[]=$cadena;
    			}
    			
    			//3. Reemplaza funciones y las evalua
    			$cadena = $this->procesarFunciones($cadena);
    			$cadenas[]=$cadena;
    			
    			//4. Evalua toda la regla
    			$valor = $this->evaluar($cadena);
    			//$valorez []= $valor;
    			$valor =  (bool) $valor;
    			$cadenas[]=$valor?'verdadero':'falso';
    			
    			$pasosSentencias[] = $cadenas;
    			$cadenas =  array();
    			if(Tipos::validarTipo($valor,$tipoRegla))  $listaResultados[] = array($operador,$valor)	;//return $valor;
    			
    			
    		}
    		$this->pasosEvaluacionSentenciasRegla = $pasosSentencias;
    		//return $this->procesarSentencias($valorRegla);
    		//return 100 == 100;
    		//return $this->procesarOperadoresComparacion('100 === 100');
    		//return array($cadenas,$listaResultados);//,$cadenas,$this->procesarFunciones('funcion1(66)'));
    		//return  $this->evaluarResultados($listaResultados);
    		return  array($this->evaluarResultados($listaResultados),$pasosSentencias,$listaResultados);
    		
    	}
    	    	 
    	 return false;
    	
    }

    public function evaluarReglaTexto($valorRegla = '', $valores = '',$tipoRegla = '', $idProceso = '' ){
    	 
    	 
    	 
    	//asigna datos de la regla
    	//$idRegla = $datosRegla[0]['id'];
    	//$nombreRegla = $datosRegla[0]['nombre'];
    	//$procesoRegla = $datosRegla[0]['proceso'];
    	//$tipoRegla = $datosRegla[0]['tipo'];
    	//$valorRegla = base64_decode($datosRegla[0]['valor']);
    	 
    	 
    	 
    	//0. Procesa Sentencias
    	$listaSentencias = $this->procesarSentencias($valorRegla);
    	$listaResultados = array();
    	$cadenas =  array();
    	$valorez =  array();
    	$pasosSentencias =  array();
    	if(is_array($listaSentencias)){
    
    		foreach ($listaSentencias as $sentencia){
    			 
    			$operador = $sentencia[0];
    			$cadena = trim($sentencia[1]);
    			$cadenas[]=$cadena;
    			 
    			//1. Procesa los parametros y los Reemplaza
    			$cadena = $this->procesarParametros($cadena);
    			$cadenas[]=$cadena;
    			//2. Reemplaza las variables y las evalua
    			if(is_array($valores)){
    				$cadena = $this->procesarVariables($cadena , $valores );
    				$cadenas[]=$cadena;
    			}
    			 
    			//3. Reemplaza funciones y las evalua
    			$cadena = $this->procesarFunciones($cadena);
    			$cadenas[]=$cadena;
    			 
    			//4. Evalua toda la regla
    			$valor = $this->evaluar($cadena);
    			//$valorez []= $valor;
    			$valor =  (bool) $valor;
    			$cadenas[]=$valor?'verdadero':'falso';
    			 
    			$pasosSentencias[] = $cadenas;
    			$cadenas =  array();
    			if(Tipos::validarTipo($valor,$tipoRegla))  $listaResultados[] = array($operador,$valor)	;//return $valor;
    			 
    			 
    		}
    		$this->pasosEvaluacionSentenciasRegla = $pasosSentencias;
    		//return $this->procesarSentencias($valorRegla);
    		//return 100 == 100;
    		//return $this->procesarOperadoresComparacion('100 === 100');
    		//return array($cadenas,$listaResultados);//,$cadenas,$this->procesarFunciones('funcion1(66)'));
    		//return  $this->evaluarResultados($listaResultados);
    		return  array($this->evaluarResultados($listaResultados),$pasosSentencias,$listaResultados);
    
    	}
    	 
    	return false;
    	 
    }
    
    
    public function getPasosCalculoRegla(){
    	return $this->pasosEvaluacionSentenciasRegla;
    }
    
        


}

?>



