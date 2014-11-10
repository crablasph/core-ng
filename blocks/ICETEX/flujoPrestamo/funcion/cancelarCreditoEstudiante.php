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
$this->estado = 0;

//Actualiza Estado del flujo
$this->actualizarEstadoFlujo();

$tema='Solicitud de Crédito ICETEX';
$cuerpo ='Su solicitud de crédito del ICETEX, ha sido devuelta, por favor verifique el estado del crédito en la pagina del ICETEX<br> ';
$cuerpo .='adicionalmente verifique que cumpla todos los requisitos ante la universidad para hacer esta solicitud a través del sistema académico<br>';
$cuerpo .='para mayor información por favor comunicarse con <b>BIENESTAR INSTITUCIONAL</b>';
$temaRegistro = 'RECHAZO SOLICITUD';
$_REQUEST['codigo']=$_REQUEST['valorConsulta'];
$this->notificarEstudiante($cuerpo , $tema, '',$temaRegistro);

echo json_encode(true);



