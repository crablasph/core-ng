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


class Autocompletar {

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
    private $proceso;
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
        $this->proceso = $this->cliente->getListaProcesos();
        
    }
    
    
    
    
    
    private function setBool($valor = ''){
    	if($valor=='t') return true;
    	return false;
    }
    
    private function seleccionarObjeto(){
    	$this->objeto = $this->cliente->getListaObjetos();
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
    
    private function getObjetoAliasPorId($objeto ='', $id = ''){
    	foreach ($this->$objeto as $elemento){
    		if($elemento['id']==$id) return $elemento['alias'];
    	}
    }
    
    private function getObjetoNombrePorId($objeto ='', $id = ''){
    	foreach ($this->$objeto as $elemento){
    		if($elemento['id']==$id) return $elemento['nombre'];
    	}
    }
    
    private function getListaPropiedad($nombre='anonimo'){

    	if($this->seleccionarObjeto()){
    		
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    		$argumentos =  array('','');
    		$peticion = call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		
    		if(!is_array($peticion)) return false;
    		
    		$resultado = array();
    		$indice = 0;
    		foreach ($peticion as $registro){
    			$indice++;
    			$resultado [] =  array('id'=>$registro[$nombre],'nombre'=>$registro[$nombre],'alias'=>$registro[$nombre]);
    		}
    		return $resultado; 
    		
    		
    	}
    	return false;
    }
    
    public function autocompletar(){
    	
    	$mensaje = 'accionAutocompletar';
    	
	    
    	if($_REQUEST['field']=='proceso') echo json_encode($this->proceso);
    	if($_REQUEST['field']=='nombre') echo json_encode($this->getListaPropiedad('nombre'));
    	if($_REQUEST['field']=='id') echo json_encode($this->getListaPropiedad('id'));
    	
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

$autocompletar = new Autocompletar ( $this->lenguaje,$objetoId );


$autocompletar->autocompletar ();
$autocompletar->mensaje ();

?>