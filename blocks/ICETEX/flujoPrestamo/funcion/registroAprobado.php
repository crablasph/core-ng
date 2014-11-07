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

//Asigna Varibles
$parametros = array();
$parametros['codigo'] = $_REQUEST['valorConsulta'];
$parametros['aprobado'] = $this->aprobado;



//Revisa si existen recibos creados en el año y periodo en curso
$cadena_sql = $this->sql->cadena_sql("aprobarCredito",$parametros);
$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);


if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoCancela");
	echo "</b></p></div>";
	exit;
}

if($this->aprobado =='S') $this->estado = 3;
else $this->estado = 4;

//Actualiza Estado del flujo
$this->actualizarEstadoFlujo();

echo json_encode(true);




