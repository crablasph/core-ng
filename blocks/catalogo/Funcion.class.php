<?php
namespace arka\catalogo;
use SoapFault;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");







// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion

class Funcion {
    
    var $sql;
    var $funcion;
    var $lenguaje;
    var $ruta;
    var $miConfigurador;
    var $error;
    var $miRecursoDB;
    var $crypto;
    var $miFormulario;
    
   
    
    
    private function procesarAjax() {
        include_once ($this->ruta . "funcion/procesarAjax.php");
    }
    
    private function Agregar(){
    	
    	include_once ($this->ruta . "formulario/AgregarForm.php");
    }
    
    function form(){
    	 
    	include_once ($this->ruta . "formulario/form.php");
    }
    
    private function crearCatalogo(){
    	 
    	include_once ($this->ruta . "funcion/crearCatalogo.php");
    }
    
    private function eliminarCatalogo(){
    
    	include_once ($this->ruta . "funcion/eliminarCatalogo.php");
    }
    
    function editarCatalogo(){
    
    	include_once ($this->ruta . "formulario/editarCatalogo.php");
    }
    
    private function agregarElementoCatalogo(){
    	include_once ($this->ruta . "funcion/agregarElementoCatalogo.php");
    }
    
    private function guardarEdicionElementoCatalogo(){
    	include_once ($this->ruta . "funcion/guardarEdicionElementoCatalogo.php");
    }
    
    public function dibujarCatalogo(){
    	include_once ($this->ruta . "formulario/dibujarCatalogo.php");
    }
    
    private function cambiarNombreCatalogo(){
    	include_once ($this->ruta . "funcion/cambiarNombreCatalogo.php");
    }
    
    private function eliminarElementoCatalogo(){
    	include_once ($this->ruta . "funcion/eliminarElementoCatalogo.php");
    }
    
    
    function action() {
        
        // Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
        // aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
        // se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
        // en la carpeta funcion
        
        //Variables
        
        
		if(isset($_REQUEST['funcion'])){
			switch($_REQUEST['funcion']){
				case 'agregar':
					
					$this->agregar();
					
					
					break;
				case "crearCatalogo":
					$this->crearCatalogo();
				case "eliminarCatalogo":
					$this->eliminarCatalogo();
					break;
				case "eliminarElementoCatalogo":
					
					$this->eliminarElementoCatalogo();
					break;
				case "editarCatalogo":
					$_REQUEST['editar']=true;
					$this->editarCatalogo();
					break;
					
				case "mostrarCatalogo":
					
						$this->dibujarCatalogo();
						break;
				case "agregarElementoCatalogo":
					$this->agregarElementoCatalogo();
					break;
					
				case "guardarEdicionElementoCatalogo":
					$this->guardarEdicionElementoCatalogo();
					break;
					
				case "cambiarNombreCatalogo":
					$this->cambiarNombreCatalogo();
					break;
				default:
					break;
			}
		}
               
        return 0;
    
    }
    
    function __construct() {
    	

        
        $this->miConfigurador = \Configurador::singleton ();
        
        $this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
        
        $this->miMensaje = \Mensaje::singleton ();
        
        $conexion = "aplicativo";
        $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        
        if (! $this->miRecursoDB) {
            
            $this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
            $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        }
        
         
        
    
    }
    
    public function setVars(){
    	$this->miParametro= new Parametro();
    	$this->miParametro->setSql ( $this->sql );
    	$this->miParametro->setLenguaje ( $this->lenguaje );
    }
    
    public function setRuta($unaRuta) {
        $this->ruta = $unaRuta;
    }
    
    function setSql($a) {
        $this->sql = $a;
    }
    
    function setFuncion($funcion) {
        $this->funcion = $funcion;
    }
    
    public function setLenguaje($lenguaje) {
        $this->lenguaje = $lenguaje;
    }
    
    public function setFormulario($formulario) {
        $this->miFormulario = $formulario;
    }

}

?>
