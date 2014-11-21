<?php

namespace reglas\reglasServicio;



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
    
    private function verificaUsuario($usuario){
    	$idTablaUsuarios = 5;
    	$parametros =  array();
    	$parametros['id'] = $usuario;
    	
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
    	
    	
    	   	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,1)){
    	   	
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return true;
    	
    }
    
    public function actualizarRelacion($id = '',$usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
    	 
    
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
    
    public function consultarRelacion($id = '',$usuario ='',$objeto='',$registro='',$permiso = '',$estado=''){
    
     
    	$parametros =  array();
    	if($id!='')$parametros['id'] = $id;
    	if($usuario!='')$parametros['usuario'] = $usuario;
    	if($objeto!='')$parametros['objeto'] = $objeto;
    	if($registro!='')$parametros['registro'] = $registro;
    	if($permiso!='')$parametros['permiso'] = $permiso;
    	if($estado!='')$parametros['estado'] = $estado;
    	 
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	if(!$consulta){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return $consulta;
    
    }
    
    public function activarInactivarRelacion($id = ''){
    
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
    	$detalle = $this->codificar($_SERVER);
    	
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
