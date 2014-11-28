<?php

namespace arka\catalogo;
use SoapFault;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");



// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion

class Persistencia {
    
    var $sql;
    var $funcion;
    var $lenguaje;
    var $ruta;
    var $miConfigurador;
    var $error;
    var $miRecursoDB;
    var $crypto;
    var $soap;
    
    function delete($conexion,$tablaNombre,$where){
    
    	$errores[1] = utf8_encode($this->lenguaje->getCadena('errorParametro'));
    	$e = new SoapFault("1", $errores[1]);
    	if($tablaNombre==null||$tablaNombre==""||$where==null||$where==""){
    		return $e->getMessage();
    	}
    
    	$errores[2] = utf8_encode($this->lenguaje->getCadena('errorConexion'));
    	$e = new SoapFault("2", $errores[2]);
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    	if (!$esteRecursoDB) {
    		//Este se considera un error fatal
    		return $e->getMessage();
    		exit;
    	}
    
    	$sqlDelete = "DELETE FROM ".$tablaNombre." WHERE ".$where;
    
    	$delete = $esteRecursoDB->ejecutarAcceso($sqlDelete);
    	
    	$errores[3] = utf8_encode($this->lenguaje->getCadena('errorEliminar'));
    	$e = new SoapFault("3", $errores[3]);
    	if($delete ==  false) return $e->getMessage();
    
    	return $delete;
    
    
    
    }
    
    function read($conexion,$query){
    
    	$errores[1] = utf8_encode($this->lenguaje->getCadena('errorParametro'));
    	$e = new SoapFault("1", $errores[1]);
    	if($query==null||$query==""){
    		return $e->getMessage();
    	}
    
    	$errores[2] = utf8_encode($this->lenguaje->getCadena('errorConexion'));
    	$e = new SoapFault("2", $errores[2]);
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    	if (!$esteRecursoDB) {
    		//Este se considera un error fatal
    		return $e->getMessage();
    		exit;
    	}
    
    	$consulta = $esteRecursoDB->ejecutarAcceso($query,"busqueda");
    	$errores[3] = utf8_encode($this->lenguaje->getCadena('errorConsulta'));
    	$e = new SoapFault("3", $errores[3]);
    	if($consulta ==  false) return $e->getMessage();
    
    	return $consulta;
    
    }
    
    function update($conexion,$tablaNombre,$arrayFields,$arrayValues,$where){
    
    	$errores[0] = utf8_encode($this->lenguaje->getCadena('errorElementosArray'));;
    	$e = new SoapFault("0", $errores[0]);
    	if(count($arrayFields)!=count($arrayValues)){
    		return $e->getMessage();
    	}
    
    	$errores[1] = utf8_encode($this->lenguaje->getCadena('errorParametro'));
    	$e = new SoapFault("1", $errores[1]);
    	if($arrayFields==null||$arrayValues==null||$tablaNombre==null||$tablaNombre==""||$where==null||$where==""){
    		return $e->getMessage();
    	}
    
    	$errores[2] = utf8_encode($this->lenguaje->getCadena('errorConexion'));
    	$e = new SoapFault("2", $errores[2]);
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    	if (!$esteRecursoDB) {
    		//Este se considera un error fatal
    		return $e->getMessage();
    		exit;
    	}
    
    	$sqlUpdate = "UPDATE ".$tablaNombre." ";
    	$sqlUpdate .=" SET ";
    
    	$setArray = array();
    
    	for ($i=0;$i<count($arrayFields);$i++){
    			
    		//verifica que el valor halla cambiado
    		$consultaValor = "SELECT ".$arrayFields[$i]." FROM ".$tablaNombre." WHERE ".$where;
    		$consulta = $esteRecursoDB->ejecutarAcceso($consultaValor,"busqueda");
    			
    		if (strrpos($arrayValues[$i], "'") != false) $value ="'".$consulta[0][0]."'";
    		elseif (strrpos($arrayValues[$i], '"') != false) $value ='"'.$consulta[0][0]."'";
    		else $value = $consulta[0][0];
    			
    		if($consulta!=false&&$arrayValues[$i]!=$value){
    			//Aun n consulta el cambio por las comillas
    			array_push($setArray,$arrayFields[$i]."=".$arrayValues[$i]);
    			//array_push($setArray,$value);
    		}
    
    	}
    
    	$sqlUpdate .= implode(",",$setArray);
    	$sqlUpdate .=" WHERE ".$where;
    
    	
    
    	$update = $esteRecursoDB->ejecutarAcceso($sqlUpdate);
    
    	$errores[3] = utf8_encode($this->lenguaje->getCadena('errorActualizar'));
    	$e = new SoapFault("3", $errores[3]);
    	if($update ==  false) return $e->getMessage();
    
    	return $update;
    
    }
    
    
    function create($conexion,$tablaNombre,$arrayFields,$arrayValues){
    
    	//-----------Nota---------------------------------------------------------------------
    	//si el campo es tipo char, string, etc
    	//es necesario ponerle comillas a los valores, ejemplo, un array de cadenas a insertar seria
    	//array("'valor1'","'valor2'","'valor3'")
    	//Algo similar hay que hacer con los nombres de las tablas
    	//algunas pueden necesitar comillas dobles para ser interpretadas
    	//por lo cual el nombre de la tabla se asignaría de la siguiente manera
    	//'"nombreTabla"'
    	//------------------------------------------------------------------------------------
    
    		
    	//Hay problemas con el objeto lenguaje
    
    	//$exitoString = utf8_encode($this->lenguaje->getCadena("resistroExito"));
    	//$errores[0] =utf8_encode($this->lenguaje->getCadena("errorLongitudArray"));
    	$errores[0] = utf8_encode($this->lenguaje->getCadena('errorElementosArray'));
    	$e = new SoapFault("0", $errores[0]);
    	if(count($arrayFields)!=count($arrayValues)){
    		return $e->getMessage();
    	}
    
    	$errores[1] = utf8_encode($this->lenguaje->getCadena('errorParametro'));
    	$e = new SoapFault("1", $errores[1]);
    	if($arrayFields==null||$arrayValues==null||$tablaNombre==null||$tablaNombre==""){
    		return $e->getMessage();
    	}
    
    	$errores[2] = utf8_encode($this->lenguaje->getCadena('errorConexion'));
    	$e = new SoapFault("2", $errores[2]);
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    	if (!$esteRecursoDB) {
    		//Este se considera un error fatal
    		return $e->getMessage();
    		exit;
    	}
    
    	$sqlInsert = "INSERT INTO ".$tablaNombre." ( ";
    	$sqlInsert .= implode(",",$arrayFields);
    	$sqlInsert .= " ) VALUES (";
    	$sqlInsert .= implode(",",$arrayValues);
    	$sqlInsert .= " )";
    
    	//return $sqlInsert;
    
    	$insert = $esteRecursoDB->ejecutarAcceso($sqlInsert);
    
    	$errores[3] = utf8_encode($this->lenguaje->getCadena('errorConexion'));
    	$e = new SoapFault("3", $errores[3]);
    	if($insert ==  null) return $e->getMessage();
    
    	return $insert;
    
    }
    
    
    
    
    function __construct() {
        
        $this->miConfigurador = \Configurador::singleton ();
        
        $this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
        
        $this->miMensaje = \Mensaje::singleton ();
        
        $conexion = "aplicativo";
        $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        
        if (! $this->miRecursoDB) {
            
            $this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
            $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        }
        
        $url=$this->miConfigurador->getVariableConfiguracion("host");
        $url.=$this->miConfigurador->getVariableConfiguracion("site");
        $url.="/index.php?";
        
           
    
    }
    
    public function setRuta($unaRuta) {
        $this->ruta = $unaRuta;
    }
    
    public function setSql($a) {
        $this->sql = $a;
    }
    
    public function setFuncion($funcion) {
        $this->funcion = $funcion;
    }
    
    public function setLenguaje($lenguaje) {
        $this->lenguaje = $lenguaje;
    }
    
    public function setFormulario($formulario) {
        $this->formulario = $formulario;
    }

}

?>
