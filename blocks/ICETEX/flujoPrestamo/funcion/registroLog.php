<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */


$conexion="icetex";


$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
    //Este se considera un error fatal
    exit;
}

			
			$parametros["usuario"] = $_REQUEST["usuario"]."-".$_REQUEST["modulo"] ;
			$parametros["accion"] = $accion ;

				
			$cadena_sql = $this->sql->cadena_sql("registroLog",$parametros);
			
			$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
			
			
			if($registros!=false){
				echo '<div style="text-align: center"><p><b>';
				echo $this->lenguaje->getCadena("errorNoCreaLog");
				echo "</b></p></div>";

			}
			
			
			
			





