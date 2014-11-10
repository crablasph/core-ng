}<?php
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




$mail = new phpmailer();



$mail->Host     = "mail.udistrital.edu.co";
$mail->FromName = "Correos Universidad Distrital";
$mail->From     = "condor@udistrital.edu.co";
$mail->Mailer   = "smtp";
$mail->SMTPAuth = true;
$mail->Username = "condor@udistrital.edu.co";
$mail->Password = "CondorOAS2012";
$mail->Timeout  = 120;
$mail->Charset  = "utf-8";
$mail->addAttachment($this->rutaArchivo,"resolucionIcetex.pdf");         // Add attachments


if($adjuntos!=''){
	if(is_array($adjuntos)){
		foreach($adjuntos as $adj) $mail->addAttachment($adj);;
	}
	else $mail->addAttachment($adjuntos);  
}


//Prueba
//$cuerpo .="<h1>ESTE CORREO ES UNA PRUEBA POR FAVOR NO LA TOME EN CUENTA</h1>";

//Cuerpo del mensaje
$mail->Body    = $cuerpo;
//Este el asunto
$mail->Subject = $tema;

$mail->IsHTML(true);



//Correo Bienestar
//$mail->AddAddress('bienestarud@udistrital.edu.co');


//correo prueba
$mail->AddAddress("ingenierocromeroa@gmail.com");
//$mail->AddAddress("fmcallejasc@correo.udistrital.edu.co");
/*
$mail->AddAddress("karrlyttos@hotmail.com");
$mail->AddAddress("ingenierocromeroa@gmail.com");
$mail->AddAddress("caromeroa@correo.udistrital.edu.co");
*/



if(!$mail->Send()) {
	echo $this->lenguaje->getCadena("errorMail")."<br>";;
	echo 'Mailer Error: ' . $mail->ErrorInfo;
	exit;
	
	
}
$mail->ClearAllRecipients();

$temaRegistro = 'NOTIFICAR BIENESTAR '.$temaRegistro.' ';

$this->registroLog($temaRegistro);











