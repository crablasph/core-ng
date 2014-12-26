<?php

namespace reglas;

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
    private $verificadorAcceso ;
    
    function __construct(){
    	$this->registrador = new Registrador();
    	$this->mensaje = new Mensaje();
    	
    	//configurar usuario
    	$this->usuario = $_REQUEST['usuario'];
    	$this->registrador->setUsuario($this->usuario);
    	$this->verificadorAcceso = new  GestorUsuariosComponentes();
    	
    	
    }
    
    
    
    public function crearRegla($nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado=''){

    	
    	if(!$this->verificadorAcceso->validarAcceso(0,1,self::ID_OBJETO)) return false;
    	
    	if($nombre===''||$proceso===''||$valor===''){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($estado=='') $estado = 3;
    	else $estado = 3;
    	if($tipo=='') $tipo = 1;
    	
    	$parametros['nombre'] = $nombre;
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	$parametros['proceso'] = $proceso;
    	$parametros['tipo'] = 1;
    	$parametros['valor'] = $valor;
    	$parametros['estado'] = 3;
    	
    	 
    	$ejecutar = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,1);
    	
    	if(!$ejecutar){
    	
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	 
    	return $ejecutar;
    	
    }
    
    public function actualizarRegla($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$valor='',$estado='',$justificacion=''){
    	 
    	if(!$this->verificadorAcceso->validarAcceso($id,3,self::ID_OBJETO)) return false;
    	
    	if($id==''||is_null($id)||$justificacion == ''||is_null($justificacion)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	 
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	$parametros['tipo'] = 1;
    	if($valor!='')	$parametros['valor'] = $valor;
    	if($estado!='')	$parametros['estado'] = $estado;
    	$parametros['id'] = $id;
    	$parametros['justificacion'] = $justificacion;
    	 
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarRegla($id = '',$nombre ='',$proceso='',$tipo = '',$estado='', $fecha=''){
    
    	
    	if(!$this->verificadorAcceso->validarAcceso($id,2,self::ID_OBJETO)) return false;
    	
    	$parametros =  array();
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	if($estado!='')	$parametros['estado'] = $estado;
    	if($id!='') $parametros['id'] = $id;
    	
    	if($fecha!='') $parametros['fecha_registro'] = $fecha;
    	
        
    	$consulta = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,2);
    	
    	if(!$consulta){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    
    	return $this->verificadorAcceso->filtrarPermitidos($consulta);
    
    }
    
    public function activarInactivarRegla($id = ''){
    
    	if(!$this->verificadorAcceso->validarAcceso($id,3,self::ID_OBJETO)) return false;
    	
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
    
    	if(!$this->verificadorAcceso->validarAcceso($id,1,self::ID_OBJETO)) return false;
    	
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    
    	$parametros =  array();
    	$parametros['id'] = $id;
    
    	 
    
    	$ejecutar = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,4);
     	if(!$ejecutar){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return $ejecutar;
    
    }
    


}

?>
