<?php 
namespace reglas\formulario;

include_once (dirname(__FILE__).'/../ClienteServicioReglas.class.php');
include_once (dirname(__FILE__).'/../Mensaje.class.php');

use reglas\ClienteServicioReglas as ClienteServicioReglas;
use reglas\Mensaje as Mensaje;

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
	    
    function __construct($lenguaje,$objetoId = '') {

    	$this->objetoId = $objetoId;
        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

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
	    $parametro = $this->listaParametros[0];
    	
    	    
    	
    	if(strtolower($this->objetoAliasSingular)=='parametro'){
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array($parametro);
    		
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		$cadenaMensaje .= $accion;
    		$cadenaMensaje .= '<br>';
    		$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    		echo $this->mensaje->getLastMensaje();
    		return true;
    	}
    	
    	if(strtolower($this->objetoAliasSingular)=='variable'){
    		//recupera datos
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array($parametro);
    		$elemento =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		
    		if(!is_array($elemento)){
    			return false;
    		}
    		
    		$accion = $this->getVariablesListaDelTexto($elemento[0]['nombre']);
    		
    		
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
    				$formularioVariable .='<div class="contenedorInput">';
    				$formularioVariable .='<div><label>'.$a[0].'</label>:<span style="white-space:pre;"> </span> </div><div><input class="ui-corner-all validate[required] " type="text" id="variable'.ucfirst($a[0]).'" name="variable'.ucfirst($a[0]).'">';
    				$formularioVariable .='</input></div>';
    				$formularioVariable .='</div><br>';
    			}
    			$formularioVariable .= '<br><br><input onclick="validarElemento()" type="button" value="'.$this->lenguaje->getCadena ( 'evaluar' ).'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only"></input>';
    			$formularioVariable .= '</form">';
    			
    		}
    	
    		if(in_array(1, $creaFormularioVariables)){
    			
    			echo $formularioVariable;
    			return false;
    		}
    		
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array($parametro,$variablesParametros[0][1]);
    		
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		if(!$accion) $cadenaMensaje .='falso';
    		else $cadenaMensaje .= $accion;
    		$cadenaMensaje .= '<br>';
    		$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    		echo $this->mensaje->getLastMensaje();
    		return true;
    	}
    	
    	if(strtolower($this->objetoAliasSingular)=='funcion'||strtolower($this->objetoAliasSingular)=='regla'){
    		
    		//recupera datos
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array($parametro);
    		$elemento =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    	
    		if(!is_array($elemento)){
    			return false;
    		}
    	
    		$accion = $this->getVariablesListaDelTexto(base64_decode($elemento[0]['valor']));
    	
    	
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
    				$formularioVariable .='<div class="contenedorInput">';
    				$formularioVariable .='<div><label>'.$a[0].'</label>:<span style="white-space:pre;"> </span> </div><div><input class="ui-corner-all validate[required] " type="text" id="variable'.ucfirst($a[0]).'" name="variable'.ucfirst($a[0]).'">';
    				$formularioVariable .='</input></div>';
    				$formularioVariable .='</div><br>';
    			}
    			$formularioVariable .= '<br><br><input onclick="validarElemento()" type="button" value="'.$this->lenguaje->getCadena ( 'evaluar' ).'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only"></input>';
    			$formularioVariable .= '</form">';
    			 
    		}
    		 
    		if(in_array(1, $creaFormularioVariables)){
    			 
    			echo $formularioVariable;
    			return false;
    		}
    	    
    		$metodo = "evaluar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array($parametro,$variablesParametros);
    	    
    		$accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		if(!$accion) $cadenaMensaje .='falso';
    		else $cadenaMensaje .=  is_bool($accion)?'verdadero':$accion;
    		
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
    	$metodo = 'getVariablesListaDelTexto';
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

$evaluar = new Evaluar ( $this->lenguaje,$objetoId );


$evaluar->evaluar ();
$evaluar->mensaje ();

?>