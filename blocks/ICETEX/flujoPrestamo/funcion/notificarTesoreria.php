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

if($tema=='') $tema = "Recepcion Resolución Credito Estudiantes";

if($cuerpo == '')$cuerpo = "Se ha cargado una resolucion de credito de estudiantes al sistema<br>el archivo PDF de la resolución se encuentra adjunto<br><br><b>El siguiente Listado se encuentra en la resolucion</b><br>";

	if(is_array($lista)){
		$cuerpo .=  "<table>";
		foreach($lista as $li){
		        
		        $cuerpo .=  "<tr><td>".$li['CODIGO']."</td>";
		        if(isset($li['DIFERENCIA'])?$li['DIFERENCIA']:'') {
		        	$cuerpo .=  "<td> - Presenta diferencia ";
		                    if($li['DIFERENCIA']>0){
		                    	$cuerpo .=  "FALTANTE $".$li['DIFERENCIA']."</td>";
		                    }else {
		                    	$cuerpo .=  "EXCEDENTE $".($li['DIFERENCIA']*-1)."</td>";
		                    }    
		        }                   
		        $cuerpo .= "</tr>";
		}
		$cuerpo .= "</table>";
	}


//Prueba
//$cuerpo .="<h1>ESTE CORREO ES UNA PRUEBA POR FAVOR NO LA TOME EN CUENTA</h1>";

//Cuerpo del mensaje
$mail->Body    = $cuerpo;
//Este el asunto
$mail->Subject = $tema;

$mail->IsHTML(true);



//Correos 
//$mail->AddAddress("jortiza@udistrital.edu.co");
//$mail->AddAddress("dcsanchezc@udistrital.edu.co");
//$mail->AddAddress("eperezg@udistrital.edu.co");


//Correo Bienestar
//$mail->AddAddress('bienestarud@udistrital.edu.co');


//correo prueba
$mail->AddAddress("ingenierocromeroa@gmail.com");
/*
$mail->AddAddress("karrlyttos@hotmail.com");
$mail->AddAddress("ingenierocromeroa@gmail.com");
$mail->AddAddress("caromeroa@correo.udistrital.edu.co");
*/



if(!$mail->Send()) {
	echo $this->lenguaje->getCadena("errorMail")."<br>";
	echo 'Mailer Error: ' . $mail->ErrorInfo;
	exit;
	
	
}
$mail->ClearAllRecipients();

$temaRegistro = 'NOTIFICAR TESORERIA '.$temaRegistro.' ';

$this->registroLog($temaRegistro);











