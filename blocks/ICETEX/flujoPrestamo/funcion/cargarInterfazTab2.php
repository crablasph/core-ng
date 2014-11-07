<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */



switch ($_REQUEST['modulo']){
	case "68":
		$this->formularioResolucion();
		break;
	case "109":
		$this->formularioContable();
		break;
	case "51":
		
		break;
	case "52":
		
		break;
	default:
		exit;
		break;
}

exit;



