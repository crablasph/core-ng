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







//Se debe consultar primero el valor del seguro y otras referencias diferentes a la matricula

$cadena_sqlRefM = $this->sql->cadena_sql("consultarReferenciaMatricula",$_REQUEST['valorConsulta']);
$registrosRefM = $esteRecursoDB->ejecutarAcceso($cadena_sqlRefM,"busqueda");
if($registrosRefM==false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoConsulta");
	echo "</b></p></div>";
	exit;
}


//Se asigna el array de las referencias
$registrosRefS = $this->referencias;



//Se asignan las variables para la creacion del recibo
$parametros = array();
$parametros['codigo'] = $_REQUEST['valorConsulta'];
$parametros['proyectoCurricular'] = $this->proyectoCurricular;
$parametros['valorOrdinaria'] = $this->valorOrdinaria;
$parametros['valorExtraOrdinaria'] = $this->valorExtraOrdinaria;
$parametros['fechaOrdinaria'] = $this->fechaOrdinaria;
$parametros['fechaExtraOrdinaria'] = $this->fechaExtraOrdinaria;
$parametros['anoPago'] = $this->anoPago;
$parametros['periodoPago'] = $this->periodoPago;
$parametros['referenciaMatricula'] = $registrosRefM[0][1];
$parametros['referenciaBancoMatricula'] = $registrosRefM[0][0];

/*
$parametros['referenciaSeguro'] = $registrosRefS[0][1];
$parametros['valorSeguro'] = 5500;
$parametros['referenciaBancoSeguro'] = $registrosRefS[0][0];

*/


//crea recibo del seguro

$cadena_sql = $this->sql->cadena_sql("separarMatriculaSeguro",$parametros);
$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);

if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoCreaRecibo");
	echo "</b></p></div>";
	exit;
}



foreach($registrosRefS as $reg){
	$parametros['referenciaSeguro'] = $reg[2];
	$parametros['valorSeguro'] = $reg[4];
	$parametros['referenciaBancoSeguro'] = $reg[3];
	
	$cadena_sql = $this->sql->cadena_sql("separarMatriculaSeguroReferencia",$parametros);
	
	$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
	
	if($registros!=false){
		echo '<div style="text-align: center"><p><b>';
		echo $this->lenguaje->getCadena("errorNoCreaRecibo");
		echo "</b></p></div>";
		exit;
	}
}	


//crea recibo de la matricula
$cadena_sql = $this->sql->cadena_sql("separarMatriculaNoSeguro",$parametros);

$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);

if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoCreaRecibo");
	echo "</b></p></div>";
	exit;
}



$cadena_sql = $this->sql->cadena_sql("separarMatriculaNoSeguroReferencia",$parametros);

$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);

if($registros!=false){
	echo '<div style="text-align: center"><p><b>';
	echo $this->lenguaje->getCadena("errorNoCreaRecibo");
	echo "</b></p></div>";
	exit;
}
$this->registroLog('SEPARAR MATRICULA '.$_REQUEST['valorConsulta']);

return true;




