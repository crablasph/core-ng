<?php

namespace reglas\reglasServicio;



if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Mensaje.class.php");
include_once ("Registrador.class.php");
include_once ("GestorUsuariosComponentes.class.php");

class GestorFuncion{
    
	const ID_OBJETO = 3;

	private $parametros ;
    private $registrador;
    private $usuario; 
    public $mensaje;
    
    function __construct(){
    	
    	$this->registrador = new Registrador();
    	$this->mensaje =  &$this->registrador->mensaje;
    	
    	
    	//configurar usuario
    	$this->usuario = $_REQUEST['usuario'];
    	$this->registrador->setUsuario($this->usuario);
    	    	
    }
    
    private function validarAcceso($idRegistro , $permiso){
    	$usuario = new GestorUsuariosComponentes();
    	
    	$permisos = $usuario->permisosUsuario($this->usuario,self::ID_OBJETO,0);
    	if(!$permisos&&isset($idRegistro)&&$idRegistro!==0&&$idRegistro!==''&&!is_null($idRegistro)) 
    		$permisos =$usuario->permisosUsuario($this->usuario,self::ID_OBJETO,$idRegistro);
    	 
    	if(in_array(0,$permisos)||in_array(5,$permisos)) return true;
    	 
    	if(!in_array($permiso,$permisos)||!$usuario->validarRelacion($this->usuario,self::ID_OBJETO,$idRegistro,$permiso)){
    		$this->mensaje->addMensaje("101","errorPermisosGeneral",'error');
    		unset($usuario);
    		return false;
    	}
    	unset($usuario);
    	return true;
    }
    
    
    public function crearFuncion($nombre ='',$descripcion='',$proceso='',$rango = '',$tipo = '',$categoria = '',$ruta='',$valor='',$estado=''){
    	
    	if(!$this->validarAcceso(0,1)) return false;
    	if($nombre==''||$proceso==''||$valor==''||$tipo==''||$rango == ''||$categoria==''||$ruta==''){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($estado=='') $estado = 1;
    	if($tipo=='') $tipo = 1;
    	
    	$parametros['nombre'] = $nombre;
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	$parametros['proceso'] = $proceso;
    	$parametros['rango'] = $rango;
    	$parametros['tipo'] = $tipo;
    	$parametros['categoria'] = $categoria;
    	$parametros['ruta'] = $ruta;
    	$parametros['valor'] = $valor;
    	$parametros['estado'] = $estado;
    	
    	
    	   	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,1)){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return true;
    	
    }
    
    public function actualizarFuncion($id = '',$nombre ='',$descripcion='',$proceso='',$proceso = '',$tipo = '',$categoria = '',$ruta='',$valor='',$estado=''){
    	 
    	if(!$this->validarAcceso($id,3)) return false;
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	 
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($rango!='') $parametros['rango'] = $rango;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	if($categoria!='')$parametros['categoria'] = $categoria;
    	if($ruta!='')$parametros['ruta'] = $ruta;
    	if($valor!='')	$parametros['valor'] = $valor;
    	if($estado!='')	$parametros['estado'] = $estado;
    	$parametros['id'] = $id;
    	 
    	 
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		echo $this->mensaje->getLastMensaje();
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarFuncion($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){
    
    	if(!$this->validarAcceso($id,2)) return false;
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
    
    public function activarInactivarFuncion($id = ''){
    
    	if(!$this->validarAcceso($id,3)) return false;
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
    
    public function duplicarFuncion($id = ''){
    
    	if(!$this->validarAcceso($id,1)) return false;
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
