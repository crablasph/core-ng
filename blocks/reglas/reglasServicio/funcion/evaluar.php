<?php 
namespace reglas\formulario;

include_once (dirname(__FILE__).'/../ClienteServicioReglas.class.php');
include_once (dirname(__FILE__).'/../Mensaje.class.php');
include_once (dirname(__FILE__).'/../Tipos.class.php');

use reglas\ClienteServicioReglas as ClienteServicioReglas;
use reglas\Mensaje as Mensaje;
use reglas\Tipos as Tipos;

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Evaluar {

    var $miConfigurador;
    
    private $cliente;
    private $objeto;
    private $atributosObjeto;
    private $objetoId;
    private $objetoNombre;
    private $objetoAlias;
    private $mensaje;
    private $tipo;
    private $estado;
    private $permiso;
    private $categoria;
    private $objetoVisble;
    private $objetoCrear;
    private $objetoConsultar;
    private $objetoActualizar;
    private $objetoCambiarEstado;
    private $Objetoduplicar;
    private $objetoEliminar;
    private $columnas;
    private $listaParametros;
    private $listaAtributosParametros;
    private $texto;
	    
    function __construct($lenguaje,$objetoId = '',$texto =  false) {

    	$this->objetoId = $objetoId;
        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->texto =  (bool) $texto;
        $this->lenguaje = $lenguaje;
        $this->mensaje = new Mensaje();
        $this->cliente  = new ClienteServicioReglas();
        $this->objeto = $this->cliente->getListaObjetos();
    }
    
    private function seleccionarObjeto(){
    	foreach ($this->objeto as $objeto){
    		if($objeto['id']==$this->objetoId){
    
    			$this->objetoNombre = $objeto['nombre'];
    			$this->objetoAlias = $objeto['alias'] 	;
    			$this->objetoAliasSingular = $objeto['ejecutar'];
    			 
    			$this->objetoVisble = $this->setBool($objeto['visible']);
    			$this->objetoCrear = $this->setBool($objeto['crear']);
    			$this->objetoConsultar = $this->setBool($objeto['consultar']);
    			$this->objetoActualizar = $this->setBool($objeto['actualizar']);
    			$this->objetoCambiarEstado = $this->setBool($objeto['cambiarestado']);
    			$this->objetoDuplicar = $this->setBool($objeto['duplicar']);
    			$this->objetoEliminar = $this->setBool($objeto['eliminar']);
    			 
    			return true;
    		}
    	}
    	return false;
    }
    
    private function determinarListaParametros(){
    	$nombreObjeto = 'selectedItems';
    	$this->listaParametros = array();
    	
    	$this->listaAtributosParametro = array();
    	if(isset($_REQUEST[$nombreObjeto])) $this->listaParametros = explode( ',', $_REQUEST[$nombreObjeto] );
    	
    	
    	
    	 
    }
    
    private function getAtributosObjeto($idObjeto = ''){
    
    	$metodo = 'getAtributosObjeto';
    	$argumentos =  array($idObjeto);
    
    	try {
    		$this->atributosObjeto =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    	}catch (\SoapFault $fault) {
    		$this->mensaje->addMensaje($fault->faultcode,":".$fault->faultstring,'information');
    		return false;
    	}
    
    	if(!is_array($this->atributosObjeto)) return false;
    	return true;
    	
    }
    
    private function setBool($valor = ''){
    	
    	if($valor=='t') return true;
    	return false;
    	
    }
    
    public function evaluar(){
    	
    	if(!$this->seleccionarObjeto()||!$this->getAtributosObjeto($this->objetoId)){
    		echo $this->mensaje->getLastMensaje();
    		return false;
    		
    		
    	}
    	
    	$this->determinarListaParametros();
	    $resultados  =  array();
	    $mensaje = 'accionEvaluar';
	    
	    $cadenaMensaje = $this->lenguaje->getCadena ( $mensaje );
	    if(isset($this->listaParametros[0]))$parametro = $this->listaParametros[0];
    	
    	    
	    
    	if(strtolower($this->objetoAliasSingular)=='parametro'){
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		
    		if($this->texto===true){
    			$metodo.='Texto';
    			$argumentos = array();
    			$argumentos[] = base64_decode($_REQUEST['valorCodificado']);
    			$argumentos[] = $_REQUEST['tipo'];
    			
    		}else 		$argumentos =  array($parametro);
    		
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		
    		$cadenaMensaje .= !$accion?'falso':(string) $accion;
    		$cadenaMensaje .= '<br>';
    		$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    		
    		echo $this->mensaje->getLastMensaje();
    		return true;
    	}
    	
    	if(strtolower($this->objetoAliasSingular)=='variable'){
    		//recupera datos
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    		if(isset($parametro)) $argumentos =  array($parametro);
    		
    		
    		$elemento =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		
    		if(!is_array($elemento)&&$this->texto===false){
    			return false;
    		}
    		//$accion = is_array($elemento)?$this->getVariablesListaDelTexto($elemento[0]['nombre']):$this->getVariablesListaDelTexto(base64_decode($_REQUEST['valorCodificado']));
    		$accion = $this->getVariablesListaDelTexto($elemento[0]['nombre']." ");
    		
    		
    		
    		$creaFormularioVariables =  array();
    		$variablesParametros =  array();
    		$formularioVariable = '';
    		if(is_array($accion)) {
    			$formularioVariable .= '<form id="formVariablesEvaluar">';
    			foreach ($accion as $a){
    				if(!isset($_REQUEST['variable'.ucfirst($a[0])])) {
    					$creaFormularioVariables[] =  1;
    				}else{
    					$variablesParametros[] =  array($a[0],$_REQUEST['variable'.ucfirst($a[0])]);
    				}
    				$formularioVariable .='<div title="tipo:'.Tipos::getTipoAlias($a[1]).' rango:'.$a[2].' valor:'.$a[3].' " class="contenedorInput">';
    				$formularioVariable .='<div  ><label>'.$a[0].'</label>:<span style="white-space:pre;"> </span> </div><div><input class="ui-corner-all validate[required] " type="text" id="variable'.ucfirst($a[0]).'" name="variable'.ucfirst($a[0]).'">';
    				$formularioVariable .='</input></div>';
    				$formularioVariable .='</div><br>';
    			}
    			$formularioVariable .= '<br><br><div id="botones"  class="marcoBotones"><input onclick="validarElemento()" type="button" value="'.$this->lenguaje->getCadena ( 'evaluar' ).'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only"></input></div>';
    			$formularioVariable .= '</form">';
    			
    		}
    		
    		
    		if(in_array(1, $creaFormularioVariables)&&$this->texto===false){
    			
    			echo $formularioVariable;
    			return false;
    		}
    		
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		if($this->texto===true){
    			$metodo.='Texto';
    			$argumentos = array();
    			$argumentos[] = base64_decode($_REQUEST['valorCodificado']);
    			
    			$argumentos[] = $_REQUEST['tipo'];
    			$argumentos[] = $_REQUEST['rango'];
    			
    		
    		}else	$argumentos =  @array($parametro,$variablesParametros[0][1]);
    		
    		
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		$cadenaMensaje .= !$accion?'falso':(string) $accion;
    		$cadenaMensaje .= '<br>';
    		
    		$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    		echo $this->mensaje->getLastMensaje();
    		return true;
    	}
    	
    	if(strtolower($this->objetoAliasSingular)=='funcion'||strtolower($this->objetoAliasSingular)=='regla'){
    		
    		//recupera datos
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    		if(isset($parametro)&&$this->texto===false) {
    			$argumentos =  array($parametro);
    			$elemento =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		}else $elemento=  false;
    		 
    		
    	
    		if(!is_array($elemento)&&$this->texto===false){
    			return false;
    		}
    	    
    		$accion = is_array($elemento)?$this->getVariablesListaDelTexto(base64_decode($elemento[0]['valor'])):$this->getVariablesListaDelTexto(base64_decode($_REQUEST['valorCodificado']));
    	
    		
    		$creaFormularioVariables =  array();
    		$variablesParametros =  array();
    		$formularioVariable = '';
    		if(is_array($accion)) {
    			$formularioVariable .= '<form id="formVariablesEvaluar">';
    			foreach ($accion as $a){
    				if(!isset($_REQUEST['variable'.ucfirst($a[0])])) {
    					$creaFormularioVariables[] =  1;
    				}else{
    					
    					$variablesParametros[] =  array($a[0],$_REQUEST['variable'.ucfirst($a[0])]);
    				}
    				$formularioVariable .='<div title="tipo:'.Tipos::getTipoAlias($a[1]).' rango:'.$a[2].' valor:'.$a[3].' " class="contenedorInput">';
    				$formularioVariable .='<div  ><label>'.$a[0].'</label>:<span style="white-space:pre;"> </span> </div><div><input class="ui-corner-all validate[required] " type="text" id="variable'.ucfirst($a[0]).'" name="variable'.ucfirst($a[0]).'">';
    				$formularioVariable .='</input></div>';
    				$formularioVariable .='</div><br>';
    			}
    			$formularioVariable .= '<br><br><br><br><br><br><div id="botones"  class=""><input onclick="';
    			if($this->texto==false)	$formularioVariable .= 'validarElemento()';
    			else $formularioVariable .= 'evaluarTexto()';
    			$formularioVariable .= '" type="button" value="'.$this->lenguaje->getCadena ( 'evaluar' ).'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only"></input></div>';
    			$formularioVariable .= '</form">';
    			 
    		}
    		 
    		if(in_array(1, $creaFormularioVariables)){
    			 
    			echo $formularioVariable;
    			return false;
    		}
    		
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		if($this->texto===true){
    			$metodo.='Texto';
    			$argumentos = array();
    			$argumentos = array();
    			$argumentos[] = base64_decode($_REQUEST['valorCodificado']);
    			$argumentos[] = $variablesParametros;
    			if(isset($_REQUEST['tipo']))$argumentos[] = $_REQUEST['tipo'];
    			else $argumentos[] =1;
    			if(isset($_REQUEST['rango'])) $argumentos[] = $_REQUEST['rango'];
    			if(isset($_REQUEST['categoria'])) $argumentos[] = $_REQUEST['categoria'];
    			if(isset($_REQUEST['rutaCodificado'])) $argumentos[] = base64_decode($_REQUEST['rutaCodificado']);
    			
    		
    		}else $argumentos =  array($parametro,$variablesParametros);
    	    
    		var_dump($argumentos);
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		
    		
    		
    		$pasos = '';
    		$conteoPasos =  0;
    		if(strtolower($this->objetoAliasSingular)=='regla') {
    			
    			$pasos .= $accion[0]?'verdadero':'falso';
    			$pasos .= '<br><br>';
    			//$cadenaMensaje .= serialize($accion[0]);
    			foreach ($accion[1] as $act){
    				$conteoPasos++;
    				$pasos .="Sentencia ".$conteoPasos.".<br>";
    				foreach ($act as $elemento){
    					$pasos .= $elemento."<br>";
    				}
    				$pasos .= "<br>";
    				
    			}
    			
    			$pasos .= "Final:<br>";
    			foreach ($accion[2] as $act){
    				$valorr = $act[1]?'verdadero':'falso';
    				$operadorr = (string) "".$act[0]."";
    				$pasos .= $operadorr." ".$valorr." ";
    				
    				
    			}
    			
    			$res = $accion[0]?'verdadero':'falso';;
    			$pasos .= "= ".$res;
    			$pasos .= "<br>";
    			
    			$cadenaMensaje .= $pasos;
    			
    		}
    		
    		if(strtolower($this->objetoAliasSingular)=='funcion'){
    			if(is_bool($accion)) $cadenaMensaje .= !$accion?'falso':'verdadero';
    			else $cadenaMensaje .= !$accion?'falso':(string) $accion;
    		}
    		
    		
    		
    		
    		$cadenaMensaje .= '<br>';
    		$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    		
    		echo $this->mensaje->getLastMensaje();
    		return true;
    	}
    	
    	
    	
    	$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    	echo $this->mensaje->getLastMensaje();
    	return true;
    }
    
    private function getVariablesListaDelTexto($texto=''){
    	$metodo = 'getVariablesListaDelTextoTodo';
    	$argumentos =  array($texto);
    	return   call_user_func_array(array($this->cliente , $metodo), $argumentos);
    	
    }
    
    
    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );

             
        }

        return true;

    }

}

$evaluar = new Evaluar ( $this->lenguaje,$objetoId,$texto );


$evaluar->evaluar ();
$evaluar->mensaje ();

?>