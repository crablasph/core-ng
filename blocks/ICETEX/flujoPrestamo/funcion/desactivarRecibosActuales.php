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



//Revisa si existen recibos creados en el año y periodo en curso
$cadena_sql = $this->sql->cadena_sql("desactivarRecibos",$_REQUEST['valorConsulta']);
$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);


if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoDesactivar");
	echo "</b></p></div>";
	exit;
}
$this->registroLog('INACTIVAR RECIBO '.$_REQUEST['valorConsulta']);
return true;




