<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */

if($_REQUEST['modulo']==51||$_REQUEST['modulo']==52){
	echo '<div style="text-align: center"><p><b>';
	echo "<br><br>para la legalización de la matricula en el proyecto curricular debe realizar la solicitud del certificado de desembolso en tesorería (7 piso)   y entregar los siguientes documentos:";
	echo "<br>   *. Carta de solicitud de certificado de desembolso dirigida a tesorería general.";
	echo "<br>   *. Recibo de pago 2014-1 ";
	echo "<br>   *. Resolución del Icetex la cual se entrega en Bienestar Institucional ";
	echo "<br>   *. Consignación Banco de Occidente, cuenta de ahorros No 23081461-8 cod 41 por valor de 5.100. ";
	echo "</b></p></div>";
	
}




