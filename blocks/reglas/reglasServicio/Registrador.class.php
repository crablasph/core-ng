<?php

namespace reglas;


if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}


include_once ("Mensaje.class.php");
include_once ("Persistencia.class.php");

class Registrador{
	
	const limiteValor = 200;
	const limiteNombre = 150;
	const limiteDescripcion = 300;
	const estadoHistorico = true;
	const numeroCopiasMaxima = 100;
	const CONEXION = 'reglas';
	
	private $fechaBetween = '';
	private $estados;
	private $tipos;
	private $objetos;
	private $permisos;
	private $operadores;
	private $categorias;
	private $columnasTabla;
	private $miConfigurador;
	public $persistencia;
	private $tabla;
	private $prefijoColumnas;
	private $columnas;
	private $columnasNoPrefijo;
	private $excluidos;
	private $parametros;
	private $valores;
	private $indexado;
	private $usuario;
	private $where;
	private $tablaAlias;
	public $mensaje;
	public $conexion;
	
	function __construct($tabla = null) {
	
		//$this->miConfigurador = \Configurador::singleton ();
		$this->mensaje =  new Mensaje();
		$this->conexion=self::CONEXION;
		
		
		
		
		//Recupera parametros de la base de datos
		$this->recuperarEstados();
	    $this->recuperarTipos();
	    $this->recuperarObjetos();
	    $this->recuperarPermisos();
	    
	    
	    if(!is_null($tabla)&&$tabla!="") $this->setAmbiente($tabla);
	    
	
	}	
	
	public function getAtributosObjeto($idObjeto = ''){
		
		if($idObjeto == '') return false;
		$nombre = $this->getObjeto($idObjeto,'id','nombre');
		$this->persistencia =  new Persistencia($this->conexion,$nombre);
		$listaColumnas = $this->persistencia->getListaColumnas();
		$prefijo = $this->persistencia->getprefijoColumna();
		 
		$resultado =  array();
		foreach ($listaColumnas as $columna){
			$resultado[] =  str_replace ($prefijo.'_','',$columna);;
		}
		
		return $resultado;
	}
	
	public function setUsuario($usuario){
		if(is_object($this->persistencia))$this->persistencia->setUsuario($usuario);
		$this->usuario = $usuario;
	}
	
	private function setAmbiente($tabla){
		if(!is_null($tabla)&&$tabla!='');{
			//crea persistencia
			$this->setTabla($tabla);
			$this->crearPersistencia();
			$this->getprefijoColumna();
			$this->setExcluidos();
			$this->recuperarColumnas();
		}return false;
	}
	
	private function setTabla($tabla){
		$this->tabla = $tabla;
		
	}
	
	private function crearPersistencia(){
		if(is_null($this->usuario)||$this->usuario=='') $this->usuario = '__indefinido__';
		$this->persistencia =  new Persistencia($this->conexion,$this->tabla,self::estadoHistorico,"'".$this->usuario."'");
	}
	
	//private 
	private function getprefijoColumna(){
		$this->prefijoColumnas = $this->persistencia->getprefijoColumna().'_';
	}
	
	private	function recuperarColumnas(){
		$this->columnas = $this->persistencia->getListaColumnas($this->excluidos);
		foreach($this->columnas as $columna)
			$this->columnasNoPrefijo[] = str_replace(substr($columna,0,4), "", $columna);
	}
	
	private function setExcluidos(){
		//$this->excluidos = array("'".$this->prefijoColumnas."_id'","'".$this->prefijoColumnas."_fecha_registro'");
		//$this->excluidos = array("'".$this->prefijoColumnas."fecha_registro'");
		$this->excluidos = array("'".$this->prefijoColumnas."'");
	}
	
	
	/*
	 * columnas
	*/
	
	private function recuperarColumnasTabla(){
		//popula $this->columnas
		$this->persistencia =  new Persistencia($this->conexion,'reglas.columnas');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->columnasTabla = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->columnasTabla = false;
		$this->mensaje->addMensaje("100","errorRecuperarColumnas",'error');
		return false;
	
	
	}
	
	public function getDatosColumnas(){
		$this->recuperarColumnasTabla();
	
		$lista = array();
		$prefijo = 'columnas_';
		foreach ($this->columnasTabla as $columna){
			$fila = array();
			foreach ($columna as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
					
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	/*
	 * operadores
	*/
	
	private function recuperarOperadores(){
		//popula $this->operadores
		$this->persistencia =  new Persistencia($this->conexion,'reglas.operadores');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->operadores = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->operadores = false;
		$this->mensaje->addMensaje("100","errorRecuperarOperadores",'error');
		return false;
	
	
	}
	
	public function getListaOperadores(){
		$this->recuperarOperadores();
		
		$lista = array();
		$prefijo = 'ope_';
		foreach ($this->operadores as $operador){
			$fila = array();
			foreach ($operador as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
					
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	/*
	 * categorias funcion
	*/
	
	private function recuperarCategorias(){
		//popula $this->categorias
		$this->persistencia =  new Persistencia($this->conexion,'reglas.categoria_funcion');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->categorias = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->categorias = false;
		$this->mensaje->addMensaje("100","errorRecuperarOperadores",'error');
		return false;
	
	
	}
	
	public function getListaCategorias(){
		$this->recuperarCategorias();
		$lista = array();
		$prefijo = 'cfun_';
		foreach ($this->categorias as $categoria){
			$fila = array();
			foreach ($categoria as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
					
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	
	
	/*
	 * permisos
	 */
	
	private function recuperarPermisos(){
		//popula $this->permisos
		$this->persistencia =  new Persistencia($this->conexion,'reglas.permisos');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->permisos = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->permisos = false;
		$this->mensaje->addMensaje("100","errorRecuperarPermisos",'error');
		return false;
	
	
	}
	
	public function getListaPermisos(){
		
		$this->recuperarPermisos();
		$lista = array();
		$prefijo = 'permisos_';
		foreach ($this->permisos as $permiso){
			$fila = array();
			foreach ($permiso as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
					
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	public function getPermiso($var = null,$tipo = null , $seleccion = null){
		
		if(!$this->validarEntradaSeleccion($var,$tipo,$seleccion)) return false;
		$prefijo = "permisos_";
		$listado = $this->permisos;
		$nombre = $this->selectTipo($prefijo,$tipo);
		$nombreS = $this->selectTipo($prefijo,$seleccion);
	
		foreach($listado as $lista){
			if(strtolower ($var)==strtolower ($lista[$nombre]))
				return $lista[$nombreS];
		}
		$this->mensaje->addMensaje("103","permisoNoEncontrado",'information');
		return false;
	}
	
	/*
	 * estados
	 */
	private function recuperarEstados(){
		//popula $this->estados
		$this->persistencia =  new Persistencia($this->conexion,'reglas.estados');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->estados = $this->persistencia->read($listaColumnas);
			return true;
		}
		
		$this->estados = false;
		$this->mensaje->addMensaje("100","errorRecuperarEstados",'error');
		return false;
		
		
	}
	
	public function getListaEstados(){
		$this->recuperarEstados();
		$lista = array();
		$prefijo = 'estados_';
		foreach ($this->estados as $estado){
			$fila = array();
			foreach ($estado as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
					
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	public function getEstado($var = null,$tipo = null , $seleccion = null){
	  if(!$this->validarEntradaSeleccion($var,$tipo,$seleccion)) return false;
		$prefijo = "estados_";
		$listado = $this->estados;
		$nombre = $this->selectTipo($prefijo,$tipo);
		$nombreS = $this->selectTipo($prefijo,$seleccion);
	
		foreach($listado as $lista){
			if(strtolower ($var)==strtolower ($lista[$nombre]))
				return $lista[$nombreS];
		}
		$this->mensaje->addMensaje("103","estadoNoEncontrado",'information');
		return false;
	}
	
	
	
	/*
	 * tipos
	 */
	private function recuperarTipos(){
		//popula $this->tipos
		$this->persistencia =  new Persistencia($this->conexion,'reglas.tipo_datos');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->tipos = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->tipos = false;
		$this->mensaje->addMensaje("104","errorRecuperarTipos",'error');
		return false;
	
	
	}
	
	public function getListaTipos(){
		$this->recuperarTipos();
		$lista = array();
		$prefijo = 'tipo_';
		foreach ($this->tipos as $tipo){
			$fila = array();
			foreach ($tipo as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
			
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	public function getTipo($var = null,$tipo = null , $seleccion = null){
	  if(!$this->validarEntradaSeleccion($var,$tipo,$seleccion)) return false;
		$prefijo = "tipo_";
		$listado = $this->tipos;
		$nombre = $this->selectTipo($prefijo,$tipo);
		$nombreS = $this->selectTipo($prefijo,$seleccion);
	
		foreach($listado as $lista){
			if(strtolower ($var)==strtolower ($lista[$nombre]))
				return $lista[$nombreS];
		}
		$this->mensaje->addMensaje("105","tipoNoEncontrado",'information');
		return false;
	}
	
	/*
	 * objetos
	 */
	
	private function recuperarObjetos(){
		//popula $this->objetos
		$this->persistencia =  new Persistencia($this->conexion,'reglas.objetos');
		$listaColumnas = $this->persistencia->getListaColumnas();
		if(is_array($listaColumnas)){
			$this->objetos = $this->persistencia->read($listaColumnas);
			return true;
		}
		$this->objetos = false;
		$this->mensaje->addMensaje("100","errorRecuperarObjetos",'error');
		return false;
	
	
	}
	
	public function getListaObjetos(){
		$this->recuperarObjetos();
		$lista = array();
		$prefijo = 'objetos_';
		foreach ($this->objetos as $objeto){
			$fila = array();
			foreach ($objeto as $a => $b){
				if(strpos($a,$prefijo)!==false){
					$indice = str_replace ($prefijo,"",$a);
					$fila[$indice] =  $b;
				}
				
			}
			if(count($fila)>0)$lista[] = $fila;
		}
		return $lista;
	}
	
	public function getObjeto($var = null,$tipo = null,$seleccion = null){
		if(!$this->validarEntradaSeleccion($var,$tipo,$seleccion)) return false;
		$prefijo = "objetos_";
		$listado = $this->objetos;
		$nombre = $this->selectTipo($prefijo,$tipo);
		$nombreS = $this->selectTipo($prefijo,$seleccion);
	
		foreach($listado as $lista){
			if(strtolower ($var)==strtolower ($lista[$nombre]))
				return $lista[$nombreS];
		}
		$this->mensaje->addMensaje("103","objetoNoEncontrado",'information');
		return false;
	}
	
	private function selectTipo($prefijo='',$tipo = ''){
		
		if($prefijo==''||$tipo=='') return false;

		switch(strtolower ($tipo)){
			case 'id':
				return $prefijo.'id';
				break;
			case 'nombre':
				return $prefijo.'nombre';
				break;
			case 'alias':
				return $prefijo.'alias';
				break;
			default:
				$this->mensaje->addMensaje("101","errorTipoNoExiste",'error');
				return false;
				break;
		}
	}
	
	private function validarEntradaSeleccion($var = null,$tipo = null,$seleccion=null){
		if(is_null($var)||$var==''){
			$this->mensaje->addMensaje("101","errorValorInvalido",'error');
			return false;
		}if(is_null($tipo)||$tipo==''){
			$this->mensaje->addMensaje("101","errorTipoInvalido",'error');
			return false;
		}if(is_null($seleccion)||$seleccion==''){
			$this->mensaje->addMensaje("101","errorSeleccionInvalido",'error');
			return false;
		}
		return true;
		
	}
	
	
	
	private function validarIdObjeto($idObjeto){
		if(!is_numeric($idObjeto)){
			$this->mensaje->addMensaje("101","erroridObjetoEntrada",'error');
			return false;
		}if(!$this->getObjeto($idObjeto,'id','nombre')){
			$this->mensaje->addMensaje("101","errorParametrosEntradaIdObjeto",'error');;
			return false;
		}
		
		return true;
	}
	
	private function validarParametros($parametros){
		
		if(!is_array($parametros)||count($parametros)==0){
			$this->mensaje->addMensaje("101","errorParametrosEntrada",'error');
			return false;
		}
		
		$llaves =  array_keys($parametros);
			foreach($llaves as $llave){
				if(!in_array($llave,$this->columnasNoPrefijo)){
					$this->mensaje->addMensaje("101","errorColumnaNoExiste",'error');
					return false;
				}
			}
		
		return true;
	}
	
	private function validarOperacion($operacion){
		
		if(!is_numeric($operacion)){
			$this->mensaje->addMensaje("101","errorOperacionEntrada",'error');
			return false;
		}
		
		return true;
	}
	
	private function validarEntrada($idObjeto = null, $parametros = null, $operacion = null){
		
		if($this->validarOperacion($operacion)&&$this->validarIdObjeto($idObjeto)){
			if($operacion!=2){
				if(!$this->validarParametros($parametros)) return false;
			}
			
				
		}else return  false;
		
		
		return true;
	}
	
	
	private function validarId($valor){
		if(!is_numeric($valor)){
			$this->mensaje->addMensaje("101","errorEntradaParametrosId",'error');
			return false;
		}
		return true;
		
	}
	
	private function validarNombre($valor){
		if(!is_string($valor)||strlen(trim($valor))>self::limiteNombre||trim($valor)==""){
			$this->mensaje->addMensaje("101","errorEntradaParametrosNombre",'error');
			return false;
		} return true;
	}
	
	private function validarDescripcion($valor){
		if(!is_string($valor)||strlen(trim($valor))>self::limiteDescripcion||trim($valor)==""){
			$this->mensaje->addMensaje("101","errorEntradaParametrosDescripcion",'error');
			return false;
		} return true;
	}
	
	private function validarProceso($valor){
		if(!is_numeric($valor)){
			$this->mensaje->addMensaje("101","errorEntradaParametrosProceso",'error');
			return false;
		}
		return true;
	}
	
	private function validarTipo($valor){
		if(!is_numeric($valor)||!$this->getTipo($valor,'id','id')){
			$this->mensaje->addMensaje("101","errorEntradaParametrosTipo",'error');
			return false;
		}
		return true;
	}
	
	private function validarValor($valor){
		if(!is_string($valor)||strlen(trim($valor))>self::limiteValor||trim($valor)==""){
			$this->mensaje->addMensaje("101","errorEntradaParametrosValor",'error');
			return false;
		} return true;
	}
	
	private function validarEstado($valor){
		if(!is_numeric($valor)||!$this->getEstado($valor,'id','id')){
			$this->mensaje->addMensaje("101","errorEntradaParametrosEstado",'error');
			return false;
		}
		return true;
	}
	
	//http://www.sergiomejias.com/2007/09/validar-una-fecha-con-expresiones-regulares-en-php/
	private function validar_fecha($fecha){
		if (ereg("(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](19|20)[0-9]{2}", $fecha)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function validarFechaRegistro($cadena=''){
		if(is_null($cadena)||$cadena==''){
			$this->mensaje->addMensaje("101","errorEntradaParametrosFechas",'error');
			return false;
		}
		
		$fechas = explode( ',', $cadena );
		$testWhere =  $this->where;
		if(!$testWhere){
			$testWhere =  '';
		}
		
		
		$testWhere = str_replace(' ', '', trim($testWhere));
		$testWhere  = preg_replace('/\s+/', '', trim($testWhere));
		
		if(count($fechas)==1&&$this->validar_fecha($fechas[0])){
			$this->fechaBetween .=" ".$this->prefijoColumnas."fecha_registro='".$fechas[0]."'::DATE " ;
			return $fechas[0];
		}elseif(count($fechas)>1&&$this->validar_fecha($fechas[0])&&$this->validar_fecha($fechas[1])){
			$this->fechaBetween .=" (".$this->prefijoColumnas."fecha_registro, ".$this->prefijoColumnas."fecha_registro) OVERLAPS ('".$fechas[0]."'::DATE, '".$fechas[1]."'::DATE)";
			
			return $fechas[0];
		}else{
			return false;
		}
		
	}
	
	private function procesarParametros($parametros){
			
		$this->parametros = array();
		$this->valores = array();
		$this->indexado = array();
		$valor = '';
		
		
		foreach($parametros as $a=>$b){
			
			switch($a){
				case 'id':
					if(!$this->validarId($b)) return false;
					$valor = $b;
					break;
				case 'nombre':
					if(!$this->validarNombre($b)) return false;
					$valor = "'".$b."'";
					break;
				case 'descripcion':
					if(!$this->validarDescripcion($b)) return false;
					$valor = "'".$b."'";
					break;
				case 'proceso':
					if(!$this->validarProceso($b)) return false;
					$valor = $b;
					break;
				case 'tipo':
					if(!$this->validarTipo($b)) return false;
					$valor = $b;
					break;
				case 'valor':
					if(!$this->validarValor($b)) return false;
					$valor = "'".$b."'";
					break;
				case 'estado':
					if(!$this->validarEstado($b)) return false;
					 $valor = $b;
					break;
				case 'fecha_registro':
					$limiteFecha = $this->validarFechaRegistro($b);
					if(!$limiteFecha) return false;
					
					$valor = "'".$limiteFecha."'";
					break;
				default:
					$valor = "'".$b."'";
					break;
			}

			$this->valores[] = $valor;
			$this->parametros[] = $this->prefijoColumnas.$a;
			$this->indexado[$this->prefijoColumnas.$a] = $valor;
			
			
		}
		
		if(count($this->parametros)==count($this->valores))	return true;
		
		return false;
	}
	
	private function setWhere($where = ''){
		
		if($where==''||is_null($where)){
			
			if(is_array($this->indexado)){
			 foreach ($this->indexado as $a=>$b) {
			    	if($a !=$this->prefijoColumnas.'fecha_registro')$where.=" ".$a.'='.$b. " AND"; 
			 }
			 $where=substr($where, 0, strlen ($where)-3);
			}
			
		}elseif ($where=='id'){

			if(isset($this->indexado[$this->prefijoColumnas.'id'])){
				$where =$this->prefijoColumnas.'id='.$this->indexado[$this->prefijoColumnas.'id'];
			}else{
				$this->mensaje->addMensaje("101","errorIdNoDefinido".$this->tablaAlias,'error');
				return false;
			}
			
		}
		$this->where = 	$where;
		return true;
	}
	
	private function procesarLeido($leido){
		if(isset($leido)&&is_array($leido)){
			//quitar indices numericos
			foreach ($leido as $a => $b){
				if(!is_numeric($a)){
					$valorNoPrefijo = str_replace($this->prefijoColumnas,'',$a);
					$leido[$valorNoPrefijo] = $b ;
					unset($leido[$a]);
				}
				unset($leido[$a]);
			}
			
			return $leido;
		}
		
			$this->mensaje->addMensaje("101","errorIdNoExiste".$this->tablaAlias,'error');
			return false;
		
		
		
			//
			
	}
	
	private function recuperarUltimoId(){
		
		$maxId = 'max('.$this->persistencia->getprefijoColumna().'_id)';
		$leido = $this->persistencia->read(array($maxId));
		 
		
		if(!$leido){
			$this->mensaje->addMensaje("101","errorCreacion".$this->tablaAlias,'error');
			return false;
		}
		return $leido[0][0];
	}
	
	private function registrarPropietario($ultimoId = ''){
		//recupera ultimo Id
		
		 if($ultimoId&&$ultimoId>=0){
		 	
		 	//set ambiente relaciones
		 	$idObjeto = 6;
		 	$tabla = $this->getObjeto($idObjeto,'id','nombre');
		 	if(!$tabla) return false;
		 	$this->tablaAlias = $this->getObjeto($idObjeto,'id','alias');
		 	$this->setAmbiente($tabla);
		 	
		 	$parametros =  array();
		 	$parametros['usuario'] = $this->usuario;
		 	$parametros['objeto'] = $idObjeto;
		 	$parametros['registro'] = $ultimoId;
		 	$parametros['permiso'] = 0;
		 	$parametros['estado'] = 1;
		 	
		 	if(!$this->procesarParametros($parametros)||!$this->persistencia->create($this->parametros,$this->valores)){
		 			
		 		$this->mensaje->addMensaje("101","errorCreacion".$this->tablaAlias,'error');
		 		return false;
		 	}
		 	
		 	return true;
		 	 
		 }
		 $this->mensaje->addMensaje("101","errorRegistroPropietario".$this->tablaAlias,'error');
		 return false;
		
	}
		
	public function ejecutar($idObjeto = null, $parametros = array(), $operacion = null){
		
		$tabla = $this->getObjeto($idObjeto,'id','nombre');
		if(!$tabla) return false;
		$this->tablaAlias = $this->getObjeto($idObjeto,'id','alias');
		$this->setAmbiente($tabla);
		
		if(!$this->validarEntrada($idObjeto, $parametros, $operacion)) return false;
		
		
		
		
		//Estado historico
		$this->persistencia->setHistorico(self::estadoHistorico);
		
		switch($operacion){
			case 1:
				//crear
				unset($parametros['id']);
				unset($parametros['fecha_creacion']);
				
				if(!$this->procesarParametros($parametros)||!$this->persistencia->create($this->parametros,$this->valores)){
					
					$this->mensaje->addMensaje("101","errorCreacion".$this->tablaAlias,'error');
					return false;
				}
				$ultimoId =  $this->recuperarUltimoId();
				
				 
				//registrar propietario
				
				if(!$this->registrarPropietario($ultimoId)) return false;
				return $ultimoId;
				
				break;
			case 2:
				//consultar
				
				if(!$this->procesarParametros($parametros)){
					
					return false;
				}
				else{
					$this->setWhere();
					
					if(strlen($this->fechaBetween)>0&&strlen($this->where)>0) $this->where .= " AND ".$this->fechaBetween;
					elseif(strlen($this->fechaBetween)>0&&!$this->where) $this->where .= $this->fechaBetween;
					
					$leido = $this->persistencia->read($this->columnas,$this->where);
					
					if(!$leido){
						$this->mensaje->addMensaje("101","errorLectura".$this->tablaAlias,'information');
						return false;
					}
					
						$lista =  array();
						foreach($leido as $lei) $lista[] =  $this->procesarLeido($lei);
						return $lista; 
					
					
				}
				break;
			case 3:
				//actualizar
				if(!$this->procesarParametros($parametros)||!$this->setWhere('id')||!$this->persistencia->update($this->parametros,$this->valores,$this->where)){ 
					$this->mensaje->addMensaje("101","errorActualizar".$this->tablaAlias,'error');
					
				
					return false;
				}
				
				
				break;
			case 4:
				//duplicar
				
				 
				if(!$this->procesarParametros($parametros)||!$this->setWhere('id')){
				  return false;
				}else{
					//1. Leer
					$columnas = $this->columnas;
					$parametros = array();
					unset($columnas[0]);
					$leido = $this->persistencia->read($columnas,$this->where);
					if(!$leido) return false;
					//2. Crear
						$parametros = $this->procesarLeido($leido[0]);
						$nombre = $parametros['nombre'];
						$creacion =  false;
						$i = 0;
						
						do{
							
							if($i==0) $parametros['nombre'] = $nombre."_copia";
							else $parametros['nombre'] = $nombre."_copia".$i;
							$this->procesarParametros($parametros);
							$creacion =  $this->persistencia->create($this->parametros,$this->valores);
							
							$i++;
						}while (!$creacion&&$i<self::numeroCopiasMaxima);
							
					  if(!$creacion){
					  	$this->mensaje->addMensaje("101","errorDuplicar".$this->tablaAlias,'error');
					  	return false;
					  }		
					
					
					
					
				}
				
				
				
				
				
				break;
			case 5:
				//cambio activo/inactivo
				if(!$this->procesarParametros($parametros)||!$this->setWhere('id')){
					return false;
				}else{
					$leido = $this->persistencia->read($this->columnas,$this->where);
					if(!$leido) return false;
					$parametros = $this->procesarLeido($leido[0]);
					
					foreach($parametros as $a => $b){
						if($a!='estado') unset($parametros[$a]);
					}
					
					//toggle
					if($parametros['estado']==2) $parametros['estado'] = 1;
					else $parametros['estado'] = 2;
					
					if(!$this->procesarParametros($parametros)||!$this->persistencia->update($this->parametros,$this->valores,$this->where)){
						$this->mensaje->addMensaje("101","errorCambiarEstado".$this->tablaAlias,'error');
						return false;
					}
						
				}
				break;
			case 6:
				//eliminar
				if(!$this->procesarParametros($parametros)||!$this->setWhere('id')||!$this->persistencia->delete($this->where)){
					$this->mensaje->addMensaje("101","errorEliminar".$this->tablaAlias,'error');
					return false;
				}
				break;
			default:
				return false;
				break;
		}
		
		return true;
		
	}
	
	
	public function registrarDatos(){
		return call_user_func_array(array($this,'ejecutar'), func_get_args());
	}
}


