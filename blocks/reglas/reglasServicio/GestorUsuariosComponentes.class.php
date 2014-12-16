<?php

namespace reglas;



if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Mensaje.class.php");
include_once ("Registrador.class.php");

class GestorUsuariosComponentes{
    
	const ID_OBJETO = 6;

	private $parametros ;
    private $registrador;
    private $usuario;
    public $mensaje;
    private $listaRegistrosPermitidos =  array();
    private $superUsuario =  false;
    
    function __construct($usuario = ''){
    	
    	$this->registrador = new Registrador();
    	$this->mensaje =  &$this->registrador->mensaje;
    	
    	
    	//configurar usuario
    	if($usuario ==''){
    		$this->usuario = $_REQUEST['usuario'];
    		$this->registrador->setUsuario($this->usuario);
    		
    	}else{
    		$this->usuario = $usuario;
    	}
    	
    }
    
    public function validarAcceso($idRegistro = '', $permiso = '', $idObjeto = ''){
    	
    	
    	
    	$listaPermitidos = $this->permisosUsuarioObjeto($this->usuario,$idObjeto);
    	
    	if(!is_array($listaPermitidos)){
    		$this->mensaje->addMensaje("101","errorPermisosGeneral",'error');
    		return false;
    	}
    	
    	if($this->superUsuario===true) return true;
    	
    	$idRegistro = (integer) $idRegistro;
    	$permiso =  (integer) $permiso;
    	$idObjeto =  (integer) $permiso;

    	//echo $permiso."///<br>";
    	//var_dump($listaPermitidos,"<br>",$this->listaRegistrosPermitidos,"<br>",$idRegistro,$permiso);
    	 
    	
    	foreach ($listaPermitidos as $permitidos){
    		
    		
    		
    		
    		//actua propietario del objeto
    		if($permitidos['registro']==0&&$permitidos['permiso']==0) {
    			$this->superUsuario = true;
    			return true;
    		}
    		
    		//tiene el permiso sobre todo el objeto
    		if($permitidos['registro']==0&&$permitidos['permiso']==$permiso) {
    			$this->superUsuario = true;
    			return true;
    		}
    		
    		//es admin
    		if($permitidos['permiso']==5){
    			$this->superUsuario = true;
    			return true;
    		}
    		
    		
    		//tiwne permiso solicitado explicitamente
    		if($permitidos['registro']==$idRegistro&&$permitidos['permiso']==$permiso) return true;
    		
    		//es propietario del registro
    		if($permitidos['registro']==$idRegistro&&$permitidos['permiso']==0) return true;
    		
    		
    		//propietario de algunos elementos y se permite consultarlos
    		if($permiso==2&&$permitidos['permiso']==0) return true;

    		
    		//tiene el permiso de consultar, no es claro sobre cuales elementos
    		if($permiso==$permitidos['permiso']&&$permiso==2) return true;
    		
    		
    		
    		
    	}
    	
    	$this->mensaje->addMensaje("101","errorPermisosGeneral",'error');
    	return false;
    	

    }
    
    public function filtrarPermitidos($consulta){
    	
    	if(!is_array($consulta)) return false;
    	
    	if($this->superUsuario===true) return $consulta;
    	
    	$resultado =  array();
    	foreach ($consulta as $fila){
    		if(in_array($fila['id'],$this->listaRegistrosPermitidos)) $resultado[] = $fila ;
    	}
    	
    	return $resultado;
    }
    
    private function verificaUsuario($usuario){
    	
    	$idTablaUsuarios = 5;
    	$parametros =  array();
    	$parametros['id'] = $usuario;
    	$parametros['estado'] = 1;
    	
    	//consulta
    	$consulta =  $this->registrador->ejecutar($idTablaUsuarios,$parametros,2);
    	
    	if(!is_array($consulta)){
    		$this->mensaje->addMensaje("101","usuarioNoExiste",'error');
    		return false;
    	}
    	return true;
    }
    
    private function verificaRegistroObjeto($objeto,$registro){
    	
    	if($registro==0) return  true;
    	
    	$parametros =  array();
    	
    	$parametros['id'] = $registro;
    	 
    	//consulta
    	$consulta =  $this->registrador->ejecutar($objeto,$parametros,2);
    	if(!is_array($consulta)){
    		 
    		$this->mensaje->addMensaje("101","registroObjetoNoExiste",'error');
    		return false;
    	}
    	
    	return true;
    }
    
    public function crearRelacion($usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
    	
    	if(!$this->validarAcceso(0,1,self::ID_OBJETO)) return false;
    	
    	if($usuario===''||$objeto===''||$registro===''||$permiso===''){
    		
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($estado=='') $estado = 1;
    	
    	//verifica que el usuario exista
    	if(!$this->verificaUsuario($usuario)) return false;
    	
    	//verifica que el objeto exista
    	if(!$this->registrador->getObjeto($objeto,'id','id')){
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	//verifica que el registro exista
    	if(!$this->verificaRegistroObjeto($objeto,$registro)) return false;
    	
    	//verifica que el permiso exista
    	if(!$this->registrador->getPermiso($permiso,'id','id')){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	$parametros =  array();
    	$parametros['usuario'] = $usuario;
    	$parametros['objeto'] = $objeto;
    	$parametros['registro'] = $registro;
    	$parametros['permiso'] = $permiso;
    	$parametros['estado'] = $estado;
    	
    	
    	$ejecutar = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,1);
    	   	if(!$ejecutar){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return $ejecutar;
    	
    	
    	
    }
    
    
    public function actualizarRelacion($id = '',$usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
    	 
    	if(!$this->validarAcceso($id,3,self::ID_OBJETO)) return false;
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}

    	
    	
    	$parametros =  array();
    	$parametros['id'] = $id;
    	if($usuario!=''){
    		//verifica que el usuario exista
    		if(!$this->verificaUsuario($usuario)) return false;
    		 
    		$parametros['usuario'] = $usuario;
    	}
    	if($objeto!=''){
    		//verifica que el objeto exista
    		if(!$this->registrador->getObjeto($objeto,'id','id')){
    			$this->mensaje = &$this->registrador->mensaje;
    			return false;
    		}
    		$parametros['objeto'] = $objeto;
    	}
    	if($registro!=''){
    		//verifica que el registro exista
    		if(!$this->verificaRegistroObjeto($objeto,$registro)) return false;
    		 
    		$parametros['registro'] = $registro;
    	}
    	if($permiso!=''){
	    	//verifica que el permiso exista
	    	if(!$this->registrador->getPermiso($permiso,'id','id')){
	    		
	    		$this->mensaje = &$this->registrador->mensaje;
	    		return false;
	    	}
    			 
    		$parametros['permiso'] = $permiso;
    	}
    	if($estado!='')$parametros['estado'] = $estado;
    	 
    	
    	
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
             
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarRelacion($id = '',$usuario ='',$objeto='',$permiso = '',$estado='',$fecha=''){
    
    	if(!$this->validarAcceso($id,2,self::ID_OBJETO)) return false;
    	$parametros =  array();
    	if($id!='')$parametros['id'] = $id;
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	if($objeto!='')$parametros['objeto'] = $objeto;
    	if($permiso!='')$parametros['permiso'] = $permiso;
    	if($estado!='')$parametros['estado'] = $estado;
    	if($fecha!='') $parametros['fecha_registro'] = $fecha;
    	 
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	if(!$consulta){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return $this->filtrarPermitidos($consulta);
    
    }
    
    public function activarInactivarRelacion($id = ''){
    
    	if(!$this->validarAcceso($id,3,self::ID_OBJETO)) return false;
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    
    	$parametros =  array();
    	$parametros['id'] = $id;
    
    	
    	 
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,5)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return true;
    
    }
    
    public function permisosUsuario($usuario ='',$objeto='',$registro=''){    	
    	
    	if($usuario===''||$objeto===''||$registro===''){
    		
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	 
    	$parametros =  array();
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	if($objeto!='')$parametros['objeto'] = $objeto;
    	if($registro!='')$parametros['registro'] = $registro;
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	
    	if(!is_array($consulta)){
    	
    		$this->mensaje->addMensaje("101","usuarioSinPermisos",'error');
    		return false;
    	}
    	
    	$retorna =  array();
    	
    	foreach ($consulta as $registro){
    		
    		$retorna[] = $registro['permiso'];
    		 
    	}
    	
    	return $retorna; 
    	 
    	 
    }
    
    public function permisosUsuarioObjeto($usuario ='',$objeto=''){
    	 
    	if($usuario===''||$objeto===''){
    
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	 
    	
    	//es admin?
    
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	$parametros['permiso'] = 5;
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	if(is_array($consulta)){
    		$this->superUsuario =  true;
    	}
    	
    	
    	// consulta relaciones asociadas al usuario y objeto
    	$parametros =  array();
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	if($objeto!='')$parametros['objeto'] = $objeto;
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	 
    	 
    	if(!is_array($consulta)){
    		 
    		$this->mensaje->addMensaje("101","usuarioSinPermisos",'error');
    		return false;
    	}
    	 
    	$retorna =  array();

    	
    	foreach ($consulta as $registro){
    		$this->listaRegistrosPermitidos[] = (integer) $registro['registro'];
    		$a = (integer) $registro['registro'];
    		$b = (integer) $registro['permiso'];
    		$retorna[] = array( 'registro'=> $a,  'permiso'=> $b);
    		 
    	}
    	asort($this->listaRegistrosPermitidos);
    	return $retorna;
    
    
    }
    
    public function validarRelacion($usuario ='',$objeto='',$registro='',$permiso = ''){
    
    	if($usuario===''||$objeto===''||$registro===''||$permiso===''){
    	
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	$parametros =  array();
    	
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	if($objeto!='')$parametros['objeto'] = $objeto;
    	if($registro!='')$parametros['registro'] = $registro;
    	if($permiso!='')$parametros['permiso'] = $permiso;
    	
    	$parametros['estado'] = 1;
    
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	 
    	if(!is_array($consulta)){
    
    		$this->mensaje->addMensaje("101","relacionNoExiste",'error');
    		return false;
    	}
    
    	
    	return true;
    
    }
    
    private function registrarAcceso($codigo , $usuario, $detalle){
    	
    	
    	//archivo
    	//http://stackoverflow.com/questions/19898688/how-to-create-a-logfile-in-php
    	//Something to write to txt log
    	$log  = "Cliente: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
    	"Intento: ".($this->verificaUsuario($usuario)?'Exito':'Fallo').PHP_EOL.
    	"Usuario: ".$usuario.PHP_EOL.
    	"Codigo: ".$codigo.PHP_EOL.
    	"-------------------------".PHP_EOL.PHP_EOL;
    	//Save string to log, use FILE_APPEND to append.
    	file_put_contents(__DIR__.'/log/log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
    	
    	
    	//bd
    	//id objeto de acceso
    	$idObjetoAcceso = 7;
    	
    	$parametros['codigo'] = $codigo;
    	$parametros['usuario'] = $usuario;
    	$parametros['detalle'] = $detalle;
    	
    	$this->registrador->ejecutar($idObjetoAcceso,$parametros,1);
    	
    }
    
    private function codificar($array){
    	
    	$cadena = serialize($array);
    	$cadena = base64_encode ($cadena);
    	return $cadena;
    }
    
    private function decodificar($cadena){
    	$decodificada = unserialize($cadena);
    	$decodificada = base64_decode ($cadena);
    	return $decodificada;
    }
    
    public function habilitarServicio(){

    	
    	//codigo
    	$codigo = uniqid();
    	
    	//detalle
    	$detalle = $this->codificar(array_merge ($_SERVER,$_REQUEST));
    	
    	//hace registro del acceso
    	$this->registrarAcceso($codigo, $this->usuario , $detalle);
    	
    	//verifica que el usuario este en la lista de usuarios
    	if(!$this->verificaUsuario($this->usuario)){
    		$this->mensaje->addMensaje("101","usuarioNoAutorizado",'error');
    		return false;
    	}

    	
    	return true;
    }
       


}

?>
