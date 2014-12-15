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

class GestorVariable{
    
	const ID_OBJETO = 2;

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
    
    public function crearVariable($nombre ='',$descripcion='',$proceso='',$tipo = '',$rango='',$valor='',$estado=''){
    	
    	if(!$this->validarAcceso(0,1)) return false;
    	if($nombre==''||$proceso==''||$valor==''||$tipo==''||$rango==''){
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
    	
    	$parametros['valor'] = $this->getValorReal($valor,$tipo);
    	$valor = $parametros['valor'];
    	
    	if(!$parametros['valor']) return false;
    	
    	if(!Rango::validarRango(base64_decode($valor),$tipo,$rango)){
    		$this->mensaje->addMensaje("101","errorEntradaRangoTipo",'error');
    		return false;;
    	}
    	
    	$parametros['estado'] = $estado;
    	
    	
    	$ejecutar = $this->registrador->ejecutar(self::ID_OBJETO,$parametros,1);
    	
    	if(!$ejecutar){	
    		$this->mensaje = &$this->registrador->mensaje;
    		return false;
    	}
    	
    	return $ejecutar;
    	
    }
    
    public function actualizarVariable($id = '',$nombre ='',$descripcion='',$proceso='',$tipo = '',$rango = '',$valor='',$estado=''){
    	 
    	if(!$this->validarAcceso($id,3)) return false;
    	if($id==''||is_null($id)){
    		$this->mensaje->addMensaje("101","errorEntradaParametrosGeneral",'error');
    		return false;
    	}
    	 
    	if($nombre!='')	$parametros['nombre'] = $nombre; 
    	if($descripcion!='')	$parametros['descripcion'] = $descripcion;
    	if($proceso!='')	$parametros['proceso'] = $proceso;
    	
    	
    	if($tipo!='')	$parametros['tipo'] = $tipo;
    	else{
    		//consultar tipo
    		$consulta = $this->registrador->ejecutar(self::ID_OBJETO,array($id),2);
    		$tipo = $consulta[0]['tipo'];
    	}
    	
    	if($rango!='')$parametros['rango'] = $this->getRangoReal($rango,$tipo);
    	else{
    		$rango = $this->getRangoReal($consulta[0]['rango'],$tipo);
    	}
    	 
    	if($valor!=''){
    		$parametros['valor'] = $this->getValorReal($valor,$tipo);
    		$valor = $parametros['valor'];
    		if(!$parametros['valor']) return false;
    		 
    	}else{
    		$valor = $consulta[0]['valor'];
    	}
    	
        if(!Rango::validarRango(base64_decode($valor),$tipo,$rango)){
    		$this->mensaje->addMensaje("101","errorEntradaRangoTipo",'error');
    		return false;;
    	}
    	 
    	
    	if($estado!='')	$parametros['estado'] = $estado;
    	$parametros['id'] = $id;
    	 
    	 
    	if(!$this->registrador->ejecutar(self::ID_OBJETO,$parametros,3)){
            
    		$this->mensaje = &$this->registrador->mensaje;
    		echo $this->mensaje->getLastMensaje();
    		return false;
    	}
    	 
    	return true;
    	 
    }
    
    public function consultarVariable($id = '',$nombre ='',$proceso='',$tipo = '',$estado='',$fecha=''){
    
    	if(!$this->validarAcceso($id,2)) return false;
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
    
    	return $consulta;
    
    }
    
    public function activarInactivarVariable($id = ''){
    
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
    
    public function duplicarVariable($id = ''){
    	
    	if(!$this->validarAcceso($id,1)) return false;
    
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
