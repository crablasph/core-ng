<?php 
namespace arka\catalogo\eliminarCatalogo;



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
    var $miFuncion;

    function __construct($lenguaje, $formulario , $sql , $funcion) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->sql = $sql;
        
        //$this->miFuncion = $funcion;
        
        $conexion = "catalogo";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }

    function eliminar() {
		
    	
    	
    	//validar request idCatalogo
    	if(!isset($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorId' );
    		$this->miConfigurador->setVariableConfiguracion ( 'tipoMensaje','error' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['idCatalogo'])>50||!is_numeric($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorValId' );
    		$this->miConfigurador->setVariableConfiguracion ( 'tipoMensaje','error' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//validar catalogo existente
    	$cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	 
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCatalogoExiste' );
    		$this->miConfigurador->setVariableConfiguracion ( 'tipoMensaje','error' );
    		$this->mensaje();
    		exit;
    	}
    	
    	
    	$cadena_sql = $this->sql->getCadenaSql("listarElementosID",$_REQUEST['idCatalogo']);
    	
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorEliminar' );
    		$this->miConfigurador->setVariableConfiguracion ( 'tipoMensaje','error' );
    		$this->mensaje();
    		exit;
    	}
    	
    	$salida = array();
    	
    	foreach ($registros as $fila){
    		$salida[] = array("id"=>$fila['elemento_id'],"nombre"=>$fila['elemento_codigo'],"alias"=>$fila['elemento_codigo']);
    	}
    	
    	echo json_encode($salida);
    	return true;
    	
    	

    	$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'operacionExitosa' );
    	$this->mensaje();
    	
    	
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
            if( $tipoMensaje)  $atributos ["estilo"] = $tipoMensaje;
            else $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );

             
        }

        return true;

    }
    

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql,$this );


$miFormulario->eliminar ();
$miFormulario->mensaje ();

?>