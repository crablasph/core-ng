<?php
namespace reglas;


if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");


include_once ("core/crypto/Encriptador.class.php");


include_once("GestorUsuariosComponentes.class.php");
include_once("Envoltura.class.php");



include_once (dirname(__FILE__)."/class/wsdl/class.phpwsdl.php");
use \PhpWsdl as PhpWsdl;


class MediadorReglas {
    
	
	const SERVICIOS = '\Envoltura';
	
	
    var $crypto;
    var $miFabricaSoap;
    var $miConfigurador;
    
    //Array de direcciones de clases a incluir
    var $definiciones;
    
    //Objetos 
    var $parametro;
    var $funcion;
    var $regla;
    var $variable;
    var $usuario;
   
    function __construct() {
    	$this->miConfigurador = \Configurador::singleton ();
    	$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
    	
    	$this->definiciones = Array(								
        				$this->ruta.'definiciones/Definiciones.php'
   
        		);
    
    }
    
	
	
    private function getEndPointURI() {
    	
    	$url=$this->miConfigurador->getVariableConfiguracion("host");
    	$url.=$this->miConfigurador->getVariableConfiguracion("site");
    	$url.="/index.php?";
    	$resultado = '';
    	
        foreach ($_REQUEST as $n=>$v){
        	if(strtolower($n)!='action'&&strtolower($n)!='pagina'&&strtolower($n)!='wsdl')
        		$resultado.=$n.'='.$v.'&';
        		
        }
     
        $bloqueNombre = $this->miConfigurador->getVariableConfiguracion ( "bloqueActualNombre" );
        $bloqueGrupo = $this->miConfigurador->getVariableConfiguracion ( "bloqueActualGrupo" );
            //variale wsSoap
            $resultado.="&wsSoap";
        	
            $resultado.="pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
        	$resultado.="&procesarAjax=true";
        	$resultado.="&action=index.php";
        	$resultado.="&bloqueNombre=".$bloqueNombre;
        	$resultado.="&bloqueGrupo=".$bloqueGrupo;
        	 
        	$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");
        	$cadena=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($resultado,$enlace);
        	
        return array($url.$cadena,$url.$resultado);
    }
    
    
    function action() {
    	
        $this->soap=PhpWsdl::CreateInstance(
        		(string) __NAMESPACE__,								// PhpWsdl will determine a good namespace
           		$this->getEndPointURI()[0],								// Change this to your SOAP endpoint URI (or keep it NULL and PhpWsdl will determine it)
        		'./blocks/reglas/reglasServicio/cache',							// Change this to a folder with write access
        		$this->definiciones,
        		(string) __NAMESPACE__.self::SERVICIOS,								// The name of the class that serves the webservice will be determined by PhpWsdl
           		null,								// This demo contains all method definitions in comments
        		null,								// This demo contains all complex types in comments
        		false,								// Don't send WSDL right now
        		false);								// Don't start the SOAP server right now
         
        
        
        // Disable caching for demonstration
        ini_set('soap.wsdl_cache_enabled',0);	// Disable caching in PHP
        PhpWsdl::$CacheTime=0;					// Disable caching in PhpWsdl
         
        // Run the SOAP server
        if($this->soap->IsWsdlRequested())			// WSDL requested by the client?
        	$this->soap->Optimize=false;				// Don't optimize WSDL to send it human readable to the browser
        
        $this->soap->RunServer();
        
        
        
        return 0;
    
    }
    
    
    
    

}

?>
