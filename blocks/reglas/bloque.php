<?php

namespace reglas\reglasServicio;

// Evitar un acceso directo a este archivo
if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

// Todo bloque debe implementar la interfaz Bloque
include_once ("core/builder/Bloque.interface.php");
include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/FormularioHtml.class.php");


include("MediadorReglas.class.php");




// Esta clase actua como control del bloque en un patron FCE

if (! class_exists ( '\\reglas\\reglasServicio\\Bloque' )) {
    
    class Bloque {
        
    	private $nombreBloque;
        private $miConfigurador;
        private $mediador; 
        
        

        
        public function __construct($esteBloque, $lenguaje = "") {
            
			
        	$_REQUEST["bloqueNombre"] = $esteBloque['nombre'];
        	$_REQUEST["bloqueGrupo"] =  $esteBloque['grupo'];
			
			
            // El objeto de la clase Configurador debe ser único en toda la aplicación
            $this->miConfigurador = \Configurador::singleton ();
            
            $ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
            $rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
            
            if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
                $ruta .= "/blocks/" . $esteBloque ["nombre"] . "/";
                $rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
            } else {
                $ruta .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
                $rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
            }
            
            $this->miConfigurador->setVariableConfiguracion ( "rutaBloque", $ruta );
            $this->miConfigurador->setVariableConfiguracion ( "rutaUrlBloque", $rutaURL );
            
            $this->mediador =  New MediadorReglas();
            

        
        }

        public function bloque() {
        	
        	
               $this->mediador->action();
                    
        }
    }
}
// @ Crear un objeto bloque especifico
// El arreglo $unBloque está definido en el objeto de la clase ArmadorPagina o en la clase ProcesadorPagina

if (isset ( $_REQUEST ["procesarAjax"] )) {
    $unBloque ["nombre"] = $_REQUEST ["bloqueNombre"];
    $unBloque ["grupo"] = $_REQUEST ["bloqueGrupo"];
}

$this->miConfigurador->setVariableConfiguracion ( "esteBloque", $unBloque );

if (isset ( $lenguaje )) {
    $esteBloque = new Bloque ( $unBloque, $lenguaje );
} else {
    $esteBloque = new Bloque ( $unBloque );
}

$esteBloque->bloque ();

?>
