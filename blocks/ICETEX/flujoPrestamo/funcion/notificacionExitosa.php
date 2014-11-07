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
echo "El correo Ha sido enviado Exitosamente<br>";
echo "<b>El estudiante tiene 15 dìas para solicitar reintegro</b>";
echo "</div><br>";




