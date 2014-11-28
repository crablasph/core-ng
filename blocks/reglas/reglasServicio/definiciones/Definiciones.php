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
	 * @param string $valor Valor, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return bool Respuesta
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
	 * @param string $valor Valor, Opcional
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
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return array Respuesta
	 */
	function consultarParametro($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
		
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
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $tipo Tipo Variable, entero Obligatorio
	 * @param string $valor Valor, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return bool Respuesta
	 */
	function crearVariable($nombre ='',$descripcion='',$proceso='',$rango = '',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarVariable($id = '',$nombre ='',$descripcion='',$proceso='',$rango = '',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return array Respuesta
	 */
	function consultarVariable($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
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
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $tipo Tipo Variable, entero Obligatorio
	 * @param string $valor Valor, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return bool Respuesta
	 */
	function crearFuncion($nombre ='',$descripcion='',$proceso='',$rango = '',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, cadena de caracteres Obligatorio
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param string $rango rango, cadena Obligatorio
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarFuncion($id = '',$nombre ='',$descripcion='',$proceso='',$rango = '',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return array Respuesta
	 */
	function consultarFuncion($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
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
	 * @param string $valor Valor, Obligatorio
	 * @param integer $estado Estado, entero Obligatorio
	 * @return bool Respuesta
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
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return bool Respuesta
	 */
	function actualizarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Identificador, entero Opcional
	 * @param string $nombre Nombre, cadena de caracteres Opcional
	 * @param string $descripcion Descripcion, Opcional
	 * @param integer $proceso IdProceso, entero Opcional
	 * @param integer $tipo Tipo Variable, entero Opcional
	 * @param string $valor Valor, Opcional
	 * @param integer $estado Estado, entero Opcional
	 * @return array Respuesta
	 */
	function consultarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
	
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
	
	/////////////USUARIOS//////////////////////////////////////////
	
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
	 * @return bool Respuesta
	 */
	function actualizarRelacion($id = '' , $usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
	
	}
	
	
	/**
	 *
	 *
	 * @param integer $id Id Registro Usuario, entero Obligatorio
	 * @param integer $usuario Id Usuario, entero Opcional
	 * @param integer $objeto Id Objeto, entero Opcional
	 * @param integer $registro Id Registro, entero Opcional
	 * @param integer $permiso Id Pemiso, entero Opcional
	 * @param integer $estado Estado, Opcional
	 * @return bool Respuesta
	 */
	function consultarRelacion($id = '',$usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
	
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
	
	
	

?>
