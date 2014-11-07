<?php 
namespace arka\catalogo\formulario;



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

    function formulario() {

    	$textos[0]=$this->lenguaje->getCadena("listaAdicion");
    	$textos[1]=$this->lenguaje->getCadena("listaVer");
    	$textos[2]=$this->lenguaje->getCadena("listaEditar");
    	$textos[3]=$this->lenguaje->getCadena("listaNombre");
    	$textos[4]=$this->lenguaje->getCadena("listaFecha");
    	$textos[5]=$this->lenguaje->getCadena("listaEliminar");
    	$textos[6]=$this->lenguaje->getCadena("listaMenu");
    	$textos[7]=$this->lenguaje->getCadena("listaId");
    	$textos[8]=$this->lenguaje->getCadena("listaCasa");
    	$cadena_sql = $this->sql->getCadenaSql("listarCatalogos",'');
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	
    	
    	
    	$cadena1 = "<br><br><br><br><br><br><br>";
    	$cadena1 .= "<div id='espacioTrabajo'>";
    	
    	//menu 
    	$cadena1 .= "<div id='marcoMenu'>";
    	$cadena1 .= '<div  id="menu" class="menu2">';
    	//$cadena .= '<div class="menu" style="max-width: 80%;margin: 0 auto;color:green;text-align:center;">';
    	$cadena1 .= '<div  class="ui-state-default ui-corner-all botonMenu">';
    	$cadena1 .='<a style="text-align: center;" class="ui-icon ui-icon-plus" onclick="agregarElementoLista()"  title="'.$textos[0].'">+</a>';
    	$cadena1 .='</div>';
    	$cadena1 .= '<div  class="ui-state-default ui-corner-all botonMenu">';
    	$cadena1 .='<a style="text-align: center;" class="ui-icon ui-icon-home" onclick="irACasa()"  title="'.$textos[8].'">+</a>';
    	$cadena1 .='</div>';
    	//</div>';
    	//$cadena .='</div>';
    	$cadena1 .='</div>';
    	$cadena1 .='<hr >';
    	
    	$cadena1 .='<br>';
    	$cadena1 .= "<div id='marcoTrabajo'>";
    	echo $cadena1; 
    	
    	if(!$registros){
    		
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorLista' );
    		$this->mensaje();
    		echo "</div>";
    		exit;
    	}
    	
    	$cadena = '<table id="listadoCatalogo">';
    	$cadena .= "<thead>";
    	$cadena .= "<tr>";
    	$cadena .= "<th>".$textos[7]."</th>";
    	$cadena .= "<th>".$textos[3]."</th>";
    	$cadena .= "<th>".$textos[4]."</th>";
    	$cadena .= "<th>".$textos[6]."     </th>";
    	$cadena .= "</tr>";
    	$cadena .= "</thead>";
    	$cadena .= "<tfoot>";
    	$cadena .= "<tr>";
    	$cadena .= "<th>".$textos[7]."</th>";
    	$cadena .= "<th>".$textos[3]."</th>";
    	$cadena .= "<th>".$textos[4]."</th>";
    	$cadena .= "<th>".$textos[6]."      </th>";
    	$cadena .= "</tr>";
    	$cadena .= "</tfoot>";
    	//exit;
    	foreach ($registros as  $fila){
    		
    		//comienza fila
    		$cadena .= "<tr>";
    		
    		///formulario
    		$cadena .= '<form name="eliminarFormulario'.$fila[0].'" id="formulario'.$fila[0].'">';
    		$cadena .= '<input type="hidden" name="idDel" id="idDel" value="'.$fila[2].'"></input>';
    		$cadena .= '</form>';
    		
    		//Id
    		$cadena .= "<td>";
    		$cadena .= $fila[0];
    		$cadena .= "</td>";
    		
    		//Nombre
    		$cadena .= "<td>";
    		$cadena .= $fila[1];
    		$cadena .= "</td>";
    		
    		//Fecha Creacion
    		$cadena .= "<td>";
    		$cadena .= $fila[2];
    		$cadena .= "</td>";
    		
    		//Edicion
    		$cadena .= "<td>";
    		
    		$cadena .= '<div class="interno">';
    		$cadena .= '<div class="mostrarElemento" onclick="mostrarElementoLista('.$fila[0].')" id="el'.$fila[0].'" title="'.$textos[1].'">v</div>';
    		$cadena .= '<div class="editarElemento2"  onclick="editarElementoLista(this)" id="el'.$fila[0].'" title="'.$textos[2].'">e</div>';
    		$cadena .= '<div class="eliminarElemento" onclick="eliminarElementoLista(this)" id="el'.$fila[0].'" title="'.$textos[5].'">x</div>';
    		$cadena .='</div>';
    		
    		
    		$cadena .= "</td>";
    		
    		//termina fila
    		$cadena .= "</tr>";
    	
    		
    	}
    	
    	$cadena .= "</div>";
    	$cadena .= "</table>";
    	$cadena .= "<br>";
    	$cadena .='<div id="contenidoCatalogoListas">';
    	$cadena .='</div>';
    	$cadena .= "</div>";
    	
    	
    	
    	echo $cadena;
    	
    	 
    	 
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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>
