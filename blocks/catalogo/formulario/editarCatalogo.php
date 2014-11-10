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
    	echo '<input type="text" maxlength="" size="50" value="'.$this->arrayDatos[0][1].'" class="ui-widget ui-widget-content ui-corner-all  validate[required] " tabindex="1" name="nombreCatalogo" id="nombreCatalogo" title="'.$nombreTitulo.'">';
    	 
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
    	   	
    	
    	
    	echo  '<form id="catalogo" name="catalogo" action="index.php" method="post">';
    	$this->campoId();
    	
    	$this->campoPadre();
    	
    	
    	
    	
    	$this->campoNombre();
    	echo '<input id="idCatalogo" type="hidden" value="'.$_REQUEST['idCatalogo'].'" name="idCatalogo">';
    	echo '<input id="idReg" type="hidden" value="0" name="idReg">';
    	echo '<input id="idPadre" type="hidden" value="0" name="idPadre">';
    	 
    	echo '</fieldset>';
    	$this->edicionBotones();
    	 
    	
    	echo "</form>";
    	
    	
    	
    	
    	 
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