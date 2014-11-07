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
echo "El correo No ha podido enviarse, por lo tanto el estudiante no ha sido notificado";
echo "</div><br>";




