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

//Asigna Variables
$parametros = array();
$parametros['codigo'] = $_REQUEST['valorConsulta'];
$parametros['estado'] = $this->estado;
$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);
$cadena_sqlD = $this->sql->cadena_sql("consultarEstadoFlujo",$parametros);

$registrosD = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");



if($registrosD!=false){
	//Actualiza el estado del flujo
	$cadena_sql = $this->sql->cadena_sql("actualizarFlujo",$parametros);
	
	$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
	if($registros!=false){
		//echo "error Actualizando Flujo";
		exit;
	}
}else{
	//crea registro en el flujo
	$cadena_sql = $this->sql->cadena_sql("creaFlujo",$parametros);
	$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
	if($registros!=false){
	
		exit;
	}
}




return true;




