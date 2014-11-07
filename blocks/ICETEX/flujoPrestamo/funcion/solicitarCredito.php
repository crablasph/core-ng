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

//Asigna el estado por defecto al cual se va acambiar
$this->estado = 2;


//Revisa si existen recibos creados en el año y periodo en curso
$datoBusqueda['codigo'] = $_REQUEST['valorConsulta'];
if(!isset($_REQUEST['periodo'])){
    //consultar periodo actual
	$cadena_sqlD = $this->sql->cadena_sql("periodoActual",'');
	$regPeriodo = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");
	$_REQUEST["periodo"] = $regPeriodo[0]['PERIODO'];
}

$datoBusqueda['anio'] = substr($_REQUEST['periodo'], 0, 4);
$datoBusqueda['per'] = substr($_REQUEST['periodo'], 5, 1);

$cadena_sql = $this->sql->cadena_sql("consultarRecibosCreados",$datoBusqueda);

$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

if($registros==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoRecibo");
	echo "</b></p></div>";
	exit;
}

//Revisa si algun recibo se ha pagado
$validaPago = false;
foreach ($registros as $reg){
	if($reg[1]=='S') $validaPago = true;
}




//Si se pago alguno el estado a actualizar es el 2
if($validaPago ==true) $this->estado = 2;

//Se obtienen los valores para clonar la fila
//
//1. se consulta la suma de las matriculas ordinarias y extra ordinarias+
$cadena_sqlS = $this->sql->cadena_sql("consultarValorMatricula",$datoBusqueda);
$registrosS = $esteRecursoDB->ejecutarAcceso($cadena_sqlS,"busqueda");

if($registrosS==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoConsulta");
	echo "</b></p></div>";
	exit;
}
$this->valorOrdinaria = $registrosS[0][0] ;
$this->valorExtraOrdinaria = $registrosS[0][1] ;  

//2. se obtienen las fechas

$cadena_sqlF = $this->sql->cadena_sql("consultarFechaMatricula",$_REQUEST['valorConsulta']);
$registrosF = $esteRecursoDB->ejecutarAcceso($cadena_sqlF,"busqueda");

if($registrosF==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoConsultaFechas");
	echo "</b></p></div>";
	exit;
}


$ford = explode('/',$registrosF[0][0]);
$fext = explode('/',$registrosF[0][1]);
$fechaOrdinaria = new DateTime($ford[2]."-".$ford[1]."-".$ford[0]);
$fechaOrdinariaN = new DateTime($ford[2]."-".$ford[1]."-".$ford[0]);
date_add($fechaOrdinariaN , date_interval_create_from_date_string('5 days'));
$fechaExtraOrdinaria = new DateTime($fext[2]."-".$fext[1]."-".$fext[0]);
$fechaExtraOrdinariaN = new DateTime($fext[2]."-".$fext[1]."-".$fext[0]);
date_add($fechaExtraOrdinariaN  , date_interval_create_from_date_string('5 days'));
$hoy = new DateTime('now');


//Verifica si la fecha de pago ordinaria es menor a hoy
$modulosEstudiantes = array(51,52);

if($fechaOrdinaria<$hoy&&in_array($_REQUEST["modulo"], $modulosEstudiantes)){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorFechaVencida");
	echo "</b></p></div>";
	exit;
}


if ($fechaOrdinaria<$hoy&&$_REQUEST["modulo"]==68){
	
	//La fecha no debe ser ni domingo ni festivo
	
	$fechaOrdinariaN  = new DateTime('now');
	date_add($fechaOrdinariaN  , date_interval_create_from_date_string('5 days'));
	$fechaExtraOrdinariaN = new DateTime('now');
	date_add($fechaExtraOrdinariaN  , date_interval_create_from_date_string('12 days'));
	$diaO = $fechaOrdinariaN->format('d');
	$mesO = $fechaOrdinariaN->format('m');
	$diaE = $fechaOrdinariaN->format('d');
	$mesE = $fechaOrdinariaN->format('m');
	
	//obtener los festivos y pasarlos a un array
	$cadena_sqlFes = $this->sql->cadena_sql("consultarFestivos",$_REQUEST['valorConsulta']);
	$registrosFes = $esteRecursoDB->ejecutarAcceso($cadena_sqlFes,"busqueda");
	
	if($registrosFes==false){
		echo '<div style="text-align: center"><p><b>';
		echo $this->lenguaje->getCadena("errorNoConsultaFestivos");
		echo "</b></p></div>";
		exit;
	}
	$listaFestivos = array();
	foreach ($registrosFes as $festivoA){
		array_push($listaFestivos,  DateTime::createFromFormat('j/m/Y', $festivoA[0])->format('d/m/Y'));
		
	}
		
	
	
	while( in_array(strtolower($fechaOrdinariaN->format('l')), array('sunday', 'saturday')) || in_array($fechaOrdinariaN->format('d/m/Y'), $listaFestivos))
	{
		echo $fechaOrdinariaN ->format('d/m/Y')."<br>";
		date_add($fechaOrdinariaN  , date_interval_create_from_date_string('1 days'));
	}
	
	while( in_array(strtolower($fechaExtraOrdinariaN->format('l')), array('sunday', 'saturday'))|| in_array($fechaOrdinariaN->format('d/m/Y'), $listaFestivos))
	{
		echo $fechaExtraOrdinariaN ->format('d/m/Y')."<br>";
		date_add($fechaExtraOrdinariaN  , date_interval_create_from_date_string('1 days'));
	}
	
	
	
	
	
	
}


//Es necesario tomar de la tabla parametrica los valores de sistematizacion y otros
$cadena_sqlRefS = $this->sql->cadena_sql("consultarReferenciasNoMatricula",$_REQUEST['valorConsulta']);

$registrosRefS = $esteRecursoDB->ejecutarAcceso($cadena_sqlRefS,"busqueda");

if($registrosRefS==false){
	
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoConsultaReferencias");
	echo "</b></p></div>";
	exit;
}


$this->referencias = $registrosRefS ;
$this->fechaOrdinaria = $fechaOrdinariaN ->format('d/m/Y');
$this->fechaExtraOrdinaria = $fechaExtraOrdinariaN ->format('d/m/Y');
$this->proyectoCurricular = $registrosF[0][2] ;
$this->anoPago = $registrosF[0][3] ;
$this->periodoPago = $registrosF[0][4] ;

//Desactiva recibos actuales
$this->desactivarRecibosActuales();

//Crear Recibos Nuevos separados matricula y recibo de pago
$this->separarMatricula();

//Actualiza Estado del flujo
$this->actualizarEstadoFlujo();

echo json_encode(true);



