<?php

namespace reglas;


if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("Mensaje.class.php");


//esta clas eesta hecha para trabajar con postgres
// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion

class Persistencia {
    
    private $miConfigurador;
    private $miRecursoDB;
    private $historico;
    private $conexion;
    private $usuario;
    private $tabla;
    private $tablaNombre;
    private $esquema;
    private $prefijoColumna;
    private $query;
    private $arrayColumnas;
    private $columnasHistorico;
    public $mensaje;
    
    
    function __construct($conexion = 'estructura' , $tabla = '' , $historico = false , $usuario = '') {
    
    	$this->miConfigurador = \Configurador::singleton ();
    
    	$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
    
    	$this->tabla =  $tabla;
    	$this->historico =  $historico;
    	if($usuario==''||is_null($usuario)) $this->usuario = '_indefinido_';
    	else $this->usuario =  $usuario;
    	$this->mensaje =  new Mensaje();
    	$this->saltarHistorico = false;
    	if (! $this->miRecursoDB) {
    
    		$this->mensaje->addMensaje("2","errorConexion",'error');
    		return false;
    	}
    
    	
    }
    
    public function getQuery(){
    	return $this->query;
    }
    
    public function setQuery($query){
    	 $this->query = $query;
    }
    
    
    public function setHistorico($historico =  false){
    	$this->historico = $historico;
    }
    
    public function setConexion($conexion =  'estructura'){
    	$this->conexion = $conexion;
    }
    
    public function setUsuario($usuario){
    	$this->usuario = $usuario;
    }
    
    public function setTabla($tabla){
    	$this->tabla = $tabla;
    }
    
    private function validarConexion(){
    	
    	if (!$this->miRecursoDB) {
    		$this->mensaje->addMensaje("1","errorConexion",'error');
    		return false;
    	}
    	return true;
    }
    
    //esto debe depender del motor de base de datos
    //proximamente incluir switch
    //el select por ahora esta hecho para Postgres
    private function probarTabla(){
    	if($this->validarConexion()){
    		$query = "SELECT '".$this->tabla."'::regclass";
    		$consulta = $this->miRecursoDB->ejecutarAcceso($query,"busqueda");
    
    		if($consulta ==  false){
    			$this->mensaje->addMensaje("2","errorTablaNoExiste",'error');
    			return false;
    		}
    
    		return true;
    	}
    	return false;
    	 
    }
    
    private function validarHistorico(){
    	
    	if($this->validarConexion()&&$this->historico){
    		$query = "SELECT '".$this->tabla."_h'::regclass";
    		$consulta = $this->miRecursoDB->ejecutarAcceso($query,"busqueda");
  			 	
    		if($consulta ==  false){
    			$this->mensaje->addMensaje("3","errorTablaHNoExiste",'error');
    			return false;
    		}
    	
    		return true;
    	}
    	return false;
    	
    }
    
    private function validarCampos($campos){
    	if(!is_array($campos)||$campos==null){
    		$this->mensaje->addMensaje("4","errorCampos",'error');
    		return false;
    	}
    	return true;
    }
    
    private function validarValores($valores){
    	if(!is_array($valores)||$valores==null){
    		$this->mensaje->addMensaje("4","errorValores",'error');
    		return false;
    	}
    	return true;
    }
    
    private function validarWhere($where){
    	if($where!=null&&($where==""||$where=='')){
    		$this->mensaje->addMensaje("5","errorWhere",'error');
    		return false;
    	}
    	return true;
    }
    
    private function compararValoresCampos($campos, $valores){
    	if(count($campos)!=count($valores)){
    		$this->mensaje->addMensaje("6","errorElementosArray",'error');
    		return false;
    	}
    	return true;
    }
    
    public function ejecutar($opcion = ''){
    	if($opcion!=''&&!is_null($opcion))	return $this->miRecursoDB->ejecutarAcceso($this->query,$opcion);
    	return $this->miRecursoDB->ejecutarAcceso($this->query);
    }
    
    function delete($where = null){
    
    	if($this->probarTabla()&&
    	  $this->validarWhere($where)){
    		
	        $this->setQuery("DELETE FROM ".$this->tabla." WHERE ".$where);
	    	$delete = $this->ejecutar() ;
	          if($delete ==  false){
	           	$this->mensaje->addMensaje("7","errorEliminar",'error');
	           	return false;
	           }
	           
	           return $delete;
	           
	    	}
	    	
	    	return false;
    
    }
    
    function read($fields , $where = null){
        
    	if($this->probarTabla()&&
    	  $this->validarCampos($fields)){
    		
    		$query = "SELECT ".implode(",",$fields);
    		$query .= " FROM ".$this->tabla;
    		if(!is_null($where)&&$where!='')$query .=" WHERE ".$where;
    		$this->setQuery($query);
    		$consulta = $this->ejecutar("busqueda") ;
    		if($consulta ==  false){
    			$this->mensaje->addMensaje("8","errorConsulta",'error');
    			return false;
    			
    		}
    		
    		return $consulta;
    		
    	}
    	return false;
    	
    	
    }
    
    //-----------Nota---------------------------------------------------------------------
    //si el campo es tipo char, string, etc
    //es necesario ponerle comillas a los valores, ejemplo, un array de cadenas a insertar seria
    //array("'valor1'","'valor2'","'valor3'")
    //Algo similar hay que hacer con los nombres de las tablas
    //algunas pueden necesitar comillas dobles para ser interpretadas
    //por lo cual el nombre de la tabla se asignaría de la siguiente manera
    //'"nombreTabla"'
    //------------------------------------------------------------------------------------
    
    
    function create($arrayFields,$arrayValues){
    
    	if($this->probarTabla()&&
    	$this->validarCampos($arrayFields)&&
    	$this->validarValores($arrayValues)&&
    	$this->compararValoresCampos($arrayFields, $arrayValues)){
    	

    		$sqlInsert = "INSERT INTO ".$this->tabla." ( ";
    		$sqlInsert .= implode(",",$arrayFields);
    		$sqlInsert .= " ) VALUES (";
    		$sqlInsert .= implode(",",$arrayValues);
    		$sqlInsert .= " )";
    		$this->setQuery($sqlInsert);
    		
    		$insert = $this->ejecutar() ;
    		if($insert ==  false){
    			$this->mensaje->addMensaje("9","errorInsertar",'error');
    			return false;
    		}
    		
    		
    		if(!$this->historico($arrayFields,$arrayValues)) return false;
    		
    		
    		
    		return $insert;
    		 
    		
    	}
    	
    	return false;
    
    	
    }
    
    
    function update($arrayFields,$arrayValues,$where= null){
    
    	if($this->probarTabla()&&
    	  $this->validarCampos($arrayFields)&&
    	  $this->validarValores($arrayValues)&&
    	  $this->validarWhere($where)&&
    	  $this->compararValoresCampos($arrayFields, $arrayValues)){
    		
    		if(!$this->contarRegistros($where)) return false;
    		
    		$pkArray = $this->readPks($where);
    		
    		if(!is_array($pkArray)) return false;
    		
    		
    		$sqlUpdate = "UPDATE ".$this->tabla." ";
    		$sqlUpdate .=" SET ";
    		
    		
    		
    		foreach ($pkArray["lista"] as $idsPk){
    			$setArray = array();
    			foreach ($idsPk as $a=>$b){
    			  $strWherePk = $a."=".$b;
    			  if($b!=end($idsPk)) $strWherePk .=" AND ";
    			}
	    		for ($i=0;$i<count($arrayFields);$i++){
	    			 
	    			//verifica que el valor halla cambiado
	    			$consulta = $this->read(array($arrayFields[$i]),$strWherePk);
	    			
	    		    if(is_array($consulta)){
	    		      foreach ($consulta as $con){
	    		      	
	    		      	if (strrpos($arrayValues[$i], "'") != false) $value ="'".$con[0]."'";
	    		      	elseif (strrpos($arrayValues[$i], '"') != false) $value ='"'.$con[0].'"';
	    		      	else $value = $con[0];
	    		      	
	    		      	if($arrayValues[$i]!=$value) $setArray[] =$arrayFields[$i]."=".$arrayValues[$i];
	    		      	
	    		      }	
	    		    }
	    			
	    		
	    		}
    		    
	    		if(count($setArray)==0){
	    			$this->mensaje->addMensaje("10","errorNoActualizar",'warning');
	    		}
	    		
	    		$sqlUpdate .= implode(",",$setArray);
	    		$sqlUpdate .=" WHERE ".$strWherePk;
	    		
	    		$this->setQuery($sqlUpdate);
	    		 
	    		$update = $this->ejecutar() ;
	    		if($update ==  false){
	    			$this->mensaje->addMensaje("11","errorActualizar",'error');
	    			return false;
	    		}
	
	    		if(!$this->historico($arrayFields,$arrayValues,$strWherePk)) return false;
	    		
	    		
    	 }
    	 return true;
    		 
    	}
    	
    	return false;
    	
    
    	
    }
    
     
    public function getListaColumnas($excluidos=null){
    	
    	if($this->probarTabla()){
    		
    		$this->recuperarTablaEsquema();
    		
    		$query = "SELECT column_name, data_type , is_nullable ";
    		$query .=" FROM information_schema.columns ";
    		$query .=" WHERE table_schema = '".$this->esquema."' ";
    		$query .=" AND table_name   = '".$this->tablaNombre."' ";
    		
    		if(!is_null($excluidos)&&is_array($excluidos)){
    			$query .=" AND column_name not in (".implode(",",$excluidos).") ";
    		}
    		
    		$this->setQuery($query);
    		$columnas = $this->ejecutar("busqueda");
    		
    		if($columnas ==  false){
    			$this->mensaje->addMensaje("12","errorColumnas",'error');
    			return false;
    		}
    		
    		
    		$cols = array();
    		$colsh = array();
    		foreach($columnas as $c){
    			$cols[] = $c[0];
    			if($this->historico)$colsh[] = $c[0]."_h";
    		}
    		
    		$this->arrayColumnas =  $columnas;
    		
    		if($this->historico) {
    			$colsh [] = $this->prefijoColumna."_usuario";
    			$this->columnasHistorico =  $colsh;
    		}
    		
    		return $cols;
    		
    	}
    	
    	return false;
    }
    
    private function insertarHistorico($valores){
    	// si se va a colocar esta funcion publica debe hacer validaciones
    	// de los valores de entrada
    	
    	$valores[] = $this->usuario;
    	  	
    	$sqlInsert = "INSERT INTO ".$this->tabla."_h ( ";
    	$sqlInsert .= implode(",",$this->columnasHistorico);
    	$sqlInsert .= " ) VALUES (";
    	$sqlInsert .= implode(",",$valores);
    	$sqlInsert .= " )";
    	$this->setQuery($sqlInsert);
    	
    	$inserth = $this->ejecutar() ;
    	
    	if($inserth ==  false){
    		$this->mensaje->addMensaje("13","errorHistorico",'error');
    		return false;
    	}
    	
    	
    	return $inserth;
    	 
    }
    
    private function recuperarTablaEsquema(){
    	//recupera tabla y esquema
    	$arrNombre = explode (".",$this->tabla);
    	if(count($arrNombre)>0){
    		$esquema = $arrNombre[0];
    		$tabla =   $arrNombre[1];
    	}else {
    		$esquema = 'public';
    		$tabla = $this->tabla;
    	}
    	
    	$this->tablaNombre = $tabla;
    	$this->prefijoColumna = substr($this->tablaNombre,0,3);
    	$this->esquema = $esquema;
    }
    
    private function historico($arrayFields , $arrayValues ,$where = null){
    	//tener en cuenta que si va a ser publica ha que agregar mas validaciones
    	
    	if($this->historico){
    		
    		
    		//si el where no existe lo crea
    		if(is_null($where)||$where==''){
    			for ($i=0;$i<count($arrayFields);$i++){
    				 
    				$where .=" ".$arrayFields[$i]." = ".$arrayValues[$i];
    				if($arrayFields[$i]!= end($arrayFields)) $where .=" AND ";
    			}
    			 	
    		}
    			
    		$consultah = $this->read($this->getListaColumnas(),$where);
    		 
    		if(is_array($consultah)){
    			$valores = array();
    			foreach($consultah[0] as $a => $b){
    				if(is_numeric($a)) $valores[] = "'".$b."'";
    			}
    			
    			
    			if(!$this->insertarHistorico($valores))	return false;
    			
    			return true;
    				
    		}
    		
    		return false;
    		 
    		 
    	}
    	return false;
    }
    
    private function getPks(){
    	//si se va a ahcer publica validar la tabla
    	//esta funcion esta hecha solo para postgres
    	$sql = "SELECT ";
    	$sql .= " pg_attribute.attname, ";
    	$sql .= " format_type(pg_attribute.atttypid, pg_attribute.atttypmod) ";
    	$sql .= " FROM pg_index, pg_class, pg_attribute " ;
    	$sql .= " WHERE ";
    	$sql .= " pg_class.oid = '".$this->tabla."'::regclass AND ";
    	$sql .= " indrelid = pg_class.oid AND ";
    	$sql .= " pg_attribute.attrelid = pg_class.oid AND ";
    	$sql .= " pg_attribute.attnum = any(pg_index.indkey) ";
    	$sql .= " AND indisprimary ";
    	
    	$this->setQuery($sql);
    	 
    	$cpk = $this->ejecutar("busqueda") ;
    	$listaPk = array();
    	
    	if(is_array($cpk)){
    		foreach ($cpk as $c) $listaPk[] = $c[0];
    		
    		return $listaPk;
    	}
    	$this->mensaje->addMensaje("14","errorPks",'error');
    	return false;
    	 
    	
    }
    
    public function readPks($where=null){
    	
    	$listaPks = $this->getPks();
    	
    	$consulta =$this->read($listaPks,$where);
    	$nWhere = '';
    	$nombres = array();
    	$valores = array();
    	if(!$consulta)
    		return false;
    	foreach ($consulta as &$fila){
    		foreach ($fila as $a=>$b){
    			if(is_numeric($a))	unset ($fila[$a]);
    			else{
    				$nWhere .=$a.'='.$b;
    				$nombres[] = $a;
    				$valores[] = $b;
    				if($b!=end($fila)) $nWhere .= ' AND ';
    			}
    			
    		}
    		if(end($consulta)!=$fila) $nWhere .= ' OR ';
    	}
    	return array("lista"=>$consulta,"nombres"=>$nombres, "valores"=>$valores,"where"=>$nWhere);
    	
    }
    
    public function contarRegistros($where=null){
    	$query = "SELECT COUNT(*) FROM ".$this->tabla;
    	if($this->validarWhere($where)) $query .=" WHERE ".$where;
    	$this->setQuery($query);
    	$conteo = $this->ejecutar("busqueda");
    	if(is_array($conteo)) return $conteo[0][0];
    	$this->mensaje->addMensaje("15","errorConteo",'warning');
    	return false; 
    	
    	
    }
    
    public function getprefijoColumna(){
    	$this->recuperarTablaEsquema();
    	return $this->prefijoColumna;
    }
    
            
    
}

?>
