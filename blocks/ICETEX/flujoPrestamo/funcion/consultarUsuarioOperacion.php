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


if(!isset($_REQUEST['valorConsulta']))$_REQUEST['valorConsulta'] = $_REQUEST['usuario'];
if(!isset($_REQUEST['periodo']))$_REQUEST['periodo'] = $_REQUEST['periodo'];
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
    //Este se considera un error fatal
    exit;
}
$titulo =$this->lenguaje->getCadena("usuarioTablaTitulo");
$titulo2 =$this->lenguaje->getCadena("usuarioTablaTituloSeleccion");
$datosArray['valor']=$_REQUEST['valorConsulta'];
$datosArray["anio"] = substr($_REQUEST['periodo'], 0, 4);
$datosArray['per']=substr($_REQUEST['periodo'], 5, 1);

$valido = 0;
$registrosD = array();
$ix = 0;

$varEst =  false;

$_REQUEST['opcionConsulta']='codigo';

if(isset($_REQUEST['tipo'])&&$_REQUEST['tipo']='ESTUDIANTE'&&isset($_REQUEST['codigo'])&&isset($_REQUEST['periodo'])){
	$varEst =  true;
	$datosArray['valor'] = $_REQUEST['codigo'];
	$_REQUEST['opcionConsulta'] = 'codigo';

}

if($_REQUEST['opcionConsulta']=='codigo'||$varEst == true) {

	//consultar Codigo de estudiante
	$cadena_sqlD = $this->sql->cadena_sql("consultarEstudiantesCodigo",$datosArray);
	
	$registrosD[$ix] = $esteRecursoDB->ejecutarAcceso($cadena_sqlD,"busqueda");
	
	if($registrosD[$ix]!=null){
		
		if(count($registrosD[$ix])>1) $valido++;
		$valido++;
	}
	
	
}


if($valido==0){
	echo utf8_encode($this->lenguaje->getCadena("errorUsuarioNoEncuentra"));
}elseif ($valido==1){
	$this->reg = $registrosD[0];
	$this->titulo = $titulo;
	$this->mostrarTabla();

}else{

	echo '<br><table class="tablaSeleccion" style="margin: 0 auto;">';
	echo '<thead><tr><th colspan="1"><b>';
	echo utf8_encode($titulo2);
	echo '</b></th></tr></thead><tbody>';
	$cadenas = "";
	$idxs = 0; 
	foreach ($registrosD as $reg){
		//echo count($reg);
		if(is_array($reg)){
			foreach ($reg as $r){
				if($_REQUEST['modulo']=='118'&&!isset($_REQUEST['soloConsulta'])){
					echo '<tr><td id="sel'.$r[1].'" onclick="consultarSeleccionUsuario('.$r[1].',0,\''.$r[2].'\',\''.$r[3].'\');">';
				}else echo '<tr><td id="sel'.$r[1].'" onclick="consultarSeleccionUsuario('.$r[1].',1,\'\',\'\');">';
				
				if(count($r)>0){
					//echo count($reg);
					foreach ($r as $a=>$b){
						if(!is_numeric($a)){
							echo $b. " ";
						}		
					}
				}
			}
		}
		
		echo ' </td></tr>';
		
	}
	echo '</tbody></table>';
	
}

if($registrosD==null){
	echo json_encode(utf8_encode($this->lenguaje->getCadena("errorConsultaDeuda")));
}