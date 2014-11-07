<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */



echo '<div style="text-align: center;font-style: italic;font-size:1.2em">';
echo "¿El estudiante desea solicitar Reintegro?<br>";
echo '<input type= "button" onclick="registroReintegro('.$_REQUEST['valorConsulta'].',\''.$_REQUEST['periodo'].'\');" value="'.$this->lenguaje->getCadena("solicitarReintegro").'">';
echo "</div><br>";





