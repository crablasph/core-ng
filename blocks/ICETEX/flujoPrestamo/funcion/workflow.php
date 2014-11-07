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

if(!isset($_REQUEST['periodo'])){
    //consultar periodo actual
	$cadena_sqlD = $this->sql->cadena_sql("periodoActual",'');
	$regPeriodo = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");
	$_REQUEST["periodo"] = $regPeriodo[0]['PERIODO'];
}

$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);
        

//Busca el paso en el cual se encuentre y redirige a la pagina
$cadena_sqlD = $this->sql->cadena_sql("consultarEstadoFlujo",$parametros); 
$registrosD = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");



if($registrosD!=false){
	echo "<br>";
	echo '<table style="margin: 0 auto;">';
	echo "<tr>";
	echo "<td><b>".$this->lenguaje->getCadena("estadoProceso")."</b></td>";
	echo "<td><b>".$this->lenguaje->getCadena("fechaCreacion")."</b></td>";
	echo "<td><b>".$this->lenguaje->getCadena("fechaActualizacion")."</b></td>";
	echo "</tr>";
	echo "<tr>";
	foreach ($registrosD[0] as $idx => $val ){
		if(is_numeric($idx)&&$idx!=0)
			echo "<td>".$val."</td>";
	}
	echo "</tr>";
	echo "</table>";
	
	//Boton para consultar historicos
	
	echo "<br>";
	echo '<br><br><div style="text-align: center;">';
	echo '<input onclick="consultarHistorico('.$_REQUEST['valorConsulta'].',\''.$_REQUEST['periodo'].'\');" type="button" value="'.$this->lenguaje->getCadena("botonConsultarHistorico").'"></input>';
	echo '</div>';
	
	if($_REQUEST["modulo"]==51||$_REQUEST["modulo"]==52){
		echo '<div id="consultaResultado">';
		echo '<div id="resultadoUsuario">';
		echo "</div>";
		echo '<div id="resultadoCredito">';
		echo "</div>";
		echo "</div>";
		
	}
	
	//Revisa Permisos sobre los pasos
	$this->permisosWorkflow($registrosD[0][0]);
	
	//envia a la pagina correspondiente dependiendo el del paso

	switch($registrosD[0][0]){
		case 0:
			$this->mensajePendiente();
			if($_REQUEST['modulo']==51||$_REQUEST['modulo']==52) $this->crearSolicitudEstudiante();
			elseif ($_REQUEST['modulo']==68) $this->crearSolicitud();
			
			break;
		case 1:
			if($_REQUEST["modulo"]==51||$_REQUEST["modulo"]==52) $this->notificacionVerificar();
			elseif($_REQUEST["modulo"]==68) $this->aprobarSolicitud();
				
			break;
		case 2:
			
			$this->verificarPagoRecibo();
			$this->aprobarCredito();
			break;
		case 3:
			$this->mensajeResolucion(); 
			break;
		case 4:
			$this->creditoNegado();
			break;
		case 5:
			$this->notificacionExitosa();
			$this->revisarPagoMatricula();
			$this->solicitarReintegro();
			break;
		case 6:
			$this->notificacionError();
			$this->revisarPagoMatricula();
			$this->solicitarReintegro();
			break;
		case 7:
			$this->formularioContable();
			break;
		case 8:
			$this->mensajeReasignacion();
			$this->formularioContable();
			
			break;
		case 9:
			$this->mensajeReintegro();
			break;
		case 10:
			$this->mensajeLegalizado();
			break;
		default:
			break;
	}
	
	
	
}else {
	if($_REQUEST['modulo']==51||$_REQUEST['modulo']==52) $this->crearSolicitudEstudiante();
	elseif ($_REQUEST['modulo']==68) $this->crearSolicitud();
	else exit;

			
}