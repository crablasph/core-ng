<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}
/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */
if(isset($_REQUEST['modulo'])){
	switch($_REQUEST['modulo']){
	case "80":
		$conexion="soporteoas";
		break;	
	case "118":
		$conexion="laboratorios";
			break;
	default: 
		$conexion="estructura";
	break;
	}
}else exit;

$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
	//Este se considera un error fatal
	exit;
}

$sqlP = $this->sql;

//echo "____________II____________";

//Obtiene Id
$sql_id = $sqlP->cadena_sql("idMayorTransacciones","");
$consultaId = $esteRecursoDB->ejecutarAcceso($sql_id,"busqueda");
if($consultaId != null) $parametros['idDeudaTransaccion'] =$consultaId[0][0]+1;

//Asigna ID Usuario
//Esta parte se tiene que completar cuando sepa como es el asunto de los usuarios
if(isset($_REQUEST['usuario']))$parametros['idUsuario'] = $_REQUEST['usuario'];
else exit;

if(!isset($parametros['deuId'])) $parametros['deuId']= '-1'; 



//Asigna Valor Anterior
//if($parametros['valorAnterior']!=0){
	$datos['nombreCampo'] = $parametros['nombreCampo'];
	$datos['deuId'] = (int)$parametros['deuId']; 
	$sql_valorAnterior = $sqlP->cadena_sql("consultaValorAnterior",$datos);
	$valorAnterior = $esteRecursoDB->ejecutarAcceso($sql_valorAnterior,"busqueda");
	$string = preg_replace('/\s+/', '', $valorAnterior[0][0]);
	if($valorAnterior != null&&$string !="") $parametros['valorAnterior'] =$valorAnterior[0][0];
	else $parametros['valorAnterior'] = "0";
//}
//echo "<br>------------<br>";
//print_r($parametros);
//echo "<br>------------<br>";

//Hace el insert
$sql_registro = $sqlP->cadena_sql("creaRegistro",$parametros);
//echo $sql_registro;  

//
$registro = $esteRecursoDB->ejecutarAcceso($sql_registro);


