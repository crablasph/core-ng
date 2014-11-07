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
$datoBusqueda['codigo'] = $_REQUEST['valorConsulta'];
if(!isset($_REQUEST['periodo'])){
    //consultar periodo actual
	$cadena_sqlD = $this->sql->cadena_sql("periodoActual",'');
	$regPeriodo = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");
	$_REQUEST["periodo"] = $regPeriodo[0]['PERIODO'];
}

$datoBusqueda['anio'] = substr($_REQUEST['periodo'], 0, 4);
$datoBusqueda['per'] = substr($_REQUEST['periodo'], 5, 1);

//Revisa si existen recibos creados en el año y periodo en curso
$cadena_sql = $this->sql->cadena_sql("consultarRecibosCreados",$datoBusqueda);
$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
if($registros==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoRecibo");
	echo "</b></p></div>";
	exit;
}

//Revisa si algun recibo se ha pagado
$validaPago = false;
foreach ($registros as $reg){
	if($reg[1]=='S') $validaPago = true;
}

if($validaPago==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoReciboPagado");
	echo "</b></p></div>";
	exit;
	
}

$this->estado = 3;

//Actualiza Estado del flujo
$this->actualizarEstadoFlujo();

echo json_encode(true);


return true;






