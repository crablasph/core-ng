<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */

/** Include PHPExcel */
require_once dirname(__FILE__) .'/Classes/PHPExcel.php';
/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) .'/Classes/PHPExcel/IOFactory.php';

$conexion="icetex";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
	//Este se considera un error fatal
	exit;
}

$esteBloque['nombre']=$_REQUEST['bloqueNombre'];
$esteBloque['grupo']=$_REQUEST['bloqueGrupo'];


$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);




$upload_dir = $this->ruta."/uploads/";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$titulo="Tesoreria".uniqid().".xlsx";
// Set document properties
$objPHPExcel->getProperties()   ->setCreator("Universidad Distrital Oficina Asesora de Sistemas")
->setTitle($titulo)
->setSubject("Archivo Carga de Resolucion ICETEX")
->setDescription("Archivo resultado de cargar una resoluciona estudiantes en el proceso de ICETEX")
->setKeywords("estudiantes ICETEX cedulas OAS")
->setCategory("ICETEX");


$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Cédula');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'Nombre');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Código');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Proyecto Curricular');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Facultad');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Periodo');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'Código resolución');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Valor matricula');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'Valor resolución');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'diferencia');



$i=2;
foreach ($lista as $li){
	
	
		$parametros["codigo"] = $li['CODIGO'];
		//Consultar Datos
		$cadena_sqlS = $this->sql->cadena_sql("consultarDatosTotalesCredito",$parametros);
		$registro = $esteRecursoDB->ejecutarAcceso($cadena_sqlS,"busqueda");
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $registro[0][0]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $registro[0][1]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $registro[0][2]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $registro[0][3]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $registro[0][4]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $registro[0][5]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $registro[0][6]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $registro[0][7]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $registro[0][8]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $registro[0][9]);
	
	$i++;
}



//escribe el nuevo archivo
$rutaExcel = $upload_dir.$titulo;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($rutaExcel);









