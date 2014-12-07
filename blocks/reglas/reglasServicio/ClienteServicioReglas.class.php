<?php
namespace reglas;
use \SoapClient as SoapClient;
if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}



include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");
include_once ("Envoltura.class.php");

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion

class ClienteServicioReglas {
	
	
    private $miConfigurador;
    private $clienteSoap;
    private $usuario;
    private $envoltura;
   
    
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
        
        
        $this->usuario = $_REQUEST['username'];
        
    }
    
    function __call($metodo, $argumentos){
    	
    
    	$this->envoltura =  New Envoltura();
    	
    	$_SERVER['HTTP_USUARIO'] =  $this->usuario;
    		
    	return call_user_func_array(array($this->envoltura , $metodo), $argumentos);
    	
    	
    	
    	
    	
    }
    
    
    
    
}

?>
