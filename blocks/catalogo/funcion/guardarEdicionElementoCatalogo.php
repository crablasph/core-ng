<?php 
namespace arka\catalogo\guardarEdicionElementoCatalogo;



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

    function __construct($lenguaje, $formulario , $sql) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->sql = $sql;
        
        $conexion = "catalogo";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }
    
    public function validarEntrada(){
    	
    	//validar request nombre
    	if(!isset($_REQUEST['nombreElemento'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorNombre' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['nombreElemento'])>50){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorLargoNombre' );
    		$this->mensaje();
    		exit;
    	}
    	 
    	//validar request idCatalogo
    	if(!isset($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorId' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['idCatalogo'])>50||!is_numeric($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorValId' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//validar request id
    	if(!isset($_REQUEST['id'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorIdE' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['id'])>50||!is_numeric($_REQUEST['id'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorValId' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//validar request idPadre
    	if(!isset($_REQUEST['idPadre'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorIdP' );
    		$this->mensaje();
    		exit;
    	}
    	
    	if(strlen($_REQUEST['idPadre'])>50||!is_numeric($_REQUEST['idPadre'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorIdP' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//validar catalogo existente
    	$cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCatalogoExiste' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//Se debe consultar el registro y si cambia validar
    	/*
    	//id padre existe
    	$cadena_sql = $this->sql->getCadenaSql("buscarIdPadre",array($_REQUEST['idPadre'],$_REQUEST['idCatalogo']));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	if(!$registros&&$_REQUEST['idPadre']!=0){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorIdPadreExiste' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//id elemento no existe
    	$cadena_sql = $this->sql->getCadenaSql("buscarIdElemento",array($_REQUEST['id'],$_REQUEST['idPadre'],$_REQUEST['idCatalogo']));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	 echo $cadena_sql ;
    	var_dump($registros,$_REQUEST);
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorIdElementoExiste' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//nombre existe en nivel
    	$cadena_sql = $this->sql->getCadenaSql("buscarNombreElementoNivel",array($_REQUEST['id'],$_REQUEST['idPadre'],$_REQUEST['idCatalogo'],$_REQUEST['nombreElemento']));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	if(is_array($registros)){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorNombreElementoExiste' );
    		$this->mensaje();
    		exit;
    	}*/
    	
    	
    	 
    	
    }

    function guardarEdicionElementoCatalogo() {
		
    	
    	
    	$cadena_sql = $this->sql->getCadenaSql("guardarEdicionElementoCatalogo",array($_REQUEST['idPadre'],$_REQUEST['id'],$_REQUEST['idCatalogo'],$_REQUEST['nombreElemento'],$_REQUEST['idElementoEd']));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql);
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCreacion' );
    		$this->mensaje();
    		exit;
    	}
    	
    	//consultar elementos 
    	
    	/*
    	//buscarElemento Creado
    	$cadena_sql = $this->sql->getCadenaSql("buscarElementoId","");
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	 
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCatalogoExiste' );
    		$this->mensaje();
    		exit;
    	}
    	echo $registros[0][0];
    	  */
    	echo $_REQUEST['idCatalogo'];  	
    	 
		    	 
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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql );

$miFormulario->validarEntrada();
$miFormulario->guardarEdicionElementoCatalogo ();
$miFormulario->mensaje ();

?>