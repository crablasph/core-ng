<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */



//Permisos modulo 68
$p68 = array(0,1,2,3,4,5,6,9);
//permisos modulo 109
$p109 = array(7,8,9);
//permisos modulo 51 y 52 estudiante
$pE = array(0,1,2,3,8);



switch ($_REQUEST['modulo']){
	case "68":
		if(!in_array($opt, $p68))
			exit;
		break;
	case "109":
		if(!in_array($opt, $p109))
			exit;
		break;
	case "51":
		if(!in_array($opt, $pE))
			exit;
		break;
	case "52":
		if(!in_array($opt, $pE))
			exit;
		break;
	default:
		exit;
		break;
}








