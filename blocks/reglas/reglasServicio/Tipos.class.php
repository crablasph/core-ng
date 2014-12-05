<?php

namespace reglas;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}



include_once ("Registrador.class.php");

class Tipos{
    
	    
    
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
    
    private function validarBoleano($valor){
    	return is_bool($valor);
    }
    
    private function validarEntero($valor){
    	$valor = (int) $valor;
    	return is_int($valor);
    }
    
    private function validarDoble($valor){
    	$valor = (float) $valor;
    	return is_float($valor);
    }
    
    private function validarPorcentaje($valor){
    	return is_float($valor);
    }
    
    private function validarFecha($valor){
    	//Formato
    	//'d/m/Y'
    	//30/01/2014
    	//
    	$d = \DateTime::createFromFormat('d/m/Y', $valor);
    	return $d && $d->format('d/m/Y') == $valor;
    }
    
    private function validarTexto($valor){
    	return is_string($valor);
    }
    
    private function validarLista($valor){
    	return is_array(explode(",",$valor));
    }
    
    private function validarNulo($valor){
    	return is_null($valor);
    }
    
    public static function evaluarTipo($valor = "" , $tipo = ""){
    	
    	$arrayDatos = self::setAmbiente($tipo);
    	
    	if($arrayDatos){

    		$idTipo = $arrayDatos['id'];
    		$nombreTipo = $arrayDatos['nombre'];
    		$aliasTipo = $arrayDatos['alias'];
    		$metodo = $arrayDatos['metodo'];
    		
    		switch($tipo){
    			case $idTipo:
    				if(method_exists(get_class(), $metodo))
    					return call_user_func_array(array(get_class() , $metodo), array($valor));
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
