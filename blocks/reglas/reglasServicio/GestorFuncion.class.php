<?php

namespace reglas;



if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

include_once ("Tipos.class.php");
include_once ("Rango.class.php");

include_once ("Mensaje.class.php");
include_once ("Registrador.class.php");
include_once ("GestorUsuariosComponentes.class.php");

class GestorFuncion{
    
	const ID_OBJETO = 3;

	private $parametros ;
    private $registrador;
    private $usuario; 
    public $mensaje;
    private $verificadorAcceso ;
    
    
    function __construct(){
    	
    	$this->registrador = new Registrador();
    	$this->mensaje =  &$this->registrador->mensaje;
    	
    	
    	//configurar usuario
    	$this->usuario = $_REQUEST['usuario'];
    	$this->registrador->setUsuario($this->usuario);
    	$this->verificadorAcceso = new  GestorUsuariosComponentes();
    }
    
    private function getValorReal($valor = '',$tipo = ''){
    
    	$valor= base64_decode($valor);
    
    	if(!Tipos::validarTipo($valor,$tipo)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosTipo",'error');
    		return false;
    	}
    
    	if(Tipos::validarTipo($valor,$tipo)) $valor = is_null(Tipos::evaluarTipo($valor,$tipo))?'nulo':base64_encode(Tipos::evaluarTipo($valor,$tipo)) ;
    	if(strtolower(Tipos::getTipoAlias($tipo))=='porcentaje')
    		$valor = base64_encode($valor*100);
    	if(strtolower(Tipos::getTipoAlias($tipo))=='nulo')
    		$valor = base64_encode('nulo');
    	if(strtolower(Tipos::getTipoAlias($tipo))=='boleano')
    		$valor = (string) $valor==''?base64_encode('0'):base64_encode(1);
    
    	return $valor;
    }
    
    private function getRangoReal($rango = '',$tipo = ''){
    
    	$intervalo = explode(",",$rango);
    	if(!$intervalo) return false;
    	$resultadoLista =  array();
    
    	foreach ($intervalo as $valor){
    
    		$resultadoLista[] = base64_decode($this->getValorReal(base64_encode($valor),$tipo));
    	}
    	 
    	return implode(',',$resultadoLista);
    }
    
    
    
    public function crearFuncion($nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$categoria = '',$ruta='',$valor='',$estado=''){
    	
    	if(!$this->verificadorAcceso->validarAcceso(0,1,self::ID_OBJETO)) return false;
    	if($nombre==''||$proceso==''||$valor==''||$tipo==''||$rango == ''||$categoria==''||$ruta==''){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($estado=='') $estado = 1;
    	if($tipo=='') $tipo = 1;
    	
    	$parametros['nombre'] = $nombre;
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	$parametros['proceso'] = $proceso;
    	
    	$parametros['tipo'] = $tipo;
    	$parametros['rango'] = $this->getRangoReal($rango,$tipo);
    	
    	$parametros['categoria'] = $categoria;
    	$parametros['ruta'] = $ruta;
    	$parametros['valor'] = $valor;
    	$parametros['estado'] = $estado;
    	
    	
    	$ejecutar = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,1);
    	   	if(!$ejecutar){
    		
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return $ejecutar;
    	
    }
    
    public function actualizarFuncion($id = '',$nombre ='',$descripcion='',$proceso = '',$tipo = '',$rango='',$categoria = '',$ruta='',$valor='',$estado=''){
    	 
    	
    	
    	if(!$this->verificadorAcceso->validarAcceso($id,3,self::ID_OBJETO)) return false;
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	else{
    		$consulta = $this->registrador->ejecutar(self::ID_OBJETO,array($id),2);
    		$tipo = $consulta[0]['tipo'];
    	}
    	if($rango!='')$parametros['rango'] = $this->getRangoReal($rango,$tipo);
    	
    	if($categoria!='')$parametros['categoria'] = $categoria;
    	if($ruta!='')$parametros['ruta'] = $ruta;
    	if($valor!='')	$parametros['valor'] = $valor;
    	if($estado!='')	$parametros['estado'] = $estado;
    	$parametros['id'] = $id;
    	 
    	return $this->registrador->ejecutar(self::ID_OBJETO,$parametros,3);
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
    
    		$this->mensaje = &$this->registrador->mensaje;
    		echo $this->mensaje->getLastMensaje();
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarFuncion($id = '',$nombre ='',$proceso='',$tipo = '',$estado='',$fecha=''){
    
    	if(!$this->verificadorAcceso->validarAcceso($id,2,self::ID_OBJETO)) return false;
    	$parametros =  array();
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	//if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	//if($valor!='')	$parametros['valor'] = $valor;
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
    
    public function activarInactivarFuncion($id = ''){
    
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
    
    public function duplicarFuncion($id = ''){
    
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
