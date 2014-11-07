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

if(!isset($_REQUEST['periodo'])){
	//consultar periodo actual
	$cadena_sqlD = $this->sql->cadena_sql("periodoActual",'');
	$regPeriodo = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");
	$_REQUEST["periodo"] = $regPeriodo[0]['PERIODO'];
}

//Revisa si existen recibos creados en el año y periodo en curso
$cadena_sql = $this->sql->cadena_sql("actualizaReintegro",$_REQUEST['valorConsulta']);

$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);


if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoRegistroReintegro");
	echo "</b></p></div>";
	exit;
}

$this->estado= 7;

$this->actualizarEstadoFlujo();

echo json_encode(true);
exit;




