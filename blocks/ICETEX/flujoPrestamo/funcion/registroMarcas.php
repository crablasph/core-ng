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



//Obtiene array de codigos 

$this->procesarExcelMarcas();

//asigna valores

$parametros = array();


$listaError = array();
$listaExito = array();
$listaMensajes=array();
$errorConsulta = false;
$errorUpdate = false;
$errorPago = false;	 
$errorResolucion = false;	 
//procesa el listado registra la resolucion para cada uno del listado
foreach ($this->listadoCodigos as $lista){
	
		
		if(isset($lista[0])) {
			$parametros["codigo"] = $lista[0];
			$_REQUEST["codigo"] = $lista[0];
		}		else{
			$parametros["codigo"] =0;
			$_REQUEST["codigo"] = 0;
		}
		
		
		//Es necesario tomar de la tabla parametrica los valores de sistematizacion y otros
                $parametros['anio'] = substr($_REQUEST['periodo'], 0, 4);
                $parametros['per'] = substr($_REQUEST['periodo'], 5, 1);
		$cadena_sqlRefS = $this->sql->cadena_sql("consultarPagoReferenciaMatricula",$parametros);
		$registrosRefS = $esteRecursoDB->ejecutarAcceso($cadena_sqlRefS,"busqueda");
		
		if($registrosRefS==false){
			
			$errorConsulta = true;
			
		}
		
		
		if($registrosRefS[0][1]==='S'){
			
			$_REQUEST["valorConsulta"] = $_REQUEST["codigo"];
			$this->estado = 10;
			$this->actualizarEstadoFlujo();
			$errorPago = true;
			
		}
		
                //$this->revisarResolucionRegistrada();
                //Revisa si existe resolucion registrada para el recibo
                $cadena_sql = $this->sql->cadena_sql("consultarResolucionCredito",$parametros);
                $registroResolucion = $esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
                $validaResolucion = false;
                if(is_array($registroResolucion)){
                    foreach ($registroResolucion as $reg){
                            if(is_numeric($reg[1]) && $reg[1]>0) $validaResolucion = true;
                    }
                }
                if($validaResolucion!=true){
				$errorResolucion = true;
			}
		if($errorConsulta == false&&$errorPago==false && $errorResolucion==false){
			//Update
			//Reegistra Marca
			$parametros["secuencia"] = $registrosRefS[0]['EMA_SECUENCIA'];	
			$parametros["observacion"] = $registrosRefS[0]['EMA_OBS'].",Marca tesoreria";
			$cadena_sql = $this->sql->cadena_sql("registroMarca",$parametros);
			
			$registros = $esteRecursoDB->ejecutarAcceso($cadena_sql);
			
			
			if($registros!=false){
				$errorUpdate = true;
			}
			
			
			$_REQUEST["valorConsulta"] = $_REQUEST["codigo"];
			$this->estado = 10;
			
			$this->actualizarEstadoFlujo();
			$this->registroLog('MARCA RECIBO '.$_REQUEST["codigo"]);
		}
		
		
		
		
					
		
		if($errorUpdate==false&&$errorConsulta == false&&$errorResolucion==false){
			array_push($listaExito,$parametros["codigo"]);
		}else{
			$arrayp = array();
			if($errorUpdate!=false) array_push($arrayp,array($parametros["codigo"],'Actualizar'));
			if($errorConsulta!=false) array_push($arrayp,array($parametros["codigo"],'No Consulta Recibo'));
			
			if($errorResolucion!=false) array_push($arrayp,array($parametros["codigo"],'No registra resolucion'));
				
				
			array_push($listaError,$arrayp );
				
		}
		
		$arraym =  array();
		
		if($errorPago!=false){ 
			array_push($arraym,array($parametros["codigo"],'Matricula Paga'));
			array_push($listaMensajes,$arraym );
		}
		
		$errorConsulta = false;
		$errorUpdate = false;
		$errorPago = false;
		$errorResolucion = false;


}

//Exitoso
if(count($listaExito)>0){
	echo '<div style="text-align: center;color:green"><p><b>';
	echo $this->lenguaje->getCadena("exitoRegistroResolucion")." <br>";
	foreach($listaExito as $li){
		echo $li."<br>";
	}
	echo "</b></p></div><br>";
}


//Mensaje
if(count($listaMensajes)>0){
	echo '<div style="text-align: center;color:blue"><p><b>';
	echo $this->lenguaje->getCadena("mensajeRegistroResolucion")." <br>";
	foreach($listaMensajes as $li){
		echo $li[0][0]." - ".$li[0][1]."<br>";
	}
	echo "</b></p></div><br><br>";
}
			
//Error
if(count($listaError)>0){
	echo '<div style="text-align: center;color:red"><p><b>';
	echo $this->lenguaje->getCadena("errorRegistroResolucion")." <br>";
	foreach($listaError as $li){
		echo $li[0][0]." - ".$li[0][1]."<br>";
	}
	echo "</b></p></div><br><br>";
}



exit;






