<?php

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlflujoPrestamo extends sql {
	
	
	var $miConfigurador;
	
	
	function __construct(){
		$this->miConfigurador=Configurador::singleton();
	}
	

	function cadena_sql($tipo,$variable="") {
		 
		/**
		 * 1. Revisar las variables para evitar SQL Injection
		 *
		 */
		
		$prefijo=$this->miConfigurador->getVariableConfiguracion("prefijo");
		$idSesion=$this->miConfigurador->getVariableConfiguracion("id_sesion");
		switch($tipo) {
			 
			/**
			 * Clausulas específicas
			 */
			
			case "buscarValorSesion":
				$cadena_sql="SELECT ";
				$cadena_sql.="valor, ";
				$cadena_sql.="sesionid, ";
				$cadena_sql.="variable, ";
				$cadena_sql.="expiracion ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."valor_sesion ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="sesionid ='".$variable["sesionId"]."' ";
				$cadena_sql.="AND ";
				$cadena_sql.="variable='".$variable["variable"]."' ";
				break;
			 
			case "buscarUsuario":
				$cadena_sql="SELECT ";
				$cadena_sql.="FECHA_CREACION, ";
				$cadena_sql.="PRIMER_NOMBRE ";				 
				$cadena_sql.="FROM ";
				$cadena_sql.="USUARIOS ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`PRIMER_NOMBRE` ='".$variable."' ";
				break;


			case "insertarRegistro":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."registradoConferencia ";
				$cadena_sql.="( ";
				$cadena_sql.="`idRegistrado`, ";
				$cadena_sql.="`nombre`, ";
				$cadena_sql.="`apellido`, ";
				$cadena_sql.="`identificacion`, ";
				$cadena_sql.="`codigo`, ";
				$cadena_sql.="`correo`, ";
				$cadena_sql.="`tipo`, ";
				$cadena_sql.="`fecha` ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="( ";
				$cadena_sql.="NULL, ";
				$cadena_sql.="'".$variable['nombre']."', ";
				$cadena_sql.="'".$variable['apellido']."', ";
				$cadena_sql.="'".$variable['identificacion']."', ";
				$cadena_sql.="'".$variable['codigo']."', ";
				$cadena_sql.="'".$variable['correo']."', ";
				$cadena_sql.="'0', ";
				$cadena_sql.="'".time()."' ";
				$cadena_sql.=")";
				break;


			case "actualizarRegistro":
				$cadena_sql="UPDATE ";
				$cadena_sql.=$prefijo."conductor ";
				$cadena_sql.="SET ";
				$cadena_sql.="`nombre` = '".$variable["nombre"]."', ";
				$cadena_sql.="`apellido` = '".$variable["apellido"]."', ";
				$cadena_sql.="`identificacion` = '".$variable["identificacion"]."', ";
				$cadena_sql.="`telefono` = '".$variable["telefono"]."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="`idConductor` =".$_REQUEST["registro"]." ";
				break;
				 

				/**
				 * Clausulas genéricas. se espera que estén en todos los formularios
				 * que utilicen esta plantilla
				 */

			case "iniciarTransaccion":
				$cadena_sql="START TRANSACTION";
				break;

			case "finalizarTransaccion":
				$cadena_sql="COMMIT";
				break;

			case "cancelarTransaccion":
				$cadena_sql="ROLLBACK";
				break;


			case "eliminarTemp":

				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tempFormulario ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_sesion = '".$variable."' ";
				break;

			case "insertarTemp":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.=$prefijo."tempFormulario ";
				$cadena_sql.="( ";
				$cadena_sql.="id_sesion, ";
				$cadena_sql.="formulario, ";
				$cadena_sql.="campo, ";
				$cadena_sql.="valor, ";
				$cadena_sql.="fecha ";
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";

				foreach($_REQUEST as $clave => $valor) {
					$cadena_sql.="( ";
					$cadena_sql.="'".$idSesion."', ";
					$cadena_sql.="'".$variable['formulario']."', ";
					$cadena_sql.="'".$clave."', ";
					$cadena_sql.="'".$valor."', ";
					$cadena_sql.="'".$variable['fecha']."' ";
					$cadena_sql.="),";
				}

				$cadena_sql=substr($cadena_sql,0,(strlen($cadena_sql)-1));
				break;

			case "rescatarTemp":
				$cadena_sql="SELECT ";
				$cadena_sql.="id_sesion, ";
				$cadena_sql.="formulario, ";
				$cadena_sql.="campo, ";
				$cadena_sql.="valor, ";
				$cadena_sql.="fecha ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$prefijo."tempFormulario ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="id_sesion='".$idSesion."'";
				break;
				
			case "consultarReferenciaMatricula":
				$cadena_sql=" SELECT REB_BANCOD, REB_REFCOD , REB_REFNOM FROM ACREFBAN ";
				$cadena_sql.=" WHERE REB_REFNOM = 'MATRICULA' ";
				break;
			
			case "consultarReferenciaSeguro":
				$cadena_sql=" SELECT REB_BANCOD, REB_REFCOD , REB_REFNOM FROM ACREFBAN ";
				$cadena_sql.=" WHERE REB_REFNOM = 'SEGURO' ";
				break;
				
			case "consultarPagoReferenciaMatricula":
				$cadena_sql=" SELECT EMA_EST_COD , EMA_PAGO , AER_REFCOD , AER_VALOR, REB_REFNOM , REB_REFDES , EMA_SECUENCIA  , EMA_OBS FROM ACESTMAT ";
				$cadena_sql.=" LEFT JOIN ACREFEST ON AER_SECUENCIA = EMA_SECUENCIA ";
				$cadena_sql.=" LEFT JOIN ACREFBAN ON  REB_REFCOD = AER_REFCOD ";
				$cadena_sql.=" WHERE EMA_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND EMA_PER = ".$variable['per']." ";
				$cadena_sql.=" AND EMA_ESTADO = 'A' ";
				$cadena_sql.=" AND EMA_EST_COD = ".$variable['codigo']." ";
				$cadena_sql.=" AND REB_REFNOM = 'MATRICULA' ";
				$cadena_sql.=" AND AER_ANO  = ".$variable['anio']." ";
				break;
					
				
			case "consultarFestivos":
				$cadena_sql="SELECT to_char(fecha_festivo,'dd/mm/yyyy') FROM pe_festivos WHERE to_char(fecha_festivo,'yyyy') = to_char(sysdate , 'yyyy')";
				break;
				
			case "consultarEstadoFlujo":
					$cadena_sql='SELECT';
					$cadena_sql.=' A."CRE_ESTADO",';
					$cadena_sql.=' B."ESTADO_VALOR",';
					$cadena_sql.=' A."CRE_FECHA_CREACION",';
					$cadena_sql.=' A."CRE_FECHA_ACTUALIZACION"';
					$cadena_sql.=' FROM CREDITO_ESTADO_ESTUDIANTES A , CREDITO_ESTADO_FLUJO B';
					$cadena_sql.=' WHERE A."CRE_ESTADO" = B."ID_ESTADO_FLUJO"';
					$cadena_sql.=' AND A."CRE_EST_COD"='.$variable['codigo'];
					$cadena_sql.=' AND A."CRE_ANO"='.$variable['anio'];
					$cadena_sql.=" AND A.CRE_PERIODO=".$variable['per'];
					$cadena_sql.=" ORDER BY A.CRE_FECHA_ACTUALIZACION DESC";
						
					break;
					
			case "consultarCreditoAprobadoReintegro":
				$cadena_sql=" SELECT CRE_APROVADO , CRE_REINTEGRO FROM CREDITO_RESOLUCION ";
				$cadena_sql.=" WHERE CRE_EST_COD = ".$variable;
				$cadena_sql.=" AND CRE_ANO = to_char(sysdate,'YYYY') ";
				$cadena_sql.=" AND CRE_PERIODO = (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ) ";
				
				break;
				
			case "aprobarCredito":
				$cadena_sql=" INSERT INTO CREDITO_RESOLUCION (CRE_EST_COD, CRE_ANO , CRE_PERIODO , CRE_APROVADO , CRE_REINTEGRO , CRE_FECHA) VALUES ( ";
				$cadena_sql.=" ".$variable['codigo'].", ";
				$cadena_sql.=" '".$variable['anio']."', '".$variable['per']."', '".$variable['aprobado']."', 'N', sysdate ";
				$cadena_sql.=" ) ";
				break;
				
			case "registroMarca":
				$cadena_sql=" UPDATE ACESTMAT SET ";
				$cadena_sql.=" EMA_PAGO = 'S', ";
				$cadena_sql.=" EMA_OBS = '".$variable['observacion']."' ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" EMA_EST_COD =  ".$variable['codigo']." ";
				$cadena_sql.=" AND EMA_SECUENCIA = ".$variable['secuencia']." ";
				$cadena_sql.=" AND EMA_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND EMA_PER = ".$variable['per']." ";
				break;
				
			case "consultarCodigoIdentificacion":
				$cadena_sql=" SELECT DISTINCT EMA_EST_COD FROM ACEST A , ACESTMAT B ";
				$cadena_sql.=" WHERE A.EST_NRO_IDEN = ".$variable['identificacion']." ";
				$cadena_sql.=" AND B.EMA_EST_COD = A.EST_COD ";
				$cadena_sql.=" AND B.EMA_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND B.EMA_PER = ".$variable['per']." ";
				break;
				
			case "registroResolucion":
				$cadena_sql=" UPDATE CREDITO_RESOLUCION SET ";
				$cadena_sql.=" CRE_MODALIDAD = '".$variable['modalidadCredito']."', ";
				$cadena_sql.=" CRE_FECHA_INGRESO = '".$variable['fechaCredito']."', ";
				$cadena_sql.=" CRE_RESOLUCION = '".$variable['resolucion']."', ";
				$cadena_sql.=" CRE_VALOR_TOTAL = ".$variable['valorTotal'].", ";
				$cadena_sql.=" CRE_VALOR_INDIVIDUAL = ".$variable['valorIndividual'].", ";
				$cadena_sql.=" CRE_DOCUMENTO = '".$variable['archivo']."' ";
				$cadena_sql.=" WHERE CRE_EST_COD = ".$variable['codigo']." ";
				$cadena_sql.=" AND CRE_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND CRE_PERIODO = ".$variable['per']." ";
				break;
			
			case "actualizaNotificado":
				$cadena_sql=" UPDATE CREDITO_RESOLUCION SET ";
				$cadena_sql.=" CRE_NOTIFICADO = '".$variable['notificado']."' ";
				$cadena_sql.=" WHERE CRE_EST_COD = ".$variable['codigo']." ";
				$cadena_sql.=" AND CRE_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND CRE_PERIODO = ".$variable['per']." ";
				break;
				
			case "actualizaReintegro":
				$cadena_sql=" UPDATE CREDITO_RESOLUCION SET ";
				$cadena_sql.=" CRE_REINTEGRO = 'S' ";
				$cadena_sql.=" WHERE CRE_EST_COD = ".$variable." ";
				$cadena_sql.=" AND CRE_ANO = to_char(sysdate,'YYYY') ";
				$cadena_sql.=" AND CRE_PERIODO = (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ) ";
				break;
				
			case "consultarCorreosEstudiante":
				$cadena_sql=" SELECT EOT_EMAIL , EOT_EMAIL_INS ";
				$cadena_sql.=" FROM ACESTOTR ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" EOT_COD = ".$variable." ";
				break;
					
			case "creaFlujo":
				$cadena_sql=" INSERT INTO  CREDITO_ESTADO_ESTUDIANTES (CRE_EST_COD , CRE_ANO , CRE_PERIODO , CRE_ESTADO , CRE_FECHA_CREACION , CRE_FECHA_ACTUALIZACION)  VALUES ( ";
				$cadena_sql.=" ".$variable['codigo'].", ";
				$cadena_sql.=" ".$variable['anio'].",".$variable['per'].", ";
				$cadena_sql.=" ".$variable['estado'].", ";
				$cadena_sql.=" sysdate,sysdate ";
				$cadena_sql.=" ) ";
				break;
				
			case "actualizarFlujo":
				$cadena_sql=" UPDATE CREDITO_ESTADO_ESTUDIANTES SET ";
				$cadena_sql.=" CRE_FECHA_ACTUALIZACION = sysdate , ";
				$cadena_sql.=" CRE_ESTADO = ".$variable['estado']." ";
				$cadena_sql.=" WHERE CRE_EST_COD = ".$variable['codigo']." ";
				$cadena_sql.=" AND CRE_ANO = ".$variable['anio']." ";
				$cadena_sql.=" AND CRE_PERIODO = ".$variable['per']." ";
				break;
					
			case "consultarRecibosCreados":
				$cadena_sql=" SELECT EMA_SECUENCIA, EMA_PAGO   FROM ACESTMAT ";
				$cadena_sql.=" WHERE EMA_ANO = ".$variable['anio'];
				$cadena_sql.=" AND EMA_PER = ".$variable['per'];
				$cadena_sql.=" AND EMA_EST_COD = '".$variable['codigo']."'";
				$cadena_sql.=" AND EMA_ESTADO = 'A'";
				break;
				
			case "consultarValorMatricula":
				$cadena_sql=" SELECT SUM(EMA_VALOR), SUM(EMA_EXT) ";
				$cadena_sql.=" FROM ACESTMAT ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" ema_ano = ".$variable['anio']." ";
				$cadena_sql.=" AND ema_est_cod = '".$variable['codigo']."' ";
				$cadena_sql.=" AND EMA_PER = ".$variable['per']." ";
				$cadena_sql.=" AND EMA_ESTADO = 'A'";
				$cadena_sql.=" GROUP BY EMA_EST_COD ";
				break;
				
			case "consultarFechaMatricula":
				$cadena_sql=" SELECT TO_CHAR(EMA_FECHA_ORD,'DD/MM/YYYY') , TO_CHAR(EMA_FECHA_EXT,'DD/MM/YYYY'), EMA_CRA_COD , EMA_ANO_PAGO , EMA_PER_PAGO ";
				$cadena_sql.=" FROM ACESTMAT ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" ema_ano = to_char(sysdate, 'YYYY') ";
				$cadena_sql.=" AND ema_est_cod = '".$variable."' ";
				$cadena_sql.=" AND EMA_PER = (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A') ";
				$cadena_sql.=" AND EMA_ESTADO = 'A'";
				$cadena_sql.=" ORDER BY 1 DESC ";
				break;
				
			case "consultarReferenciasNoMatricula":
				$cadena_sql=" SELECT AER_ANO , AER_SECUENCIA ,AER_REFCOD , AER_BANCOD , AER_VALOR ,REB_REFNOM , REB_REFDES FROM ACREFEST	 ";
				$cadena_sql.=" INNER JOIN ACREFBAN ON  REB_REFCOD = AER_REFCOD "; 
				$cadena_sql.=" WHERE AER_SECUENCIA IN ( "; 
				$cadena_sql.=" SELECT EMA_SECUENCIA FROM ACESTMAT "; 
				$cadena_sql.=" WHERE EMA_ANO = to_char(sysdate,'YYYY') ";
				$cadena_sql.=" AND EMA_PER = (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ) ";
				$cadena_sql.=" AND EMA_EST_COD = ".$variable." ";
				$cadena_sql.=" AND EMA_ESTADO = 'A' ";
				$cadena_sql.=" ) AND REB_REFNOM !='MATRICULA' ";
				$cadena_sql.="  AND AER_ANO = to_char(sysdate,'YYYY')";
				break;
				
			case "desactivarRecibos":
				$cadena_sql=" UPDATE ACESTMAT SET EMA_ESTADO = 'I' ";
				$cadena_sql.=" WHERE EMA_ANO = to_char(sysdate, 'YYYY') ";
				$cadena_sql.=" AND EMA_PER = (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A') ";
				$cadena_sql.=" AND EMA_EST_COD = '".$variable."' ";
				$cadena_sql.=" AND EMA_ESTADO = 'A'";
				break;
				
			case "separarMatriculaSeguro":
				$cadena_sql=" INSERT INTO ACESTMAT ";
				$cadena_sql.=" (EMA_EST_COD, EMA_CRA_COD, EMA_VALOR, EMA_EXT, EMA_ANO, EMA_PER, EMA_FECHA, EMA_ESTADO , EMA_SECUENCIA , ";
				$cadena_sql.=" EMA_CUOTA , EMA_FECHA_ORD , EMA_FECHA_EXT , EMA_IMP_RECIBO , EMA_PAGO , EMA_ANO_PAGO , EMA_PER_PAGO , EMA_OBS , EMA_ENVIADO_EMAIL ";
				$cadena_sql.=" ) VALUES ( ";
				$cadena_sql.=" ".$variable['codigo']." ,";
				$cadena_sql.=" ".$variable['proyectoCurricular']." , ";
				$cadena_sql.=" 0 , ";
				$cadena_sql.=" 0, ";
				$cadena_sql.=" to_char(sysdate,'YYYY'),(SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ),sysdate,'A', SEQ_MATRICULA.NEXTVAL,1, ";
				$cadena_sql.=" to_date('".$variable['fechaOrdinaria']."','DD/MM/YYYY'), ";
				$cadena_sql.=" to_date('".$variable['fechaExtraOrdinaria']."','DD/MM/YYYY'), ";
				$cadena_sql.=" 0,'N', ";
				$cadena_sql.=" ".$variable['anoPago'].", ";
				$cadena_sql.=" ".$variable['periodoPago'].", ";
				$cadena_sql.=" 'Seguro Matricula ICETEX', ";
				$cadena_sql.=" 'N' ";
				$cadena_sql.=" ) ";
				break;
				
			case "separarMatriculaSeguroReferencia":
				$cadena_sql=" INSERT INTO ACREFEST (AER_ANO , AER_SECUENCIA , AER_BANCOD , AER_REFCOD , AER_VALOR) VALUES ( ";
				$cadena_sql.=" to_char(sysdate,'YYYY'), ";
				$cadena_sql.=" SEQ_MATRICULA.CURRVAL,".$variable['referenciaBancoSeguro'].",".$variable['referenciaSeguro'].", ";
				$cadena_sql.=" ".$variable['valorSeguro']." ";
				$cadena_sql.=" ) ";
				break;
				
			case "separarMatriculaNoSeguro":
				$cadena_sql=" INSERT INTO ACESTMAT ";
				$cadena_sql.=" (EMA_EST_COD, EMA_CRA_COD, EMA_VALOR, EMA_EXT, EMA_ANO, EMA_PER, EMA_FECHA, EMA_ESTADO , EMA_SECUENCIA , ";
				$cadena_sql.=" EMA_CUOTA , EMA_FECHA_ORD , EMA_FECHA_EXT , EMA_IMP_RECIBO , EMA_PAGO , EMA_ANO_PAGO , EMA_PER_PAGO , EMA_OBS , EMA_ENVIADO_EMAIL ";
				$cadena_sql.=" ) VALUES ( ";
				$cadena_sql.=" ".$variable['codigo']." ,";
				$cadena_sql.=" ".$variable['proyectoCurricular']." , ";
				$cadena_sql.=" ".$variable['valorOrdinaria']." , ";
				$cadena_sql.=" ".$variable['valorExtraOrdinaria'].", ";
				$cadena_sql.=" to_char(sysdate,'YYYY'),(SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ),sysdate,'A', SEQ_MATRICULA.NEXTVAL,1, ";
				$cadena_sql.=" to_date('".$variable['fechaOrdinaria']."','DD/MM/YYYY'), ";
				$cadena_sql.=" to_date('".$variable['fechaExtraOrdinaria']."','DD/MM/YYYY'), ";
				$cadena_sql.=" 0,'N', ";
				$cadena_sql.=" ".$variable['anoPago'].", ";
				$cadena_sql.=" ".$variable['periodoPago'].", ";
				$cadena_sql.=" 'Matricula ICETEX', ";
				$cadena_sql.=" 'N' ";
				$cadena_sql.=" ) ";
				break;
				
			case "separarMatriculaNoSeguroReferencia":
				$cadena_sql=" INSERT INTO ACREFEST (AER_ANO , AER_SECUENCIA , AER_BANCOD , AER_REFCOD , AER_VALOR) VALUES ( ";
				$cadena_sql.=" to_char(sysdate,'YYYY'), ";
				$cadena_sql.=" SEQ_MATRICULA.CURRVAL,".$variable['referenciaBancoMatricula'].",".$variable['referenciaMatricula'].", ";
				$cadena_sql.=" ".$variable['valorOrdinaria']." ";
				$cadena_sql.=" ) ";
				break;
				
			case "consultarEstudiantesCodigo":
					$cadena_sql='SELECT ';
					$cadena_sql.=' A."EST_NOMBRE" AS "Nombre", ';
					$cadena_sql.=' A."EST_NRO_IDEN" AS "Identificacion", ';
					$cadena_sql.=' \'ESTUDIANTE\' AS "Tipo", ';
					$cadena_sql.=' A."EST_COD" AS "Codigo", ';
					$cadena_sql.=' A."EST_CRA_COD" AS "Proyecto Curricular", ';
					$cadena_sql.=' B."ESTADO_NOMBRE" AS "Estado" ';
					$cadena_sql.=' FROM  ACEST A,  ACESTADO B ';
					$cadena_sql.=' WHERE A."EST_ESTADO" = B."ESTADO_COD" ';
					$cadena_sql.=' AND A."EST_COD" = \''.$variable['valor'].'\' ';
					break;
					
			case "consultarEstudiantesIdentificacion":
					$cadena_sql='SELECT ';
					$cadena_sql.=' A."EST_NOMBRE" AS "Nombre", ';
					$cadena_sql.=' A."EST_NRO_IDEN" AS "Identificacion", ';
					$cadena_sql.=' \'ESTUDIANTE\' AS "Tipo", ';
					$cadena_sql.=' A."EST_COD" AS "Codigo", ';
					$cadena_sql.=' A."EST_CRA_COD" AS "Proyecto Curricular", ';
					$cadena_sql.=' B."ESTADO_NOMBRE" AS "Estado" ';
					$cadena_sql.=' FROM  ACEST A,  ACESTADO B ';
					$cadena_sql.=' WHERE A."EST_ESTADO" = B."ESTADO_COD" ';
					$cadena_sql.=' AND A."EST_NRO_IDEN" = \''.$variable['valor'].'\' ';
					break;
					
			case "registroContable":
				$cadena_sql=" INSERT INTO CREDITO_FINANCIERA ";
				$cadena_sql.=" (CRE_EST_COD, CRE_ANO, CRE_PERIODO, CRE_NRO_CTA_ICETEX, CRE_NRO_CTA_FACULTAD, CRE_NIT_FACULTAD, CRE_NRO_R3, CRE_NRO_R6, ";
				$cadena_sql.=" CRE_TIPO, CRE_RADICADO, CRE_OBSERVACIONES, CRE_FECHA_REGISTRO) "; 
				$cadena_sql.=" VALUES ( ";
				$cadena_sql.=" ".$variable['codigo'].", ";
				$cadena_sql.=" to_char(sysdate,'YYYY'), (SELECT APE_PER FROM ACASPERI WHERE APE_ESTADO = 'A' ), ";
				$cadena_sql.=" '".$variable['cuentaICETEX']."', ";
				$cadena_sql.=" '".$variable['cuentaFacultad']."', ";
				$cadena_sql.="  '".$variable['nitFacultad']."', ";
				$cadena_sql.=" '".$variable['R3']."', ";
				$cadena_sql.=" '".$variable['R6']."', "; 
				$cadena_sql.=" '".$variable['tipo']."', ";
				$cadena_sql.=" '".$variable['numero']."', ";
				$cadena_sql.=" '".$variable['observaciones']."', ";
				$cadena_sql.=" sysdate ";
				$cadena_sql.=" ) ";
				break;
					
				
			case "consultarHistorico":
				$cadena_sql=" SELECT A.CRE_FECHA_CREACION , B.CRE_RESOLUCION , B.CRE_VALOR_TOTAL, B.CRE_VALOR_INDIVIDUAL , B.CRE_DOCUMENTO, ";
				$cadena_sql.=" DECODE(B.CRE_REINTEGRO,'S','SI','N','NO') 	 ";
				$cadena_sql.=" FROM CREDITO_ESTADO_ESTUDIANTES A "; 
				$cadena_sql.=" LEFT JOIN CREDITO_RESOLUCION B ON ";
				$cadena_sql.=" A.CRE_EST_COD = B.CRE_EST_COD AND A.CRE_ANO = B.CRE_ANO AND A.CRE_PERIODO = B.CRE_PERIODO ";
				$cadena_sql.=" WHERE A.CRE_EST_COD =  ".$variable['codigo'];
				$cadena_sql.=" AND A.CRE_ANO =  ".$variable['anio'];
				$cadena_sql.=" AND A.CRE_PERIODO =  ".$variable['per'];
				
				break;
		

		  case "consultarNombres":
		  	$cadena_sql='SELECT  A."EST_NOMBRE" AS "Nombre",  A."EST_NRO_IDEN" AS "Identificacion", ';
		  	$cadena_sql.=' \'ESTUDIANTE\' AS "Tipo",A."EST_COD" AS "Codigo",B."ESTADO_NOMBRE" AS "Estado" ';
		  	$cadena_sql.=' FROM  ACEST A,  ACESTADO B ';
		  	$cadena_sql.=' WHERE A."EST_ESTADO" = B."ESTADO_COD" ';
		  	$cadena_sql.=' AND lower(A."EST_NOMBRE")  like \'%'.$variable.'%\' ';
		  	
		  	$cadena_sql.=' UNION ';
		  	
		  	$cadena_sql.=' SELECT  C."DOC_NOMBRE"||\' \'||C."DOC_APELLIDO" AS "Nombre",  C."DOC_NRO_IDEN" AS "Identificacion", ';
		  	$cadena_sql.='  \'DOCENTE\' AS "Tipo", 0 AS "Codigo", C."DOC_ESTADO" AS "Estado" ';
		  	$cadena_sql.=' FROM  ACDOCENTE C ';
		  	$cadena_sql.=' WHERE  lower(C."DOC_NOMBRE") like \'%'.$variable.'%\' or lower(C."DOC_APELLIDO") like \'%'.$variable.'%\' ';
		  	
		  	$cadena_sql.=' UNION ';
		  	
		  	$cadena_sql.=' SELECT  D."EMP_NOMBRE" AS "Nombre",  D."EMP_NRO_IDEN" AS "Identificacion", ';
		  	$cadena_sql.='  \'ADMINISTRATIVO\' AS "Tipo", 0 AS "Codigo" ,D."EMP_ESTADO" AS "Estado" ';
		  	$cadena_sql.=' FROM  PEEMP D ';
		  	$cadena_sql.=' WHERE  lower(D."EMP_NOMBRE") like \'%'.$variable.'%\' ';
		  			break;
		  	
		  case "consultarNombrePorIdentificacionEstudiante":
		  	$cadena_sql='SELECT A."EST_NOMBRE" AS "Nombre" FROM  ACEST A';
		  	$cadena_sql.=' WHERE A."EST_NRO_IDEN"=\''.$variable['identificacionDeudor'].'\'';
		  	break;
		  
		  	  	
		  case "consultarCodigoEstudiantePorIdentificacion":
		  	$cadena_sql='SELECT A."EST_COD" AS "Codigo" FROM  ACEST A ';
		  	$cadena_sql.=' WHERE A."EST_NRO_IDEN"=\''.$variable['identificacionDeudor'].'\' ORDER BY 1 DESC';
		  	break;
		  	
		  case "registroLog":
		  	$cadena_sql= "INSERT INTO CREDITO_REGISTRO (CRE_REG_USUARIO , CRE_REG_ACCION) VALUES ('".$variable['usuario']."','".$variable['accion']."') ";
		  	break;
		  	
		  
                case "periodoActualYAnterior":
		  	$cadena_sql=' SELECT ape_ano||\'-\'||ape_per as PERIODO  ';
		  	$cadena_sql.='FROM acasperi WHERE ape_Estado IN (\'A\',\'P\') ORDER BY PERIODO DESC';
		  	break;
		
                case "listadoPeriodos":
		  	$cadena_sql=' SELECT ape_ano||\'-\'||ape_per as PERIODO  ';
		  	$cadena_sql.='FROM acasperi WHERE ape_ano>=2014 ORDER BY PERIODO DESC';
		  	break;
		
                case "periodoActual":
		  	$cadena_sql=" SELECT ape_ano||'-'||ape_per as PERIODO,  ";
		  	$cadena_sql.=" ape_ano as ANIO,";
		  	$cadena_sql.=" ape_per as PER  ";
		  	$cadena_sql.=" FROM acasperi WHERE ape_estado='A' ";
		  	break;
		
                case "consultarResolucionCredito":
		  	$cadena_sql=' SELECT CRE_RESOLUCION, CRE_VALOR_INDIVIDUAL, CRE_FECHA ';
		  	$cadena_sql.=' FROM CREDITO_RESOLUCION ';
		  	$cadena_sql.=' WHERE CRE_ANO='.$variable['anio'].' ';
		  	$cadena_sql.=' AND CRE_PERIODO='.$variable['per'].' ';
		  	$cadena_sql.=' AND CRE_EST_COD='.$variable['codigo'].' ';
		  	break;
		  	
            case "consultarDatosTotalesCredito":
               	$cadena_sql=" 	SELECT B.EST_NRO_IDEN IDENTIFICACION ";
                $cadena_sql.=" 	, C.CRA_NOMBRE NOMBRE ";
                $cadena_sql.=" 	, B.EST_COD CODIGO ";
                $cadena_sql.=" 	, C.CRA_NOMBRE CARRERA ";
                $cadena_sql.=" 	, D.DEP_NOMBRE FACULTAD ";
                $cadena_sql.=" 	, A.ema_ano||'.'||A.EMA_PER PERIODO , E.CRE_RESOLUCION ";
                $cadena_sql.=" 	, SUM(A.EMA_VALOR) MATRICULA ";
                $cadena_sql.=" 	, E.CRE_VALOR_INDIVIDUAL ";
                $cadena_sql.="  , (SUM(A.EMA_VALOR) -  E.CRE_VALOR_INDIVIDUAL ) DIFERENCIA ";
                $cadena_sql.=" FROM ACESTMAT  A, ACEST B , ACCRA C , GEDEP D , CREDITO_RESOLUCION E ";
                $cadena_sql.=" WHERE ";
                $cadena_sql.=" A.ema_ano = ".$variable['anio'];
                $cadena_sql.=" AND A.ema_est_cod = '".$variable['codigo']."' ";
                $cadena_sql.=" AND A.EMA_PER = ".$variable['per']." ";
                $cadena_sql.=" AND A.EMA_ESTADO = 'A' ";
                $cadena_sql.=" AND A.ema_est_cod =  B.EST_COD ";
                $cadena_sql.=" AND C.CRA_COD = B.EST_CRA_COD ";
                $cadena_sql.=" AND C.CRA_DEP_COD = D.DEP_COD ";
                $cadena_sql.=" AND B.EST_COD = E.CRE_EST_COD ";
                $cadena_sql.=" GROUP BY A.EMA_EST_COD ,  B.EST_NRO_IDEN, C.CRA_NOMBRE,B.EST_COD, D.DEP_NOMBRE, A.ema_ano,A.EMA_PER, E.CRE_RESOLUCION ";
                $cadena_sql.=" , E.CRE_VALOR_INDIVIDUAL ,E.CRE_VALOR_INDIVIDUAL ";
                	break;

		}
		
		return $cadena_sql;

	}
}
?>
