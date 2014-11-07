<?php 
namespace arka\catalogo\editarCatalogo;



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
    var $arrayElementos ;
    var $arrayDatos ;
    var $funcion;

    function __construct($lenguaje, $formulario , $sql, $funcion) {

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

    function formulario() {
		
    	//validar request idCatalogo
    	if(!isset($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorId' );
    		$this->mensaje();
    		exit;
    	}
    	   	
    	
    	
    	$this->consultarDatosCatalogo();
    	$this->principal();
    	//$this->consultarElementos();
    	//echo '<div id="arbol">';
    	$this->funcion->dibujarCatalogo();
    	//echo '</div>';
    	exit;
    	
    	 
    	
    	
    	    	 
    	 
    }
    
    private function consultarElementos(){
    	
    	$cadena_sql = $this->sql->getCadenaSql("listarElementos",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'catalogoVacio' );
    		$this->mensaje();
    		exit;
    	}
    	
    	$this->arrayElementos = $registros;
    	
    }
    
    private function consultarDatosCatalogo(){
    	
    	$cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	 
    	 
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'catalogoVacio' );
    		$this->mensaje();
    		exit;
    	}
    	 
    	$this->arrayDatos = $registros;
    }
    
    private function edicionNombreCatalogo(){
    	$nombre =  $this->lenguaje->getCadena ( 'nombreCatalogo' );
    	$nombreTitulo =  $this->lenguaje->getCadena ( 'nombreTitulo' );
    	$crear =  $this->lenguaje->getCadena ( 'cambiarNombre' );
    	$crearTitulo =  $this->lenguaje->getCadena ( 'cambiarNombreTitulo' );
    	 
    	echo '<form name="catalogo_1" action="index.php" method="post" id="catalogo_1">';
    	//echo '<div id="agregar" class="marcoBotones">';
    	echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
    	 
    	 
    	echo '<div style="float:left; width:150px"><label for="nombre">'.$nombre.'</label><span style="white-space:pre;"> </span></div>';
    	echo '<input type="text" maxlength="" size="50" value="" class="ui-widget ui-widget-content ui-corner-all  validate[required] " tabindex="1" name="nombre" id="nombre" title="'.$nombreTitulo.'">';
    	 
    	echo '</fieldset>';
    	//echo '</div>';
    	 
    	echo '<div id="botones"  class="marcoBotones">';
    	echo '<div class="campoBoton">';
    	echo '<button  onclick="cambiarNombreCatalogo()" type="button" tabindex="2" id="crearA" value="'.$crear.'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.$crear.'</button>';
    	
    	echo '</div>';
    	echo '</div>';
    	echo '</form>';
    }
    
    private function edicionBotones(){
    	
    	echo '<div id="botones" class="marcoBotones"> ';
    	echo '<div class="campoBoton">';
    	echo '<button "="" onclick=" agregarElementoCatalogo()" type="button" tabindex="2" id="agregarA"';
        echo 'value="Agregar Elemento" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">Agregar Elemento</button>';
        echo '</div><div class="campoBoton">';
        echo '<button "="" onclick=" reiniciarEdicion('.$_REQUEST['idCatalogo'].')" type="button" tabindex="3" id="reiniciarA"';
    	echo 'value="Reiniciar" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">Reiniciar</button>';
    	echo '</div>';
    	echo '</div>';
    	
    }
    
    private function campoId(){
    	
    	echo '<div class= "jqueryui  anchoColumna1">';
    	echo '<div style="float:left; width:150px"><label for="id">Identificador</label><span style="white-space:pre;"> </span></div>';
    	echo '<input type="text" maxlength="" size="50" value="" class="ui-widget ui-widget-content ui-corner-all';
        echo ' validate[required,number] " tabindex="2" name="id" id="id" title="Ingrese Identificador Elemento">';
    	echo '</div>';
    	
    }
    
    private function campoPadre(){
    	$idPadreTitulo =  $this->lenguaje->getCadena ( 'idPadreTitulo' );
    	$idPadre =  $this->lenguaje->getCadena ( 'idPadre' );
    	echo '<div class="jqueryui  anchoColumna1">';
    	echo '<div style="float:left; width:150px"><label for="idPadre">'.$idPadre.'</label></div>';
    	echo '<div style="float:left; width:536px;"><div  tabindex="3" size="50" value="0" name="lidPadre" id="lidPadre" title="'.$idPadreTitulo.'" class="ui-widget ui-widget-content ui-corner-all">0</div></div>';
    	echo '</div>';
    	
    }
    
    private function campoNombre(){
    	echo '<br>';
    	echo ' <div class="jqueryui  anchoColumna1">';
    	echo ' <div style="float:left; width:150px"><label for="nombreElemento">Nombre Elemento</label><span style="white-space:pre;"> </span></div>';
    	echo ' <input type="text" maxlength="" size="50" value="" class="ui-widget ui-widget-content';
        echo ' ui-corner-all  validate[required,onlyLetterNumber] " tabindex="4" name="nombreElemento" id="nombreElemento" title=" Ingrese el Nombre del Elemento">';
    	echo ' </div>';
    	
    }
    
    private function notaUso(){
    	echo ' <div class="jqueryui  anchoColumna1">';
    	echo "<p>";
    	echo "<b>Nota de Uso</b>: Para cambiar el Identificador Padre es necesario seleccionar un elemento del catalogo";
    	echo "</p>";
    	echo ' </div><br>';
    }
    
    
    
    
    
    
    
    
    
    private function  principal(){
    	
    	
    	// Rescatar los datos de este bloque
    	$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
    	
    	
    	$tab = 0;
    	
    	
    	$this->edicionNombreCatalogo();
    	
    	echo "<br>";
    	$this->notaUso();
    	
    	echo "<br>";
    	
    	echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
    	   	
    	/*// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
    	$esteCampo = $esteBloque ['nombre'];
    	$atributos ['action'] = 'index.php';
    	$atributos ['tipoFormulario'] = '';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipoEtiqueta'] = 'inicio';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['estilo'] = '';
    	$atributos ['marco'] = true;
    	
    	$atributos ['metodo'] = 'POST';
    	echo $this->miFormulario->formulario ( $atributos );
    	//exit;*/
    	
    	echo  '<form id="catalogo" name="catalogo" action="index.php" method="post">';
    	$this->campoId();
    	$this->campoPadre();
    	
    	// ---------------- SECCION: Controles del Formulario -----------------------------------------------
    	/*
    	// ------------------Division para los botones-------------------------
    	$atributos ["id"] = "agregar";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	 */
    	/*
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'id';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required,number';
    	$atributos ['valor'] = '';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	
    	
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	*/
    	/*
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'idPadre';
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
    	$atributos ['valor'] = '0';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	 
    	 
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	 */
    	
    	
    	/*
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'nombreElemento';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required,onlyLetterNumber';
    	$atributos ['valor'] = '';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	 */
    	
    	$this->campoNombre();
    	echo '<input id="idCatalogo" type="hidden" value="'.$_REQUEST['idCatalogo'].'" name="idCatalogo">';
    	echo '<input id="idReg" type="hidden" value="0" name="idReg">';
    	echo '</fieldset>';
    	$this->edicionBotones();
    	 
    	
    	echo "</form>";
    	
    	// ------------------Fin Division para los botones-------------------------
    	//echo $this->miFormulario->division ( "fin" );
    	 
    	
    	// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
    	
    	// ------------------Division para los botones-------------------------
    	/*
    	$atributos ["id"] = "botones";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'agregar';
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
    	$atributos ['onClick'] = 'agregarElementoCatalogo()';
    	$tab ++;
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	
    	
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'reiniciar';
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
    	$atributos ['onClick'] = 'reiniciarEdicion('.$_REQUEST['idCatalogo'].')';
    	$tab ++;
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	   
    	
    	// ------------------Fin Division para los botones-------------------------
    	echo $this->miFormulario->division ( "fin" );
    	*/
    	
    	/*
    	$atributos ["id"] = "idCatalogo"; // No cambiar este nombre
    	$atributos ["tipo"] = "hidden";
    	$atributos ['estilo'] = '';
    	$atributos ["obligatorio"] = false;
    	$atributos ['marco'] = true;
    	$atributos ["etiqueta"] = "";
    	$atributos ["valor"] = $_REQUEST['idCatalogo'];
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );

    	$atributos ["id"] = "idReg"; // No cambiar este nombre
    	$atributos ["tipo"] = "hidden";
    	$atributos ['estilo'] = '';
    	$atributos ["obligatorio"] = false;
    	$atributos ['marco'] = true;
    	$atributos ["etiqueta"] = "";
    	$atributos ["valor"] = 0;
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	*/
    	
    	
    	/*// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
    	// Se debe declarar el mismo atributo de marco con que se inició el formulario.
    	$atributos ['marco'] = true;
    	$atributos ['tipoEtiqueta'] = 'fin';
    	echo $this->miFormulario->formulario ( $atributos );*/
    	
    	
    	
    	 
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
    

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql, $this );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>