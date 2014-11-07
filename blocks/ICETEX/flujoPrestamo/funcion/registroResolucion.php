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

//procesa mover el archivo

$this->moverArchivo();

//Obtiene array de codigos y valor individual para la resolucion

$this->procesarExcelCodigos();

//asigna valores

$parametros = array();
$parametros["archivo"] = $this->rutaArchivo;
$parametros["resolucion"] = $_REQUEST['resolucion'];
$parametros["valorTotal"] = $_REQUEST['valorTotal'];
$parametros["periodo"] = $_REQUEST['periodo'];
$parametros["anio"] = substr($_REQUEST['periodo'], 0, 4);
$parametros["per"] = substr($_REQUEST['periodo'], 5, 1);

$listaError = array();
$listaExito = array();

$errorUpdate = false;
$errorInsert = false;
$errorModalidad = false;
$errorFecha = false;
$errorValor = false;
$errorCodigo = false;
$bandera =0;
//procesa el listado registra la resolucion para cada uno del listado

if(!count($this->listadoCodigos)||count($this->listadoCodigos)<1){
	echo '<br><br><div style="text-align: center;font-style: italic;color:red;">';
	echo "<br>No existe conincidencia entre la resolucion ingresada y el archivo cargado<br>";
	echo "</div><br>";
	exit;
}
foreach ($this->listadoCodigos as $lista){
	    $arrayp = array();
		
		if(isset($lista[0])) {
			$parametros["codigo"] = $lista[0];
			$_REQUEST["codigo"] = $lista[0];
		}		else{
			$parametros["codigo"] =0;
			$_REQUEST["codigo"] = 0;
		}
		if(isset($lista[1])) {
			$parametros["valorIndividual"] = $lista[1];
			$_REQUEST['valorIndividual'] = $lista[1];
		}else {
			$parametros["valorIndividual"]=0;
			$_REQUEST['valorIndividual'] = 0;
		}
		$parametros['aprobado'] = 'S';
		$parametros['identificacion'] = $lista[4];
		
		//Validar Codigo - Identificacion
		if($parametros["codigo"]==0){
			$errorCodigo = true;
		}
		
		
		//validar que el valor individual sea numerico
		if(!is_numeric($parametros["valorIndividual"])||strlen ($parametros["valorIndividual"])>7){
			$errorValor = true;
		}
		
		
		//validar Modalidad de Credito
		if(strlen ($lista[2])<1){
			$parametros['modalidadCredito'] = 0;
			$errorModalidad = true;
		}else $parametros['modalidadCredito'] = $lista[2];
		
		//validar Modalidad de Credito
		if(!$this->validateDate($lista[3])){
			$parametros['fechaCredito'] = 0;
			$errorFecha = true;
		}else $parametros['fechaCredito'] = $lista[3];
		 
		
		
		
		if($errorValor==false&&$errorModalidad==false&&$errorFecha==false&&$errorCodigo==false){
				//Se agrega el cambio ya que no se habia tenido en cuenta que para hacer la solicitud el 
				//credito debe encontrarse aprobado
				//Insert
				//aprueba credito
				$cadena_sql = $this->sql->cadena_sql("aprobarCredito",$parametros);
				$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
				
				
				if($registros!=false){
					$errorInsert = true ;
				}
				
				//Update
				//Reegistra en la resolucion
				$cadena_sql = $this->sql->cadena_sql("registroResolucion",$parametros);
				$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
				
				
		
				if($registros!=false){
					$errorUpdate = true;
				}
				
				$tema = "Estado aprobación Crédito ICETEX";
				
				
				$cuerpo = "Buenos Días, <br><br><br>";
				$cuerpo .="<br>Le informamos que el giro del ICETEX a su nombre ha llegado, por lo anterior debe acercarse a realizar el tramite correspondiente dependiendo de cada caso.";
				$cuerpo .="<br><br>1. SI EL ESTUDIANTE NO HA CANCELADO A LA FECHA LA MATRICULA: para la legalización de la matricula en el proyecto curricular debe realizar la solicitud del certificado de desembolso en tesorería (7 piso)   y entregar los siguientes documentos:";
				$cuerpo .="<br>   *. Carta de solicitud de certificado de desembolso dirigida a tesorería general.";
				$cuerpo .="<br>   *. Recibo de pago 2014-1 ";
				$cuerpo .="<br>   *. Resolución del Icetex la cual se entrega en Bienestar Institucional ";
				$cuerpo .="<br>   *. Consignación Banco de Occidente, cuenta de ahorros No 23081461-8 cod 41 por valor de 5.100. ";
				$cuerpo .="<br><br><br>2. SI EL ESTUDIANTE YA CANCELO A LA FECHA LA MATRICULA: Para el reintegro del dinero debe radicar en Bienestar Institucional los siguientes documentos:";
				$cuerpo .="<br>   *. Carta de Solicitud dirigida al Ing. Jorge Federico Ramirez Escobar Dir de Bienestar Institucional. ";
				$cuerpo .="<br>   *. Dos (2) Fotocopias del documento de identidad. ";
				$cuerpo .="<br>   *. Fotocopia del recibo cancelado que se note el sello del Banco. ";
				$cuerpo .="<br><br><br><br>Cualquier duda o inquietud pueden comunicarse.";
				$cuerpo .="<br><br>Cordialmente,";
				$cuerpo .="<br>SONIA YANQUEN M.<br>Bienestar Institucional";
				$cuerpo .="<br>Horario de atención:<br>Lunes a Viernes 09:00 am a 12:30 pm y 02:00 pm a 05:00 pm.";
				
				
				//Aviso de prueba
				//$cuerpo .="<h1>SI RECIBE ESTE CORREO POR FAVOR OBVIELO ES UNA PRUEBA</h1>";
				
		
		
		}else{
			
			
			$errorInsert = true ;
			$errorUpdate = true;
			
		
		}
		
		$_REQUEST["valorConsulta"] = $_REQUEST["codigo"];
		
		
		
		
		if($errorUpdate==false&&$errorInsert==false){
                        $listaExito[$bandera]['CODIGO']=$parametros["codigo"];

                        //1. se consulta la suma de las matriculas ordinarias y extra ordinarias+
                $cadena_sqlS = $this->sql->cadena_sql("consultarValorMatricula",$parametros);
                $registrosS = $esteRecursoDB->ejecutarAcceso($cadena_sqlS,"busqueda");

                $valorOrdinaria = $registrosS[0][0] ;
                //$this->valorExtraOrdinaria = $registrosS[0][1] ;  
                        $diferencia=$valorOrdinaria - $parametros['valorIndividual'];
                        $uDiferencia = (float) sprintf('%u', $diferencia);
                        if($diferencia!=0){
                            $listaExito[$bandera]['DIFERENCIA']=$diferencia;
                            if($diferencia>0) $tDiferencia = 'Excede ';
                            if($diferencia<0){ $uDiferencia=$diferencia*-1;$tDiferencia = 'Falta ';}
                            $cuerpo .= "<br><br><b>NOTA</b>: El valor girado por el ICETEX presenta una diferencia comparada con el valor de matricula,".$tDiferencia.$diferencia;
                            $cuerpo .= "<br>Acerquese o comuniquese con <b>Bienestar Institucional</b> la universidad para realizar el procedimiento de cancelación de valores adecuados o por el contrario reclamación de los valores a devolver";
                        }
                        $bandera++;
                        
                 $this->notificarEstudiante($cuerpo, $tema , $this->rutaArchivo, 'RESOLUCION');
                 $this->actualizarEstadoFlujo();
		}else{
			
			
			if($errorValor!=false) array_push($arrayp,array($parametros["codigo"],'Valor individual',$parametros['identificacion']));
			if($errorModalidad!=false) array_push($arrayp,array($parametros["codigo"],'Modalidad',$parametros['identificacion']));
			if($errorFecha!=false) array_push($arrayp,array($parametros["codigo"],'Fecha',$parametros['identificacion']));
			if($errorCodigo!=false) array_push($arrayp,array($parametros["codigo"],'Identificacion',$parametros['identificacion']));
			if($errorInsert!=false) array_push($arrayp,array($parametros["codigo"],'Insertar',$parametros['identificacion']));
			if($errorUpdate!=false) array_push($arrayp,array($parametros["codigo"],'Actualizar',$parametros['identificacion']));
			
			array_push($listaError,$arrayp );
				
		}
		
		
		
		$errorUpdate = false;
		$errorInsert = false;
		$errorModalidad = false;
		$errorFecha = false;
		$errorValor = false;
		$errorCodigo = false;
}

//Exitoso

if(count($listaExito)>0){
	$diferencias = '';
	$diferencias .= '<div style="text-align: center;color:green"><p><b>';
	$diferencias .= $this->lenguaje->getCadena("exitoRegistroResolucion")." <br>";
    $diferencias .= '<table style="margin: 0 auto;">';
	foreach($listaExito as $li){
		$diferencias .= '<tr><td>'.$li['CODIGO'].'</td>';
                if(isset($li['DIFERENCIA'])?$li['DIFERENCIA']:'') {
                    $diferencias .= "<td> - Presenta diferencia ";
                    if($li['DIFERENCIA']>0){$diferencias .= "FALTANTE $".$li['DIFERENCIA'].'</td>';}else {$diferencias .= "EXCEDENTE $".($li['DIFERENCIA']*-1).'</td>';;}    
                    
                }else{
                    $diferencias .= '<td>&nbsp;</td>';
                }
		$diferencias .= "</tr>";
	}
	$diferencias .= "</table></b></p></div><br>";
	echo $diferencias;
}
			
			
//Error
if(count($listaError)>0){
	echo '<div style="text-align: center;color:red"><p><b>';
	echo $this->lenguaje->getCadena("errorRegistroResolucion")." <br>";
	foreach($listaError as $li){
		echo $li[0][0]." - ".$li[0][2].": ".$li[0][1]."<br>";
	}
	echo "</b></p></div><br><br>";
}
$rutaExcel = $this->crearExcelTesoreriaResolucion($listaExito);
$this->notificarTesoreria($listaExito,'','',$rutaExcel,'CARGA RESOLUCION');

exit;






