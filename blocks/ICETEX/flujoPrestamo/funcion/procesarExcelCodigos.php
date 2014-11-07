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

$esteBloque['nombre']=$_REQUEST['bloqueNombre'];
$esteBloque['grupo']=$_REQUEST['bloqueGrupo'];

$maxSize=10*1024*1024;
$upload_dir = $this->ruta."/uploads/";



if(!isset($_FILES["excelResolucion"]) )		{
	echo $this->lenguaje->getCadena("errorExcelResolucion");
	exit;
}

$this->listadoCodigos = array();

if (isset($_FILES["excelResolucion"])&&$_FILES["excelResolucion"]['size']<=$maxSize
	&&($_FILES["excelResolucion"]['type']=="application/vnd.ms-excel"||$_FILES["excelResolucion"]['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")) {
	
	if ($_FILES["excelResolucion"]["error"] > 0) {
		echo "Error: " . $_FILES["file"]["error"] . "<br>";
	} else {
		//if(move_uploaded_file($_FILES["excelResolucion"]["tmp_name"], $upload_dir . $excelResolucion)){
			 
		//}
		if (!file_exists($_FILES["excelResolucion"]["tmp_name"])) {
			exit("Por favor ingrese un archivo valido." . EOL);
		}
		$objPHPExcelReader = PHPExcel_IOFactory::load($_FILES["excelResolucion"]["tmp_name"]);
		
		$permitidos = array('RENOVADOS','LEGALIZADOS');
		
		
		$this->procesarHoja($permitidos[0],$objPHPExcelReader);
		$this->procesarHoja($permitidos[1],$objPHPExcelReader);
		
		
		
		
	}
}else {
	echo "documento Invalido";
	$this->rutaArchivo =false;
	exit;
}







