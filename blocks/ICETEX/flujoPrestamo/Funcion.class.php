<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}



include_once("core/manager/Configurador.class.php");
include_once("core/builder/InspectorHTML.class.php");
include_once("core/builder/Mensaje.class.php");
include_once("core/crypto/Encriptador.class.php");
include_once("core/auth/Sesion.class.php");

//mail
include("classes/mail/class.phpmailer.php");
include("classes/mail/class.smtp.php");

//Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
//metodos mas utilizados en la aplicacion

//Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
//en camel case precedido por la palabra Funcion

class FuncionflujoPrestamo
{

	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	var $reg;
	var $titulo;
	var $valorOrdinaria;
	var $valorExtraOrdinaria;
	var $fechaOrdinaria;
	var $fechaExtraOrdinaria;
	var $proyectoCurricular;
	var $anoPago;
	var $periodoPago;
	var $estado;
	var $aprobado;
	var $rutaArchivo;
	var $listadoCodigos;
	


	function verificarCampos(){
		include_once($this->ruta."/funcion/verificarCampos.php");
		if($this->error==true){
			return false;
		}else{
			return true;
		}


	}
	
	function procesarHoja($indexName,$objPHPExcelReader){
		include($this->ruta."/funcion/procesarHoja.php");
	}
	
	function validateDate($date)
	{
		$d = DateTime::createFromFormat('j/m/y', $date);
        return $d && $d->format('j/m/y') == $date;
	}

	function mostrarTabla()
	{
		include($this->ruta."/funcion/mostrarTabla.php");
	}
	
	function workflow()
	{
		include($this->ruta."/funcion/workflow.php");
	}
	
	function crearSolicitud()
	{
		include($this->ruta."/funcion/crearSolicitud.php");
	}
	
	function crearSolicitudEstudiante()
	{
		include($this->ruta."/funcion/crearSolicitudEstudiante.php");
	}
	
	function solicitarCredito()
	{
		include($this->ruta."/funcion/solicitarCredito.php");
	}
	
	function solicitarCreditoEstudiante(){
		include($this->ruta."/funcion/solicitarCreditoEstudiante.php");
	}function cancelarCreditoEstudiante(){
		include($this->ruta."/funcion/cancelarCreditoEstudiante.php");
	}
	
	function desactivarRecibosActuales(){
		include($this->ruta."/funcion/desactivarRecibosActuales.php");
	}
	
	function separarMatricula()	{
		include($this->ruta."/funcion/separarMatricula.php");
	}
	
	function actualizarEstadoFlujo(){
		include($this->ruta."/funcion/actualizarEstadoFlujo.php");
	}
	
	function verificarPagoRecibo()
	{
		include($this->ruta."/funcion/verificarPagoRecibo.php");
	}
	
	function aprobarCredito(){
		include($this->ruta."/funcion/aprobarCredito.php");
	
	}
	
	function registroAprobado(){
		include($this->ruta."/funcion/registroAprobado.php");
	
	}
	
	function creditoNegado(){
		include($this->ruta."/funcion/creditoNegado.php");
	
	}
	
	function formularioResolucion(){
		include($this->ruta."/funcion/formularioResolucion.php");
	}
	
	function registroResolucion(){
		include($this->ruta."/funcion/registroResolucion.php");
	}
	
	function moverArchivo(){
		include($this->ruta."/funcion/moverArchivo.php");
	}
	function aprobarSolicitud(){
		include($this->ruta."/funcion/aprobarSolicitud.php");
	}
	
	function notificarEstudiante($cuerpo, $tema , $adjuntos='', $temaRegistro = ''){
		include($this->ruta."/funcion/notificarEstudiante.php");
	}
	function notificacionVerificar(){
		include($this->ruta."/funcion/notificacionVerificar.php");
	}
	function notificacionExitosa(){
		include($this->ruta."/funcion/notificacionExitosa.php");
	}
	function notificacionError(){
		include($this->ruta."/funcion/notificacionError.php");
	}
	function solicitarReintegro(){
		include($this->ruta."/funcion/solicitarReintegro.php");
	}
	
	function registroReintegro(){
		include($this->ruta."/funcion/registroReintegro.php");
		
	}
	function formularioContable(){
		include($this->ruta."/funcion/formularioContable.php");
	
	}
	function registroContable(){
		include($this->ruta."/funcion/registroContable.php");
	}
	function mensajeReasignacion(){
		include($this->ruta."/funcion/mensajeReasignacion.php");
		
	}function mensajePendiente(){
		include($this->ruta."/funcion/mensajePendiente.php");
		
	}function mensajeReintegro(){
		include($this->ruta."/funcion/mensajeReintegro.php");
		
	}function consultarHistorico(){
		include($this->ruta."/funcion/consultarHistorico.php");
		
	}function permisosWorkflow($opt){
		include($this->ruta."/funcion/permisosWorkflow.php");
		
	}function notificacionRecibos(){
		include($this->ruta."/funcion/notificacionRecibos.php");
		
	}function revisarPagoMatricula(){
		include($this->ruta."/funcion/revisarPagoMatricula.php");
		
	}function mensajeResolucion(){
		include($this->ruta."/funcion/mensajeResolucion.php");
		
	}function cargarInterfazTab2(){
		include($this->ruta."/funcion/cargarInterfazTab2.php");
		
	}function mensajeLegalizado(){
		include($this->ruta."/funcion/mensajeLegalizado.php");
		
	}function procesarExcelCodigos(){
		include($this->ruta."/funcion/procesarExcelCodigos.php");
		
	}function notificarTesoreria($lista,$cuerpo ='', $tema='', $adjuntos='',$temaRegistro=''){
		include($this->ruta."/funcion/notificarTesoreria.php");
		
	}function notificarBienestar($cuerpo ='', $tema='', $adjuntos='',$temaRegistro=''){
		include($this->ruta."/funcion/notificarBienestar.php");
		
	}
	function enviarMarcas(){
		include($this->ruta."/funcion/registroMarcas.php");
	}function procesarExcelMarcas(){
		include($this->ruta."/funcion/procesarExcelMarcas.php");
	}function registroLog($accion){
		include($this->ruta."/funcion/registroLog.php");
	}function revisarResolucionRegistrada(){
		include($this->ruta."/funcion/revisarResolucionRegistrada.php");
		
	}function crearExcelTesoreriaResolucion($lista){
		include($this->ruta."/funcion/crearExcelTesoreriaResolucion.php");
		return $rutaExcel;
		
	}
	
	
	
	
	
	
	function consultarEstudiante(){
		include_once($this->ruta."/funcion/consultarEstudiante.php");
	}
	
	function consultarUsuario(){
		if($_REQUEST['metodo']=='operacion') include_once($this->ruta."/funcion/consultarUsuarioOperacion.php");
		else include_once($this->ruta."/funcion/consultarUsuarioInterfaz.php");
	}
	
	function crearRegistro($parametros){
		include($this->ruta."/funcion/crearRegistro.php");
	}
	function identificacionACodigo($identificacion){
		include($this->ruta."/funcion/identificacionACodigo.php");
		return $codigo;
	}

	
	
	
	

	

	function action()
	{
		
		//Valida si la sesion esta permitida
		include_once($this->ruta."/funcion/validarSession.php");

		//Evitar que se ingrese codigo HTML y PHP en los campos de texto
		//Campos que se quieren excluir de la limpieza de código. Formato: nombreCampo1|nombreCampo2|nombreCampo3
		$excluir="";
		$_REQUEST=$this->miInspectorHTML->limpiarPHPHTML($_REQUEST);

		//Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
		//aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
		//se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
		//en la carpeta funcion

		//Importante: Es adecuado que sea una variable llamada opcion o action la que guie el procesamiento:
		
		
		if(isset($_REQUEST["procesarAjax"])&&$_REQUEST["procesarAjax"]==true&&isset($_REQUEST["funcion"])){

			//Realizar una validación específica para los campos de este formulario:
			//la siguiente linea no deber�a estar comentada, en esta plantilla no se verifica lo enviado 
			//pero siempre se deben validar los campos. 
			$validacion=$this->verificarCampos();
			//$validacion=true;
			
			if($validacion==false){
				//Instanciar a la clase pagina con mensaje de correcion de datos
				exit;
				echo json_encode("Datos Incorrectos");

			}else{
				//Validar las variables para evitar un tipo  insercion de SQL
				$_REQUEST=$this->miInspectorHTML->limpiarSQL($_REQUEST);
				switch($_REQUEST["funcion"]){
					case "cargarInterfazTab2":
						$this->cargarInterfazTab2();
						break;
					case "enviarMarcas":
						$this->enviarMarcas();
						break;
					case "mensajeRecibos":
						$this->notificacionVerificar();						
						break;
					case "consultarHistorico";
					$this->consultarHistorico();
					break;
					case "registroContable":
						$this->registroContable();
						break;
					case "registroReintegro":
						$this->registroReintegro();
						break;
					case "registroResolucion":
						$this->registroResolucion();
						break;
					case "obtenerDeudasInterfaz":
						$this->obtenerDeudas();
						break;
					case "solicitarCredito":
							$this->solicitarCredito();
							break;
					case "solicitarCreditoEstudiante":
							$this->solicitarCreditoEstudiante();
							break;
					case "cancelarCreditoEstudiante":
							$this->cancelarCreditoEstudiante();
							break;
					case "editarDeudas":
						
								if($_REQUEST['metodo']=='operacion'||$_REQUEST['metodo']=='interfaz'||!isset($_REQUEST['metodo'])){
									$this->consultarUsuario();
								}elseif($_REQUEST['metodo']=='nuevo'){									
									$this->nuevoDeuda();
								}elseif($_REQUEST['metodo']=='crear'){
									$this->crearDeuda();
								}
								
								break;
					case "cancelarCredito":
						$this->aprobado = 'N';
								$this->registroAprobado();
								break;
					case "aprobarCredito":
						$this->aprobado = 'S';
								$this->registroAprobado();
								break;
				}			
					
				
			}
		}
		
	
	}


	function __construct()
	{

		$this->miConfigurador=Configurador::singleton();

		$this->miInspectorHTML=InspectorHTML::singleton();
			
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");		
		
		$this->miMensaje=Mensaje::singleton();
		
		$this->miSesion=Sesion::singleton();
		
		$conexion="aplicativo";
		$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
		
		if(!$this->miRecursoDB){
		
			$this->miConfigurador->fabricaConexiones->setRecursoDB($conexion,"tabla");
			$this->miRecursoDB=$this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);			
		}
		
		
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	function setSql($a)
	{
		$this->sql=$a;
	}

	function setFuncion($funcion)
	{
		$this->funcion=$funcion;
	}

	public function setLenguaje($lenguaje)
	{
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}

}
?>
