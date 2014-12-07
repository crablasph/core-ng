<?php
namespace reglas\reglasServicio;


if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


    
    
     /////////////PARAMETROS//////////////////////////////////////////
	
	/**
	 *
	 *
	 * @param string $nombre Nombre, cadena de caracteres Obligatorio
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Obligatorio
	 * @param integer $tipo Tipo Variable, entero Obligatorio
	 * @param string $valor Valor codificado base64, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return integer Respuesta
	 */
	function crearParametro($nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
		
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor codificado base64, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarParametro($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
		
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @param string $fecha Fecha, cadena Opcional
	 * @return array Respuesta
	 */
	function consultarParametro($id = '',$nombre ='',$proceso='',$tipo = '',$estado='',$fecha=''){
		
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function activarInactivarParametro($id = ''){
		
	}
	
	

	/////////////VARIABLES//////////////////////////////////////////
	
	/**
	 *
	 *
	 * @param string $nombre Nombre, cadena de caracteres Obligatorio
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Obligatorio
	 * @param integer $tipo Tipo Variable, entero Obligatorio
	 * @param string $rango rango, cadena Obligatorio
	 * @param string $valor Valor codificado base64, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return integer Respuesta
	 */
	function crearVariable($nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $rango rango, cadena Obligatorio
	 * @param string $valor Valor codificado base64, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarVariable($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @param string $fecha Fecha, cadena Opcional
	 * @return array Respuesta
	 */
	function consultarVariable($id = '',$nombre ='',$proceso='',$tipo = '',$estado='',$fecha=''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function activarInactivarVariable($id = ''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function duplicarVariable($id = ''){
	
	}
	
	
	
	/////////////FUNCIONES//////////////////////////////////////////
	
	/**
	 *
	 *
	 * @param string $nombre Nombre, cadena de caracteres Obligatorio
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Obligatorio
	 * @param integer $tipo Tipo Funcion, entero Obligatorio
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $categoria categoria funcion, entero Obligatorio
	 * @param string $ruta lugar de ejecucion codificado base64, cadena Obligatorio
	 * @param string $valor Valor codificado base64, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return integer Respuesta
	 */
	function crearFuncion($nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$categoria = '',$ruta='',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo funcion, entero Opcional
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $categoria categoria funcion, entero opcional
	 * @param string $ruta lugar de ejecucion codificado base64, cadena opcional
	 * @param string $valor Valor codificado base64, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarFuncion($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$categoria = '',$ruta='',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @param string $fecha Fecha, cadena Opcional
	 * @return array Respuesta
	 */
	function consultarFuncion($id = '',$nombre ='',$proceso='',$tipo = '',$estado='',$fecha=''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function activarInactivarFuncion($id = ''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function duplicarFuncion($id = ''){
	
	}

	
	/////////////REGLAS//////////////////////////////////////////
	
	/**
	 *
	 *
	 * @param string $nombre Nombre, cadena de caracteres Obligatorio
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Obligatorio
	 * @param integer $tipo Tipo Variable, entero Obligatorio
	 * @param string $valor Valor codificado base64, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return integer Respuesta
	 */
	function crearRegla($nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor codificado base64, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
	}
	
	///intervalo ejemplo 01/01/2007,01/01/2014 en formato dd/mm/yyyy
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @param string $fecha Fecha, fecha Opcional 
	 * @return array Respuesta
	 */
	function consultarRegla($id = '',$nombre ='',$proceso='',$tipo = '',$estado='', $fecha = ''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function activarInactivarRegla($id = ''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Obligatorio
	 * @return bool Respuesta
	 */
	function duplicarRegla($id = ''){
	
	}
	
	/////////////PERMISOS//////////////////////////////////////////
	
	/**
	 *
	 *
	 * @param integer $usuario Id Usuario, entero Obligatorio
	 * @param integer $objeto Id Objeto, entero Obligatorio
	 * @param integer $registro Id Registro, entero Obligatorio
	 * @param integer $permiso Id Pemiso, entero Obligatorio
	 * @param integer $estado Estado, Opcional
	 * @return bool Respuesta
	 */
	function crearRelacion($usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Id Registro Usuario, entero Obligatorio
	 * @param integer $usuario Id Usuario, entero Obligatorio
	 * @param integer $objeto Id Objeto, entero Opcional
	 * @param integer $registro Id Registro, entero Opcional
	 * @param integer $permiso Id Pemiso, entero Opcional
	 * @param integer $estado Estado, Opcional
	 * @return integer Respuesta
	 */
	function actualizarRelacion($id = '' , $usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Id Registro Usuario, entero Obligatorio
	 * @param integer $usuario Id Usuario, entero Opcional
	 * @param integer $objeto Id Objeto, entero Opcional
	 * @param integer $permiso Id Pemiso, entero Opcional
	 * @param integer $estado Estado, Opcional
	 * @param string $fecha fecha intervalo, Opcional
	 * @return bool Respuesta
	 */
	function consultarRelacion($id = '',$usuario ='',$objeto='',$permiso = '',$estado='',$fecha=''){
	
	}
	
	/**
	 *
	 *
	 * @param integer $id Id Registro Usuario, entero Obligatorio
	 * @return bool Respuesta
	 */
	function activarInactivarRelacion($id){
	
	}
	
	/////////////////////////Evaluar Regla ///////////////////////////
	

	/**
	 * @pw_complex stringArray A string array type
	 */
	/**
	 * @pw_complex stringArrayArray An array of ComplexTypeDemo
	 */
	
	
	/**
	 *
	 *
	 * @param integer $idRegla Id Registro Regla, entero Obligatorio
	 * @param stringArrayArray $valores Array de los valores de las variables ejemplo array(array("variable1",1),array("variable2",2)), array Obligatorio
	 * @param integer $idProceso id del proceso, Entero Obligatorio
	 * @return bool Respuesta
	 */
	function evaluarRegla($idRegla = '', $valores = '', $idProceso = '' ){
	
	}
	
	//eliminar
	///////////////////////////Funciones de ayuda para traer listas y parametros de la bd///////////////////
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaEstados(){
		
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaTipos(){
	
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaObjetos(){
	
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaPermisos(){
	
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaOperadores(){
	
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getListaCategorias(){
	
	}
	
	/**
	 *
	 *
	 * @void
	 * @return array Respuesta
	 */
	function getDatosColumnas(){
	
	}
	
	/**
	 *
	 * 
	 * @param integer $idObjeto Id Objeto, entero Obligatorio
	 * @return array Respuesta
	 */
	function getAtributosObjeto($idObjeto){
	
	}
	
	
	

?>
