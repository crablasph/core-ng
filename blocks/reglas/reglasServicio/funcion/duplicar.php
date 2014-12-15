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


class Duplicar {

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
    
    public function duplicar(){
    	
    	if(!$this->seleccionarObjeto()||!$this->objetoDuplicar||!$this->getAtributosObjeto($this->objetoId)){
    		echo $this->mensaje->getLastMensaje();
    		return false;
    		
    		
    	}
    	
    	$this->determinarListaParametros();
	    $resultados  =  array();
	    $mensaje = 'accionDuplicar';
	    
	    $cadenaMensaje = $this->lenguaje->getCadena ( $mensaje );
	    
    	foreach ($this->listaParametros as $parametro){
    	    $metodo = "duplicar".ucfirst($this->objetoAliasSingular);
	    	$argumentos =  array($parametro);
	    	$accion =  false;
	    	try {
	    	   $accion =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
	    	}catch (\SoapFault $fault) {
	    		$this->mensaje->addMensaje($fault->faultcode,":".$fault->faultstring,'information');
	    		$accion =  false;
	    	}
	    	$resultados []= array($parametro, $accion);
	    	$cadenaMensaje .= 'id Elemento '.$parametro." crea ".$accion;
	    	if(!$accion) $cadenaMensaje .= ' <span style="color=red;">FALLO</span>';
	    	else $cadenaMensaje .= ' <span style="color=green;">EXITO</span>';
	    	$cadenaMensaje .= '<br>';
    	}
    	
    	
    	
    	$this->mensaje->addMensaje('2001',":".$cadenaMensaje,'information');
    	echo $this->mensaje->getLastMensaje();
    	return true;
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

$duplicar = new Duplicar ( $this->lenguaje,$objetoId );


$duplicar->duplicar ();
$duplicar->mensaje ();

?>