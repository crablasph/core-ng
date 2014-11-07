<?php 
namespace arka\catalogo\eliminarElementoCatalogo;



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
    	    	
    	
    	
    	
    	
    	
    	 
    	
    }
    
    private function consultarElementosNivel($nivel){
    	$cadena_sql = $this->sql->getCadenaSql("elementosNivel",array($_REQUEST['idCatalogo'],$nivel));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	  
    	 
    	 
    	return $registros;
    }

    function eliminarElementoCatalogo() {
		
		//tiene hijos
    	
    	$consultar =  false;
    	$valores = array($_REQUEST['idReg']);
    	
    	do {
    		$nivel = $_REQUEST['idReg'];
    		$consultar = $this->consultarElementosNivel($nivel);
    		
	    	if(is_array($consultar)){
	    		foreach($consultar as $cons){
	    			array_push($valores,$cons['elemento_id']);
	    		}	
	    	}
	    		
    	}while (0);
    
    	foreach($valores as $val){

    		$cadena_sql = $this->sql->getCadenaSql("eliminarElementoCatalogo",$val);
    		$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql);
    		 
    		if(!$registros){
    			$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCreacion' );
    			$this->mensaje();
    			exit;
    		}
    	}
    	
    	
    	
    	
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
$miFormulario->eliminarElementoCatalogo ();
$miFormulario->mensaje ();

?>