<?php

namespace reglas\reglasServicio;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Mensaje.class.php");
include_once ("Registrador.class.php");
include_once ("GestorUsuariosComponentes.class.php");

class ConstructorReglas{
    
	const ID_OBJETO = 4;

	private $parametros ;
    private $registrador;
    private $usuario;
    public $mensaje;
    
    function __construct(){
    	$this->registrador = new Registrador();
    	$this->mensaje = new Mensaje();
    	
    	//configurar usuario
    	$this->usuario = $_REQUEST['usuario'];
    	$this->registrador->setUsuario($this->usuario);
    	//unset($_REQUEST['usuario']);
    	
    }
    
    private function validarRelacion($idRegistro , $permiso){
    	$usuario = new GestorUsuariosComponentes();
    	$permisos = $usuario->permisosUsuario($this->usuario,self::ID_OBJETO,$idRegistro);
         
    	if(in_array(0,$permisos)||in_array(5,$permisos)) return true;
    	
    	if(!in_array($permiso,$permisos)||!$usuario->validarRelacion($this->usuario,self::ID_OBJETO,$idRegistro,$permiso)){
    		$this->mensaje->addMensaje("101","errorPermisosGeneral",'error');
    		unset($usuario); 
    		return false;
    	}
    	unset($usuario);
    	return true;
    }
    
    public function crearRegla($nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){

    	
    	if(!$this->validarRelacion(0,1)) return false;
    	
    	if($nombre===''||$proceso===''||$valor===''||$tipo===''){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($estado=='') $estado = 3;
    	if($tipo=='') $tipo = 1;
    	
    	$parametros['nombre'] = $nombre;
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	$parametros['proceso'] = $proceso;
    	$parametros['tipo'] = $tipo;
    	$parametros['valor'] = $valor;
    	$parametros['estado'] = $estado;
    	
    	
    	   	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,1)){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return true;
    	
    }
    
    public function actualizarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
    	 
    	if(!$this->validarRelacion($id,3)) return false;
    	
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	 
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	if($valor!='')	$parametros['valor'] = $valor;
    	if($estado!='')	$parametros['estado'] = $estado;
    	$parametros['id'] = $id;
    	 
    	 
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
    
     
    	if(!$this->validarRelacion($id,2)) return false;
    	
    	$parametros =  array();
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	if($valor!='')	$parametros['valor'] = $valor;
    	if($estado!='')	$parametros['estado'] = $estado;
    	if($id!='') $parametros['id'] = $id;
        
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	if(!$consulta){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return $consulta;
    
    }
    
    public function activarInactivarRegla($id = ''){
    
    	if(!$this->validarRelacion($id,3)) return false;
    	
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
    
    public function duplicarRegla($id = ''){
    
    	if(!$this->validarRelacion($id,1)) return false;
    	
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    
    	$parametros =  array();
    	$parametros['id'] = $id;
    
    	 
    
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,4)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return true;
    
    }
    


}

?>
