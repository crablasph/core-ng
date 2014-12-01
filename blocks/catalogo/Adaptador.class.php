<?php

namespace reglas\reglasServicio;
use SoapClient;
use SoapFault;
use SimpleXMLElement;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");

include_once ('Lenguaje.class.php');

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion

class Adaptador {
    
    var $sql;
    var $funcion;
    public static $lenguaje;
    var $ruta;
    var $miConfigurador;
    var $error;
    var $miRecursoDB;
    var $crypto;
    
    var $wsdl;
    
    
    
    private function validarWsdl($wsdl){
    	
    	if($wsdl==null||$wsdl==""||!file_get_contents($wsdl)){
    		return false;
    	}
    	
    	try {
    		$sxe = new SimpleXMLElement(file_get_contents($wsdl));
    	}catch (Exeption  $e){
    		return false;
    	}
    	
    	return true;
    }
    
    private function validarNombre($wsdl,$nombre){
    	return true;
    }
    
    private function validarArgumentos($argumentos){
    
    	if(!is_array($argumentos))
    			return false;
    		
    	return true;
    }
    
    public static function peticion($wsdl,$nombre,$argumentos) {
    	
    	self::$lenguaje = new lenguaje();
    	
    	$errores[1] = self::$lenguaje->getCadena('errorWSDL');
    	$e = new SoapFault("1", $errores[1]);
    	if(!self::validarWsdl($wsdl))
    		return $e->getMessage();
    	
    	$errores[2] = self::$lenguaje->getCadena('errorNombre');
    	$e = new SoapFault("2", $errores[2]);
    	if(!self::validarNombre($wsdl,$nombre))
    		return $e->getMessage();
    	 
    	$errores[3] = self::$lenguaje->getCadena('errorArgumentos');
    	$e = new SoapFault("3", $errores[3]);
    	if(!self::validarArgumentos($argumentos))
    		return $e->getMessage();
    	 
    	
    	try{
    	
    		$sClient = new SoapClient($wsdl);
    		return call_user_func_array(array($sClient, $nombre), $argumentos);
    		
    			
    	} catch(SoapFault $e){
    		return $e;
    	}
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
        
        $url=$this->miConfigurador->getVariableConfiguracion("host");
        $url.=$this->miConfigurador->getVariableConfiguracion("site");
        $url.="/index.php?";
        
           
    
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
        $this->formulario = $formulario;
    }

}

?>
