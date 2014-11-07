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

//Valores para el insert
$parametros = array();
$parametros['codigo'] = $_REQUEST['codigo'];
$parametros['cuentaICETEX'] = $_REQUEST['cuentaICETEX'];
$parametros['nitFacultad'] = $_REQUEST['nitFacultad'];
$parametros['cuentaFacultad'] = $_REQUEST['cuentaFacultad'];
$parametros['R3'] = $_REQUEST['R3'];
$parametros['R6'] = $_REQUEST['R6'];
$parametros['tipo'] = $_REQUEST['tipo'];
$parametros['numero'] = $_REQUEST['numero'];
$parametros['observaciones'] = $_REQUEST['observaciones'];
$parametros['anio'] = substr($_REQUEST['periodo'], 0, 4);
$parametros['per'] = substr($_REQUEST['periodo'], 5, 1);


if($_REQUEST['tipo']=='DEVOLUCION'){
	$this->estado = 9;
}elseif($_REQUEST['tipo']=='RECLASIFICACION'){
	$this->estado = 8;
}

//Revisa si existen recibos creados en el año y periodo en curso
$cadena_sql = $this->sql->cadena_sql("registroContable",$parametros);
$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);


if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorRegistroContable");
	echo "</b></p></div>";
	exit;
}





$_REQUEST['valorConsulta'] = $_REQUEST['codigo'];
$this->actualizarEstadoFlujo();

echo json_encode(true); 

