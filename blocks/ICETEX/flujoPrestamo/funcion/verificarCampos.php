<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}
/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */
$conexion = "estructura";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
	//Este se considera un error fatal
	exit;
}



if(!isset($_REQUEST['funcion'])||$_REQUEST['funcion']=="")
	$this->error=true;



if(isset($_REQUEST['metodo'])&&$_REQUEST['metodo']=="crear"){
	if(!isset($_REQUEST['Material'])||!isset($_REQUEST['Multa'])||!isset($_REQUEST['Periodo'])
		||!isset($_REQUEST['Anno'])||!isset($_REQUEST['listadoLaboratorios'])||!isset($_REQUEST['listadoEstados'])
		||!isset($_REQUEST['identificacionDeudor']))
		$this->error=true;

	if( strlen($_REQUEST['Material'])>=100){
		echo json_encode($this->lenguaje->getCadena("errorMaterial"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Multa'])||$_REQUEST['Multa']<0){
		echo json_encode($this->lenguaje->getCadena("errorMulta"));
		$this->error=true;
		exit;
	}if( strlen($_REQUEST['Multa'])>9){
		
		echo json_encode($this->lenguaje->getCadena("errorMultaLongitud"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Periodo'])||$_REQUEST['Periodo']<0||$_REQUEST['Periodo']>3){
		echo json_encode($this->lenguaje->getCadena("errorPeriodo"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['listadoLaboratorios'])){
		echo json_encode($this->lenguaje->getCadena("errorListadoLaboratorios"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['listadoEstados'])){
		echo json_encode($this->lenguaje->getCadena("errorListadoEstados"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Anno'])	||$_REQUEST['Anno']<1995||$_REQUEST['Anno']>date("Y")){
		echo json_encode($this->lenguaje->getCadena("errorAnno"));
		$this->error=true;
		exit;
	}
}
if(!isset($_REQUEST['metodo']))$_REQUEST['metodo']= -1;

if($_REQUEST['funcion']=="solicitarCredito"||$_REQUEST['funcion']=="cancelarCredito"||$_REQUEST['funcion']=="registroReintegro"||$_REQUEST['funcion']=="aprobarCredito"||$_REQUEST['funcion']=="consultarHistorico"	){
	if(!isset($_REQUEST['valorConsulta'])){
		echo "aqui toy";
		echo $this->lenguaje->getCadena("errorCodigo");
		$this->error=true;
	}
}

if($_REQUEST['funcion']=="registroResolucion"){
	
	if(!isset($_REQUEST['resolucion'])||strlen($_REQUEST['resolucion'])>50){
		echo $this->lenguaje->getCadena("errorResolucion");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['valorTotal'])||!is_numeric($_REQUEST['valorTotal'])){
		echo $this->lenguaje->getCadena("errorValorTotal");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['modulo'])||!is_numeric($_REQUEST['modulo'])){
		
		$this->error=true;
		exit;
	}
	
	
	
}


if($_REQUEST['funcion']=="registroContable"){

	if(!isset($_REQUEST['cuentaICETEX'])||strlen($_REQUEST['cuentaICETEX'])>50){
		echo $this->lenguaje->getCadena("errorCuentaICETEX");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['nitFacultad'])||strlen($_REQUEST['nitFacultad'])>50){
		echo $this->lenguaje->getCadena("errorNitFacultad");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['cuentaFacultad'])||strlen($_REQUEST['cuentaFacultad'])>50){
		echo $this->lenguaje->getCadena("errorCuentaFacultad");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['R3'])||strlen($_REQUEST['R3'])>50){
		echo $this->lenguaje->getCadena("errorR3");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['R6'])||strlen($_REQUEST['R6'])>50){
		echo $this->lenguaje->getCadena("errorR6");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['tipo'])){
		if($_REQUEST['tipo']!='DEVOLUCION'&&$_REQUEST['tipo']!='RECLASIFICACION')
			echo $this->lenguaje->getCadena("errorCuentaFacultad");
			$this->error=true;
			exit;
	}if(!isset($_REQUEST['numero'])||strlen($_REQUEST['numero'])>50){
		echo $this->lenguaje->getCadena("errorNumero");
		$this->error=true;
		exit;
	}if(!isset($_REQUEST['observaciones'])||strlen($_REQUEST['observaciones'])>300){
		echo $this->lenguaje->getCadena("errorObservaciones");
		$this->error=true;
		exit;
	}
	
	if(!isset($_REQUEST['codigo'])){
		echo "codigo obligatorio";
		$this->error=true;
		exit;
	}



}


if(isset($_REQUEST['funcion'])&&$_REQUEST['funcion']=="actualizarDeuda"){
	if(!isset($_REQUEST['Material'])||!isset($_REQUEST['Multa'])||!isset($_REQUEST['Periodo'])
	||!isset($_REQUEST['Anno'])||!isset($_REQUEST['listadoLaboratorios'])||!isset($_REQUEST['listadoEstados'])
	||!isset($_REQUEST['identificacionDeudor']))
		$this->error=true;
	
	if( strlen($_REQUEST['Material'])>=100){
		echo json_encode($this->lenguaje->getCadena("errorMaterial"));
		$this->error=true;
		exit;
	}if( strlen($_REQUEST['Multa'])>9){
		
		echo json_encode($this->lenguaje->getCadena("errorMultaLongitud"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Multa'])||$_REQUEST['Multa']<0){
		echo json_encode($this->lenguaje->getCadena("errorMulta"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Periodo'])||$_REQUEST['Periodo']<0||$_REQUEST['Periodo']>3){
		echo json_encode($this->lenguaje->getCadena("errorPeriodo"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['listadoLaboratorios'])){
		echo json_encode($this->lenguaje->getCadena("errorListadoLaboratorios"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['listadoEstados'])){
		echo json_encode($this->lenguaje->getCadena("errorListadoEstados"));
		$this->error=true;
		exit;
	}if(!is_numeric ($_REQUEST['Anno'])	||$_REQUEST['Anno']<1995||$_REQUEST['Anno']>date("Y")){
		echo json_encode($this->lenguaje->getCadena("errorAnno"));
		$this->error=true;
		exit;
	}

}

if(isset($_REQUEST['funcion'])&&$_REQUEST['funcion']=="filtrarDeudas"){
	if(!isset($_REQUEST['listadoProyectos'])||!isset($_REQUEST['listadoFacultades'])) {
		$this->error=true;
	}
	
}