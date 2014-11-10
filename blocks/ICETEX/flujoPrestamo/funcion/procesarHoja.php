<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}


$workSheet = $objPHPExcelReader->setActiveSheetIndexByName($indexName);
$lastRow = $workSheet->getHighestRow();

for ($i=2;$i<=$lastRow;$i++){
		
	//cedula
	$celdaA = trim($workSheet->getCell('E'.$i)->getValue());
	//valor individual
	$celdaB = trim($workSheet->getCell('H'.$i)->getValue());
	//Resolucion
	$celdaK = trim($workSheet->getCell('K'.$i)->getValue());
	//modalidad resolucion
	$celdaC = trim($workSheet->getCell('C'.$i)->getValue());
	//Fecha que llego la resolucion
	$celdaI = trim($workSheet->getCell('I'.$i)->getFormattedValue());
		
	$celdaK  = trim($celdaK);
		
	if($celdaA!='0' &&!is_null($celdaA)&&$celdaK==$_REQUEST["resolucion"]){
		$codigo = $this->identificacionACodigo($celdaA);
		
		$arr = array($codigo,$celdaB,$celdaC,$celdaI,$celdaA);
			
			

		array_push($this->listadoCodigos,$arr);
	}

		
}




