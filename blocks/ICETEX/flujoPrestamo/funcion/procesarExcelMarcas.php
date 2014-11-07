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



if(!isset($_FILES["excelMarca"]) )		{
	echo $this->lenguaje->getCadena("errorExcelMarca");
	exit;
}

$this->listadoCodigos = array();

if (isset($_FILES["excelMarca"])&&$_FILES["excelMarca"]['size']<=$maxSize
	&&($_FILES["excelMarca"]['type']=="application/vnd.ms-excel"||$_FILES["excelMarca"]['type']=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")) {
	
	if ($_FILES["excelMarca"]["error"] > 0) {
		echo "Error: " . $_FILES["file"]["error"] . "<br>";
	} else {
		//if(move_uploaded_file($_FILES["excelMarca"]["tmp_name"], $upload_dir . $excelMarca)){
			 
		//}
		if (!file_exists($_FILES["excelMarca"]["tmp_name"])) {
			exit("Por favor ingrese un archivo valido." . EOL);
		}
		$objPHPExcelReader = PHPExcel_IOFactory::load($_FILES["excelMarca"]["tmp_name"]);
		$lastRow = $objPHPExcelReader->getActiveSheet()->getHighestRow();
		for ($i=2;$i<=$lastRow;$i++){
			$celdaA = $objPHPExcelReader->getActiveSheet()->getCell('A'.$i)->getValue();
			
			if(trim($celdaA)!='0' &&!is_null($celdaA)){
				$celdaA = $this->identificacionACodigo($celdaA);
			
				$arr = array($celdaA);
				array_push($this->listadoCodigos,$arr);
				
			}
			
			
		}
		
	}
}else {
	echo "documento Invalido";
	$this->rutaArchivo =false;
	exit;
}






