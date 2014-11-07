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
echo "No se registra resolución aun.<br>Es Necesario cargar una resolución asociada al estudiante primero";
echo "</div><br>";




