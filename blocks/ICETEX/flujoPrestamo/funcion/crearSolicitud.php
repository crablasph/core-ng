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





	
	echo '<div id="formularioSolicitarCredito" style="margin: 0 auto;text-align: center;"><h3>'.$this->lenguaje->getCadena("mensajeSolicitud").'<br>'.$datoBusqueda['anio'].'-'.$datoBusqueda['per'].'</h3>';
	echo "<br>";
	echo '<form  name ="crearSolicitud" id ="crearSolicitud">';
	echo '<input type="hidden" name="codigo" value = "'.$_REQUEST['valorConsulta'].'">';
	if($_REQUEST["modulo"]==51||$_REQUEST["modulo"]==52) echo '<input onclick="solicitarCreditoEstudiante('.$_REQUEST['valorConsulta'].',\''.$datoBusqueda['anio'].'-'.$datoBusqueda['per'].'\')" type="button" value="'.$this->lenguaje->getCadena("aceptarSolicitud").'"></input>';
	else echo '<input onclick="solicitarCredito('.$_REQUEST['valorConsulta'].')" type="button" value="'.$this->lenguaje->getCadena("aceptarSolicitud").'"></input>';
	echo "</form>";
	echo "</div>";

	
			
