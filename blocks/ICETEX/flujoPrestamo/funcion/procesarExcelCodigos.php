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
		$lastRow = $objPHPExcelReader->getActiveSheet()->getHighestRow();
		for ($i=2;$i<=$lastRow;$i++){
			
			//cedula
			$celdaA = trim($objPHPExcelReader->getActiveSheet()->getCell('E'.$i)->getValue());
			//valor individual
			$celdaB = trim($objPHPExcelReader->getActiveSheet()->getCell('H'.$i)->getValue());
			//Resolucion
			$celdaK = trim($objPHPExcelReader->getActiveSheet()->getCell('K'.$i)->getValue());
			//modalidad resolucion
			$celdaC = trim($objPHPExcelReader->getActiveSheet()->getCell('C'.$i)->getValue());
			//Fecha que llego la resolucion
			$celdaI = trim($objPHPExcelReader->getActiveSheet()->getCell('I'.$i)->getValue());
			
			$celdaK  = trim($celdaK);
			
			if(trim($celdaA)!='0' &&!is_null($celdaA)&&$celdaK==$_REQUEST["resolucion"]){
				$celdaA = $this->identificacionACodigo($celdaA);
				$arr = array($celdaA,$celdaB,$celdaC,$celdaI);
			
			
				
				array_push($this->listadoCodigos,$arr);
			}
				
			
		}
		
	}
}else {
	echo "documento Invalido";
	$this->rutaArchivo =false;
	exit;
}







