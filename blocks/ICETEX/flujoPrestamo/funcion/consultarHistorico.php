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
$parametros["codigo"] = $_REQUEST['valorConsulta'];
$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);

//consulta historico
$cadena_sqlD = $this->sql->cadena_sql("consultarHistorico",$parametros); 

$registrosD = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");

if($registrosD==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoConsulta");
	echo "</b></p></div>";
	exit;
}

$rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
$rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloqueNombre"] . "/";

//Muestra tabla de historico

echo '<div id="financiera" style="text-align: left;padding: 10px;">';

//Titulo
echo '<div style="text-align: center;padding: 10px;">';
echo "<h3>".$this->lenguaje->getCadena("tituloTablaHistorico")."</h3>";
echo '</div>';

$contador = 0;

foreach($registrosD as $reg){
	
	$contador ++;
	echo '<div style="text-align: center;padding: 10px;"><b>';
	echo $this->lenguaje->getCadena("numeroSolicitud").$contador;
	echo '</b></div>';
	echo '<table style="margin: 0 auto;">';
	
	
	//fecha creacion
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("fechaCreacion");
	echo "</td><td>".$reg[0];
	echo "</td></tr>";
	
	//numeor resolucion
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("resolucion");
	echo "</td><td>".$reg[1];
	echo "</td></tr>";
	
	//valor total
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("valorTotal");
	echo "</td><td>".$reg[2];
	echo "</td></tr>";
	
	//valor individual
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("valorIndividual");
	echo "</td><td>".$reg[3];
	echo "</td></tr>";
	
	//documento
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("documentoResolucion");
	echo '</td><td>';
	if($reg[4]!="")echo '<a href="'.$rutaURL."uploads/".basename($reg[4]).'">documento</a>';
	echo "</td></tr>";
	
	//reintegro
	echo "<tr><td>";
	echo $this->lenguaje->getCadena("devolucion");
	echo "</td><td>".$reg[5];
	echo "</td></tr>";
	
	
	
	
}


echo "</div>";


exit;






