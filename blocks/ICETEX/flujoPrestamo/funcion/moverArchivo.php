<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */


$esteBloque['nombre']=$_REQUEST['bloqueNombre'];
$esteBloque['grupo']=$_REQUEST['bloqueGrupo'];

$maxSize=10*1024*1024;
$upload_dir = $this->ruta."/uploads/";



if(!isset($_FILES["documentoResolucion"]) )		{
	echo $this->lenguaje->getCadena("errorDocumentoResolucion");
	exit;
}

$documentoResolucion =  "resolucionICETEX".uniqid().".pdf";

if (isset($_FILES["documentoResolucion"])&&$_FILES["documentoResolucion"]['size']<=$maxSize
	&&($_FILES["documentoResolucion"]['type']=="application/pdf")) {
	
	if ($_FILES["documentoResolucion"]["error"] > 0) {
		echo "Error: " . $_FILES["file"]["error"] . "<br>";
	} else {
		if(move_uploaded_file($_FILES["documentoResolucion"]["tmp_name"], $upload_dir . $documentoResolucion)){
			$ruta = $upload_dir ;
			
			$this->rutaArchivo =$ruta.$documentoResolucion; 
		}
		
	}
}else {
	echo "documento Invalido";
	$this->rutaArchivo =false;
	exit;
}






