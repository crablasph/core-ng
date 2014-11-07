<?php 
namespace arka\catalogo\AgregarForm;



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
    
    function formulario(){
    	$nombre =  $this->lenguaje->getCadena ( 'nombreCatalogo' );
    	$nombreTitulo =  $this->lenguaje->getCadena ( 'nombreTitulo' );
    	$crear =  $this->lenguaje->getCadena ( 'crear' );
    	$crearTitulo =  $this->lenguaje->getCadena ( 'crearTitulo' );
    	$crearLabel =  $this->lenguaje->getCadena ( 'crearLabel' );
    	
    	echo '<form name="catalogo" action="index.php" method="post" id="catalogo">';
    	echo '<div id="agregar" class="marcoBotones">';
    	echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
    	
    	
    	echo '<div style="float:left; width:150px"><label for="nombre">'.$nombre.'</label><span style="white-space:pre;"> </span></div>';
    	echo '<input type="text" maxlength="" size="50" value="" class="ui-widget ui-widget-content ui-corner-all  validate[required] " tabindex="1" name="nombre" id="nombre" title="'.$nombreTitulo.'">';
    	
    	echo '</fieldset>';
    	echo '</div>';
    	
    	echo '<div id="botones"  class="marcoBotones">';
    	echo '<div class="campoBoton">';
    	echo '<button  onclick=" crearCatalogo()" type="button" tabindex="2" id="crearA" value="'.$crear.'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.$crear.'</button>';
        echo '<input type="hidden" value="false" id="crear" name="crear">';
    	echo '</div>';
    	echo '</div>';
    	
    }

    /*function formulario() {

    	
    	
    	// Rescatar los datos de este bloque
    	$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
    	
    	
    	$atributosGlobales ['campoSeguro'] = 'true';
    	$_REQUEST['tiempo']=time();
    	
    	// -------------------------------------------------------------------------------------------------
    	
    	// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
    	$esteCampo = $esteBloque ['nombre'];
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
    	$atributos ['tipoFormulario'] = '';
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'POST'
    	$atributos ['metodo'] = 'POST';
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
    	$atributos ['action'] = 'index.php';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
    	
    	// Si no se coloca, entonces toma el valor predeterminado.
    	$atributos ['estilo'] = '';
    	$atributos ['marco'] = true;
    	$tab = 1;
    	// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
    	
    	// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
    	$atributos ['tipoEtiqueta'] = 'inicio';
    	echo $this->miFormulario->formulario ( $atributos );
    	
    	// ---------------- SECCION: Controles del Formulario -----------------------------------------------
    	
    	// ------------------Division para los botones-------------------------
    	$atributos ["id"] = "agregar";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	 
    	
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'nombre';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required';
    	$atributos ['valor'] = '';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	
    	// ------------------Fin Division para los botones-------------------------
    	echo $this->miFormulario->division ( "fin" );
    	 
    	
    	// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
    	
    	// ------------------Division para los botones-------------------------
    	
    	$atributos ["id"] = "botones";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'crear';
    	$atributos ["id"] = $esteCampo;
    	$atributos ["tabIndex"] = $tab;
    	$atributos ["tipo"] = 'boton';
    	// submit: no se coloca si se desea un tipo button genérico
    	//$atributos ['submit'] = true;
    	$atributos ["estiloMarco"] = '';
    	$atributos ["estiloBoton"] = 'jqueryui';
    	// verificar: true para verificar el formulario antes de pasarlo al servidor.
    	$atributos ["verificar"] = 'true';
    	//$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
    	$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
    	$atributos ['onClick'] = 'crearCatalogo()';
    	$tab ++;
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	
    	
    	// ------------------Fin Division para los botones-------------------------
    	echo $this->miFormulario->division ( "fin" );
    	
    	// ------------------- SECCION: Paso de variables ------------------------------------------------
    	
    	// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
    	
    	// Paso 1: crear el listado de variables
    	
    	$valorCodificado = "action=" . $esteBloque ["nombre"];
    	$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
    	$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
    	$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
    	$valorCodificado .= "&opcion=registrarBloque";
    	
    	$atributos ['marco'] = true;
    	$atributos ['tipoEtiqueta'] = 'fin';
    	echo $this->miFormulario->formulario ( $atributos );
    	
    	return true;
    	 
		    	 
    }*/

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
    
    
    

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>