<?php 
namespace arka\catalogo\cambiarNombreCatalogo;





if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Formulario {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $sql;
    var $esteRecursoDB;
    var $funcion;
    var $editar;

    function __construct($lenguaje, $formulario , $sql , $funcion) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->sql = $sql;
        
        $this->funcion = $funcion;
        

        
        
        
        $conexion = "catalogo";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }

    function cambiarNombre() {
		
    	
    	
    	//validar request nombre
    	if(!isset($_REQUEST['nombreCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorNombre' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['nombreCatalogo'])>50){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorLargoNombre' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//validar nombre existente
    	$cadena_sql = $this->sql->getCadenaSql("buscarCatalogo",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	if(is_array($registros)){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorNombreExiste' );
    		$this->mensaje();
    		exit;
    	}
    	
    	$cadena_sql = $this->sql->getCadenaSql("cambiarNombreCatalogo",array($_REQUEST['nombreCatalogo'],$_REQUEST['idCatalogo']));
    	
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql);
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCambioNombre' );
    		$this->mensaje();
    		exit;
    	}

    	
		 $this->mensaje2('cambioNombre');
    	
    	 $this->funcion->dibujarCatalogo();
    	
    	exit;
    	
    	 
		    	 
    }

    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        //$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

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
    
    function mensaje2($mensaje) {
    
    	
    	
    
    				$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
    	
    		// -------------Control texto-----------------------
    		$esteCampo = 'divMensaje';
    		$atributos ['id'] = $esteCampo;
    		$atributos ["tamanno"] = '';
    		$atributos ["estilo"] = 'information';
    		$atributos ["etiqueta"] = '';
    		$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
    		echo $this->miFormulario->campoMensaje ( $atributos );
    		unset ( $atributos );
    
    		 
    	
    
    	return true;
    
    }
    
    

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql , $this);


$miFormulario->cambiarNombre ();
$miFormulario->mensaje ();

?>