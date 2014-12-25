<?php 
namespace reglas\formulario;

include_once (dirname(__FILE__).'/../ClienteServicioReglas.class.php');

use reglas\ClienteServicioReglas as ClienteServicioReglas;
use reglas\Mensaje as Mensaje;

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Formulario {

    var $miConfigurador;
    var $miFormulario;
    private $cliente;
    private $objetos;

    function __construct($lenguaje, $formulario) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;
        if(isset($_REQUEST['username'])) {
          $this->cliente  = new ClienteServicioReglas();
          $this->objetos = $this->cliente->getListaObjetos();
        } 
        

    }
    
    private function formularioUsuario(){
    	$textos[0] = strtolower($this->lenguaje->getCadena ('usuario'));
    	$url=$this->miConfigurador->getVariableConfiguracion("host");
    	$url.=$this->miConfigurador->getVariableConfiguracion("site");
    	$url.="/index.php?";
    	
    	$cadenaACodificar="bloqueNombre=". $this->miConfigurador->getVariableConfiguracion ( "bloqueActualNombre" );
    	$cadenaACodificar.="&bloqueGrupo=". $this->miConfigurador->getVariableConfiguracion ( "bloqueActualGrupo" );
    	$cadenaACodificar.="&pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
    	
    	//Codificar las variables
    	$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");
    	
    	$cadenaCod=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar,$enlace);
    	
    	$cadena ='<form id="formUsuario"  method="POST" name="formUsuario" action="'.$url.$cadenaCod.'">';
    	$cadena .='<fieldset>';
    	$cadena .='<div class="contenedorInput" >';
    	$cadena .='<div style="float:left; ">';
    	$cadena .= '<label for="'.$textos[0].'">';
    	$cadena .= ucfirst(utf8_encode($textos[0]));
    	$cadena .= '</label>';
    	$cadena .= '</div>';
    	$cadena .= '<input type="text" name="username" id="username" value="" placeholder="'.ucfirst($textos[0]).'" class="ui-corner-all">';
    	$cadena .= '</div>';
    	
    	$cadena .='</fieldset>';
    	$textos[0] = $this->lenguaje->getCadena ('enviar');
    	$cadena .= '<div id="botones"  class="marcoBotones">';
    	$cadena .= '<div class="campoBoton">';
    	$cadena .= '<input type="submit" value="Enviar">';
    	$cadena .= '</div>';
    	$cadena .= '</div>';
    	$cadena .='</form>';
    	echo $cadena;
    	
    }

    function formulario() {
    	
    	if(!isset($_REQUEST['username'])) {
    		$this->formularioUsuario();
    		return 0;
    	}
    	//Esqueleto interfaz
    	echo '<div id ="cabeza"  >';
    	
    	//barra de herramientas
    	echo  '<div id ="herramientas" class="ui-widget-header ui-corner-all" >';
    			
    	
    			//botones
		    	echo '<button id="consultar">'.$this->lenguaje->getCadena('principalConsultar').'</button>';
    	        echo '<button id="crear">'.$this->lenguaje->getCadena('principalCrear').'</button>';
    	        echo '<button id="editar">'.$this->lenguaje->getCadena('principalEditar').'</button>';
    	        echo '<button id="cambiarEstado">'.$this->lenguaje->getCadena('principalCambiarEstado').'</button>';
    	        echo '<button id="duplicar">'.$this->lenguaje->getCadena('principalDuplicar').'</button>';
    	        echo '<button id="validar">'.$this->lenguaje->getCadena('principalValidar').'</button>';
		    	
		    			
    	echo "</div>";
    	
    	
    	//barra de menu para acceder a reglas, funciones, variables, parametros
		echo  '<div id ="menu" class="ui-widget-header ui-corner-all">';
		     echo '<div class="posiscion">';
		      echo '<div>';
		      echo '<button  id="objetoSeleccionado">'.$this->objetos[0]['alias'].'</button>';
		      echo '<button  id="seleccionar">'.utf8_encode($this->lenguaje->getCadena('principalSeleccionarAccion')).'</button>';
			  echo '</div>';
			  echo '<ul id="menuLista">';
			  //Itera array objetos y los muestra
			  foreach ($this->objetos as $objeto){
			  	if($objeto['visible']=='t')
			  	echo '<li onclick="setObjeto('.$objeto['id'].',\''.$objeto['alias'].'\')">'.utf8_encode($objeto['alias']).'</li>';
			  }
			  
			  echo '</ul>';
			  echo '</div>';
		echo "</div>";
		
		//formulario de objeto
		echo '<form id="objetosFormulario">';
		echo '<input type="hidden" id ="objetoId" name="objetoId" value ="'.$this->objetos[0]['id'].'">';
		echo '</form>';
		
		//formulario de usuario
		echo '<form id="identificacionFormulario">';
		echo '<input type="hidden" id ="username" name="username" value ="'.$_REQUEST['username'].'">';
		echo '</form>';
		
		//formulario de seleccion
		echo '<form id="seleccionFormulario">';
		echo '<input type="hidden" id ="selectedItems" name="selectedItems" value ="">';
		echo '</form>';
		
		
		//fin encabezado
		echo "</div>";
        echo '<br><br><hr>';
		
		//cuerpo
    	echo '<div class="container-fluid" id="cuerpo">';
    	
    	
    	//define espacios para interacciones
    	
    	//mensaje
    	echo '<div id="espacioMensaje">';
    	echo '</div>';
    	
    	//espacio Trabajo
    	echo '<div  id="espacioTrabajo">';
    	
    	
    	
    	//fin espacio Trabajo
    	echo '</div>';
    	
    	//fun cuerpo
        echo "</div>";
    	
        
    	echo '<div id ="pies">';
    	
    	echo "</div>";
    	
      

    }

    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>