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

//Asigna el estado por defecto al cual se va acambiar
$this->estado = 1;

//Actualiza Estado del flujo
$this->actualizarEstadoFlujo();

$tema='Solicitud de Crédito ICETEX';
$cuerpo ='El estudiante de código '.$_REQUEST['valorConsulta'].' Ha realizado una soliciud de credito ICETEX<br>';
$cuerpo .=' Verificar si tiene el credito aprobado y registrarlo en el sistema academico';
$temaRegistro = 'SOLICITUD CREDITO';
$this->notificarBienestar($cuerpo , $tema, '',$temaRegistro);

echo json_encode(true);



