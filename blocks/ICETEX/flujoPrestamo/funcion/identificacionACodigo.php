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

//Asigna Variables
$parametros = array();
$parametros['identificacion'] = $identificacion;
$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);

$cadena_sqlD = $this->sql->cadena_sql("consultarCodigoIdentificacion",$parametros);

$registrosD = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");

if(!is_null($registrosD)){

	$codigo = $registrosD[0][0];
}

$codigo = 0;










