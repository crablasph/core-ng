<?php

namespace reglas;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}


include_once ("Registrador.class.php");


class Rango{
    
	    
    
    function __construct(){
    	
    	
    }
    
    private function setAmbiente($tipo = ''){
    	 
    	$registrador =  new Registrador;
    	$idTipo = $registrador->getTipo($tipo,'id','id');
    
    
    	if(!$idTipo){
    		 
    		return false;
    	}
    
    	$idTipo = $idTipo;
    	$nombreTipo = $registrador->getTipo($tipo,'id','nombre');
    	$aliasTipo = $registrador->getTipo($tipo,'id','alias');
    	$metodo = "validar".ucfirst($aliasTipo);
    	unset($registrador);
    	return array("id" =>$idTipo,"nombre" =>$nombreTipo,"alias" =>$aliasTipo,"metodo" =>$metodo);
    	 
    }
    
        
    private function validarBoleano($valor,$rango=''){
    	$valor = (bool) $valor;
    	return is_bool($valor);
    }
    
    private function validarEntero($valor = '',$rango = ''){
    	$intervalo = explode(",",$rango);
    	if(!$intervalo) return false;
    	$minimo = (integer) $intervalo[0];
    	$maximo = (integer) $intervalo[1];
    	if($valor<$minimo||$valor>$maximo) return false;
    	return true;
    }
    
    private function validarDoble($valor = '',$rango = ''){
    	$intervalo = explode(",",$rango);
    	if(!$intervalo) return false;
    	
    	$minimo = (double) $intervalo[0];
    	if(strpos($intervalo[0],'.')!==false) $minimo = (double) $intervalo[0];
    	$maximo = (double) $intervalo[1];
    	if(strpos($intervalo[1],'.')!==false) $maximo = (double) $intervalo[1];
    	
    	if($valor<$minimo||$valor>$maximo) return false;
    	return true;
    }
    
    private function validarPorcentaje($valor = '',$rango = ''){
    	$valor =  $valor/100;
    	$intervalo = explode(",",$rango);
    	if(!$intervalo) return false;
    	$minimo = $intervalo[0]/100;
    	$maximo = $intervalo[1]/100;
    	if($valor<$minimo||$valor>$maximo) return false;
    	return true;
    }
    
    private function validarFecha($valor,$rango){
    	//Formato
    	//'d/m/Y'
    	//30/01/2014
    	//
    	$d = \DateTime::createFromFormat('d/m/Y', $valor);
    	if($d && $rango=='*') return true; 
    	$intervalo = explode(",",$rango);
    	if(!$intervalo) return false;
    	$minimo = \DateTime::createFromFormat('d/m/Y', $intervalo[0]);
    	$maximo = \DateTime::createFromFormat('d/m/Y', $intervalo[1]);
    	if(!$d||$d<$minimo||$d>$maximo) return false;
    	return true;
    }
    
    private function validarTexto($valor = '',$rango = ''){
    	if($rango=='*') return true;
    	return in_array($valor,explode(",",$rango));
    }
    
    private function validarLista($valor,$rango=''){
    	if($rango=='*') return true;
    	foreach (explode(",",$valor) as  $val){
    		if(!in_array($val,explode(",",$rango))) return false;
    	}
    	return true;
    }
    
    private function validarNulo($valor = '',$rango = ''){
    	$valor =  null;
    	return is_null($valor);
    }
    
    public static function validarRango($valor = "" , $tipo = "" , $rango = ""){
    	
    	$arrayDatos = self::setAmbiente($tipo);
    	
    	if($arrayDatos){

    		$idTipo = $arrayDatos['id'];
    		$nombreTipo = $arrayDatos['nombre'];
    		$aliasTipo = $arrayDatos['alias'];
    		$metodo = $arrayDatos['metodo'];
    		
    		switch($tipo){
    			case $idTipo:
    				if(method_exists(get_class(), $metodo))
    					return call_user_func_array(array(get_class() , $metodo), array($valor,$rango));
    				return false;
    				break;
    			default:
    				return false;
    				break;
    		}
    		 
    	}
    	return false;
    	
    	
    }
    

}

?>
