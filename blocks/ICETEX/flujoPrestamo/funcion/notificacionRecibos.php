<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */


echo '<br><br><div style="text-align: center;font-style: italic;color:#FF0000;">';
echo "<br>Se han generado 2 recibos de pago<br>";
echo "<b>Es necesario Cancelar el recibo de pago sólamente con el seguro para continuar con el proceso</b>";
echo "</div><br>";




