<?php 
namespace arka\catalogo\dibujarCatalogo;



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
    
    public function dibujarCatalogo(){
    	
    	//consultar elementos 
    	
    	
    	$base = $this->consultarElementosNivel(0);
    	
    	//Inicio Lista
    	echo "<br>";
    	echo '<div id = "arbol">';
    	echo "<br>";
    	if(!$base){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'catalogoVacio' );
    		$this->mensaje();
    		exit;
    	}else  	$this->dibujarLista($base);
    	echo "</div>";
    	
    	
    	
    }
    
    private function dibujarLista($base){
    	
    	if($base){
    		
	    	echo '<ul  style="list-style: none;">';
	    	foreach ($base as $b){
	    		
	    		$base2 = $this->consultarElementosNivel($b['elemento_id']);
	    		echo '<div class="cont contenedor'.$b['elemento_padre'].'" id="elemento'.$b['elemento_padre'].'" ';
	    		echo ">";
	    		echo '<li id="el'.$b['elemento_id'].'">';
	    		
	    		/////comienzo fila
	    		echo '<div class="listaCatalogo ui-state-default ui-corner-all">';
	    		echo '<div class="interno" >';
	    		if(isset($_REQUEST['editar'])&&$_REQUEST['editar']==true){
		    		//edicion
		    	
		    		echo '<a title="click para editar" style="color: transparent;" class="editarElemento2" onclick="editarElementoCatalogo('.$b['elemento_id'].','.$b['elemento_padre'].','.$b['elemento_codigo'].',\''.$b['elemento_nombre'].'\','.$b['elemento_catalogo'].')">e';
		    		echo "</a>";
		    		echo '<a title="click para eliminar" style="color: transparent;" class="eliminarElemento" onclick="eliminarElementoCatalogo('.$b['elemento_id'].','.$b['elemento_padre'].','.$b['elemento_codigo'].','.$b['elemento_catalogo'].')">e';
		    		echo "</a>";
		    		
	    		}
	    		if($base2){
		    		echo '<a title="click para contraer elementos" style="color: transparent;" class="disminuir" onclick="cambioHijos(\'contenedor'.$b['elemento_id'].'\',this)">+';
		    		echo "</a>";
	    		}
	    		echo "</div>";
	    		//expandir hijos
	    		echo '<div class="interno"   ';
	    		if(isset($_REQUEST['editar'])&&$_REQUEST['editar']==true){
	    			echo 'title="click para seleccionar el padre"';
	    		}
	    		
	    		
	    		echo ' onclick="accion(\'contenedor'.$b['elemento_id'].'\','.$b['elemento_codigo'].','.$b['elemento_id'].')"';
	    		//}		
	    		echo '>';
	    		echo $b['elemento_codigo'].". ".$b['elemento_nombre']." - ".$b['elemento_fecha_creacion'];
	    		echo "</div>";
	    		
	    		echo "</div>";
	    		///fin fila
	    		
	    
	    		
	    		if($base2)	$this->dibujarLista($base2);
	    
	    		echo "</li>";
	    		echo "</div>";
	    	}
	    	echo "</ul>";
    	}
    	return true;
    }
    
    private function consultarElementosNivel($nivel){
    	$cadena_sql = $this->sql->getCadenaSql("elementosNivel",array($_REQUEST['idCatalogo'],$nivel));
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	
    	return $registros;
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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql,$this );


$miFormulario->dibujarCatalogo ();
$miFormulario->mensaje ();

?>