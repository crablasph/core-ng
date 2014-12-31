<?php 
namespace reglas\formulario;

include_once (dirname(__FILE__).'/../ClienteServicioReglas.class.php');
include_once (dirname(__FILE__).'/../Mensaje.class.php');
include_once (dirname(__FILE__).'/../Tipos.class.php');

use reglas\Tipos as Tipos;
use reglas\ClienteServicioReglas as ClienteServicioReglas;
use reglas\Mensaje as Mensaje;

if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class FormularioCrear {

    var $miConfigurador;
    
    private $metodoValidar;
    private $cliente;
    private $objeto;
    private $atributosObjeto;
    private $objetoId;
    private $objetoNombre;
    private $objetoAlias;
    private $mensaje;
    private $tipo;
    private $estado;
    private $permiso;
    private $categoria;
    private $objetoVisble;
    private $objetoCrear;
    private $objetoConsultar;
    private $objetoActualizar;
    private $objetoCambiarEstado;
    private $Objetoduplicar;
    private $objetoEliminar;
    private $columnas;
    private $operadores;
    private $listaParametros;
    private $listaAtributosParametros;
    private $proceso;
    
    function __construct($lenguaje,$objetoId = '') {

    	$this->objetoId = $objetoId;
        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        if(isset($_REQUEST['usuario'])) $_REQUEST['usuarioDefinitivo'] = $_REQUEST['usuario'];
        
        $this->lenguaje = $lenguaje;
        $this->mensaje = new Mensaje();
        $this->cliente  = new ClienteServicioReglas();
        $this->objeto = $this->cliente->getListaObjetos();
        $this->tipo = $this->cliente->getListaTipos();
        $this->estado = $this->cliente->getListaEstados();
        $this->permiso = $this->cliente->getListaPermisos();
        $this->categoria = $this->cliente->getListaCategorias();
        $this->columnas = $this->cliente->getDatosColumnas();
        $this->operadores = $this->cliente->getListaOperadores();
        $this->proceso = $this->cliente->getListaProcesos();

    }
    
    private function getColumnaAliasPorNombre($nombre = ''){
    	foreach ($this->columnas as $columna){
    		
    		if($columna['nombre'] ==$nombre) return $columna['alias']; 
    		
    	}
    	return "no definido";
    }
    
    private function determinarListaParametros(){
    	
    	$nombreObjeto = 'selectedItems';
    	$lista =  array();
    	$this->listaParametros = array();
    	$this->listaAtributosParametro = array();
    	if(isset($_REQUEST[$nombreObjeto])) $lista = explode( ',', $_REQUEST[$nombreObjeto] );

    	if(isset($lista[0])&&$lista[0]!=''){
    		$_REQUEST['id']= $lista[0];
    		$this->listaParametros[] = $_REQUEST['id'];
    		$this->metodoValidar = 'actualizar';
    	}else $this->metodoValidar = 'crear';;
    	
    	foreach ($this->atributosObjeto as $nombreObjeto){
    		foreach ($this->columnas as $datosColumna){
    			if($datosColumna['nombre']==$nombreObjeto&&$datosColumna[$this->metodoValidar]=='t'){
    				
    				if($nombreObjeto == 'usuario'){
    				    if(isset($_REQUEST[$nombreObjeto."Definitivo"])) $this->listaParametros[] = $_REQUEST[$nombreObjeto."Definitivo"];
    				    else $this->listaParametros[] = '';
    					$this->listaAtributosParametros[] = $datosColumna;
    					continue;
    				}
    				
    				if(isset($_REQUEST[$nombreObjeto])&&$datosColumna['codificada']!='t') $this->listaParametros[] = $_REQUEST[$nombreObjeto];
    				elseif (isset($_REQUEST[$nombreObjeto])&&$datosColumna['codificada']=='t') $this->listaParametros[] = $_REQUEST[$nombreObjeto."Codificada"];
    				else $this->listaParametros[] = '';
    				$this->listaAtributosParametros[] = $datosColumna;
    			}
    		}
    	}
    	
    	
    	
    	return true;
    	
    	
    }
    
    
    
    private function setBool($valor = ''){
    	if($valor=='t') return true;
    	return false;
    }
    
    private function seleccionarObjeto(){
    	foreach ($this->objeto as $objeto){
    		if($objeto['id']==$this->objetoId){

    			$this->objetoNombre = $objeto['nombre'];
    			$this->objetoAlias = $objeto['alias'] 	;
    			$this->objetoAliasSingular = $objeto['ejecutar'];
    			
    			$this->objetoVisble = $this->setBool($objeto['visible']);
    			$this->objetoCrear = $this->setBool($objeto['crear']);
    			$this->objetoConsultar = $this->setBool($objeto['consultar']);
    			$this->objetoActualizar = $this->setBool($objeto['actualizar']);
    			$this->objetoCambiarEstado = $this->setBool($objeto['cambiarestado']);
    			$this->objetoDuplicar = $this->setBool($objeto['duplicar']);
    			$this->objetoEliminar = $this->setBool($objeto['eliminar']);
    			
    			return true;
    		}
    	}
    	return false;
    }
    
    
    
    private function getListaElementos($nombre = '', $argumentos = ''){
    	
    	//determinar si en la variable request hay algo de aqui
    	//$this->atributosObjeto
    	//
    	if($argumentos =='') $this->determinarListaParametros();
    	
    	if($nombre ==''){
    		$metodo = "consultar".ucfirst($this->objetoAliasSingular);
    	}else{
    		$metodo = "consultar".ucfirst($nombre);
    	}
    	if($argumentos ==''){
    		$argumentos =  $this->listaParametros;
    	}
    	
    	if($this->metodoValidar=='actualizar'||$nombre!=''){
    		
    		try {
    			$this->listaElementos =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    		}catch (\SoapFault $fault) {
    			$this->mensaje->addMensaje($fault->faultcode,":".$fault->faultstring,'information');
    			return false;
    		}
    		
    		if($nombre=='funcion'){
    			$metodo = 'getFuncionesPredefinidas';
    			$listaFuncionesGenerales =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    			if(is_array($this->listaElementos))
    				$this->listaElementos = array_merge($listaFuncionesGenerales,$this->listaElementos);
    			else $this->listaElementos = $listaFuncionesGenerales;
    		}
    		
    		if(!is_array($this->listaElementos)) return false;
    		
    		
    		if($nombre==''){
    			foreach ($this->listaElementos as $elemento){
    				foreach ($elemento as $a=>$b){
    					$_REQUEST[$a] = $b;
    				}
    			
    			}	
    		}
    		
    		
    		
    		
    	}
    	   
    	
    	return true;
    }
    
    private function getAtributosObjeto($idObjeto = ''){
    	 
    	$metodo = 'getAtributosObjeto';
    	$argumentos =  array($idObjeto);
    	 
    	try {
    		$this->atributosObjeto =  call_user_func_array(array($this->cliente , $metodo), $argumentos);
    	}catch (\SoapFault $fault) {
    		$this->mensaje->addMensaje($fault->faultcode,":".$fault->faultstring,'information');
    		return false;
    	}
    
    	if(!is_array($this->atributosObjeto)) return false;
    	return true;
    }
    
    private function getObjetoAliasPorId($objeto ='', $id = ''){
    	foreach ($this->$objeto as $elemento){
    		if($elemento['id']==$id) return $elemento['alias'];
    	}
    }
    
    private function getObjetoNombrePorId($objeto ='', $id = ''){
    	foreach ($this->$objeto as $elemento){
    		if($elemento['id']==$id) return $elemento['nombre'];
    	}
    }
    
    
    private function setTextoTabla($valor = '', $nombre =''){
    	
    	$nombreSelect = '';
    	$aliasSelect = '';
    	foreach ($this->columnas as $columna){
    		
    		if($columna['nombre']==$nombre&&$columna['codificada']=='t'){
    			
    			return base64_decode($valor);
    		}
    		
    		if($columna['nombre']==$nombre&&$columna['input']=='select'){
    			$objeto = $columna['nombre'];
    			$id = $valor;
    			return $this->getObjetoAliasPorId($objeto , $id);
    		}
    		
    		
    	}
    	
    	return $valor;
    	
    	
    }
    
    private function dibujarTabla(){
    	$columnasFila = '';
    	$textoElemento = '';
    	
    	foreach ($this->listaElementos as $fila){
    		$textoElemento .= "<tr>";
    		$columnasFila = '';
    		foreach ($fila as $g=>$f){
    			$textoElemento .= "<td>".ucfirst(strtolower($this->setTextoTabla($f,$g)))."</td>";;
    			if(end($this->listaElementos)== $fila) $columnasFila .="<th>".ucfirst(strtolower($this->getColumnaAliasPorNombre($g)))."</th>";
    		}
    		$textoElemento .= "</tr>";
    	}
    	
    	$cadena = '<table id="tabla" class="tabla">';
    	 
    	$cadena .= "<thead>";
    	$cadena .= "<tr>";
    	$cadena .= $columnasFila;
    	$cadena .= "</tr>";
    	$cadena .= "</thead>";
    	 
    	$cadena .= "<tfoot>";
    	$cadena .= "<tr>";
        $cadena .= $columnasFila;
    	$cadena .= "</tr>";
    	$cadena .= "</tfoot>";
    	
    	$cadena .= '<tbody>';
        $cadena .= $textoElemento;
    	$cadena .= '</tbody>';
    	$cadena .= "</table>";
    	return $cadena;
    	 
    }
    
    
    
    private function selectElemento($elemento='elemento', $blanco = false){
    	$cambio = '';
    	if(strtolower($this->objetoAlias)!='reglas'){
    		if($elemento=='tipo'){
    			$cambio = ' onchange="cambiarRango(\''.$elemento.'\')"';
    		}
    		
    		if($elemento=='categoria'){
    			$cambio = ' onchange="cambiarCategoria(\''.$elemento.'\')"';
    		}
    	}
    	
    	$deshabilitado = false;
    	if(strtolower($this->objetoAlias)=='reglas'){
    		$deshabilitado = true;
    	}
    	
    	
    	$cadena= '';
    	$textos = array();
    	$textos[0] = utf8_encode($this->lenguaje->getCadena ($elemento));
    	$textos[1] = utf8_encode($this->lenguaje->getCadena ($elemento."Titulo"));
    	$cadena .= '<div  class="form-group" >';
    	$cadena .= '<div>';
    	$cadena .= '<label for="'.$textos[0].'">';
    	$cadena .= ucfirst($textos[0]);
    	$cadena .= '</label>';
    	$cadena .= '<span style="white-space:pre;"> </span>';
    	$cadena .= '</div>';
    	$cadena .= '<select title="'.$textos[1].'" name="'.$elemento.'" id="'.$elemento.'" class="form-control" ';
    	$cadena .=	$cambio;
    	if($deshabilitado) $cadena .= '  disabled ';;
    	$cadena .= ' >';
    	if(!$blanco) $cadena .= '<option ></option>';
    	foreach ($this->$elemento as $fila){
    		$cadena .= '<option ';
    		$cadena .= ' value = "'.$fila['id'].'" ';
    		if(isset($_REQUEST[$elemento]) && $_REQUEST[$elemento]==$fila['id'])$cadena .= '  selected ';
    		$cadena .= '>'.ucfirst(strtolower($fila['alias'])).'</option>';
    	}
    	$cadena .= '</select>';
    	$cadena .= '</div>';
    	
    	return $cadena;
    	 
    }
    
    private function textElemento($elemento='elemento', $requerido = false, $codificada =  false, $autocompletar =  false){
    	$cadena= '';
    	$cadenaHidden= '';
    	$valor = ''; 
    	$textos = array();
    	$textos[0] = utf8_encode($this->lenguaje->getCadena ($elemento));
    	$textos[1] = utf8_encode($this->lenguaje->getCadena ($elemento."Titulo"));
    	$cadena .='<div class="form-group" >';
    	
    	$cadena .= '<label for="'.$textos[0].'">';
    	$cadena .= ucfirst(strtolower($textos[0]));
    	$cadena .= '</label>';
    	$cadena .= '<span style="white-space:pre;"> </span>';
    	
    	
    	if($autocompletar&&$elemento=='proceso'){
    		
    		$cadena .= '<input type="text" class="form-control ';
    		if($requerido) {
    			if($elemento=='proceso') $cadena .= ' validate[required,custom[valorLista]] ';
    			else $cadena .= ' validate[required] ';
    		}
    		
    		$cadena .='" title="'.$textos[1].'" onkeyup="autocompletar(\''.$elemento.'\')"  name="'.$elemento.'Nombre" id="'.$elemento.'Nombre"  placeholder="'.ucfirst($textos[0]).'" ';
    		 
    		if(isset($_REQUEST[$elemento])&&!$codificada) $valor =' value="'.$this->getObjetoNombrePorId($elemento,$_REQUEST[$elemento]).'" ';
    		elseif(isset($_REQUEST[$elemento])&&$codificada) $valor =' value="'.$this->getObjetoNombrePorId($elemento,base64_decode($_REQUEST[$elemento])).'" ';
    		else $valor .=' value="" ';
    		 
    		$cadena .=$valor;
    		 
    		$cadena .= '></input>';
    	}
    	
    	if($autocompletar&&$elemento=='proceso'){
    		$cadena .= '<input type="hidden" class=" form-control ';
    	}else $cadena .= '<input type="text" class="form-control '; 
    	
    	if($requerido) $cadena .= ' validate[required] '; 
    	
    	$cadena .='" title="'.$textos[1].'" name="'.$elemento.'" id="'.$elemento.'"  placeholder="'.ucfirst($textos[0]).'" ';
    	
    	if(isset($_REQUEST[$elemento])&&!$codificada) $valor =' value="'.$_REQUEST[$elemento].'" ';
    	elseif(!isset($_REQUEST[$elemento])&&$codificada) $valor =' onchange="codificarValor(\''.$elemento.'\')" value="" ';
    	elseif(isset($_REQUEST[$elemento])&&$codificada) $valor =' onchange="codificarValor(\''.$elemento.'\')" value="'.base64_decode($_REQUEST[$elemento]).'" ';
    	else $valor .=' value="" ';
    	
    	$cadena .=$valor;
    	
    	$cadena .= '></input>';
    	$cadena .= '</div>';
    	
    	//input hidden codificado
    	if($codificada) {
    		$cadena.= '<input type="hidden" id="'.$elemento.'Codificado" name="'.$elemento.'Codificado" ';
    		if(isset($_REQUEST[$elemento])) $cadena .=' value="'.$_REQUEST[$elemento].'" ';
    		else $cadena .=' value="" ';
    		$cadena .=' >';
    	}
    	return $cadena; 
    }
    
    private function getVariablesListaDelTexto($texto=''){
    	$metodo = 'getVariablesListaDelTexto';
    	$argumentos =  array($texto);
    	return   call_user_func_array(array($this->cliente , $metodo), $argumentos);
    	 
    }
    
    
    
    private function listasInput($regla = false){
    	
    	
    	
    	$cadena= '';
    	$cadena.=' <div id="tabsListas">';
    	$cadena.=' <ul>';
    	$cadena.=' <li><a href="#tabs-operaciones">'.utf8_encode($this->lenguaje->getCadena ('operaciones')).'</a></li>';
    	$cadena.=' <li><a href="#tabs-parametros">'.utf8_encode($this->lenguaje->getCadena ('parametros')).'</a></li>';
    	$cadena.=' <li><a href="#tabs-variables">'.utf8_encode($this->lenguaje->getCadena ('variables')).'</a></li>';
    	//if($regla===true){
    		$cadena.=' <li><a href="#tabs-funciones">'.utf8_encode($this->lenguaje->getCadena ('funciones')).'</a></li>';
    	//}
    	$cadena.=' </ul>';
    	
    	
    	//operaciones
    	$cadena.=' <div class="tabOverflow" id="tabs-operaciones">';
    	$cadena.=' <div class="contenedorCalculadora">';
    	
    	foreach ($this->operadores as $operador){
    		$cadena.=' <div onclick="insertarValorTextBox(\''.$operador['nombre'].'\')" class="elementoListaCalculadora"><a  title="'.$operador['alias'].'"  >'.$operador['nombre'].'</a></div>';
    	}
    	
    	$cadena.=' </div>';
    	$cadena.=' </div>';
    	
    	
    	//parametros
    	
    	$cadena.=' <div  id="tabs-parametros">';
    	$cadena.=' <div >';
    	
    	$cadena.=' <div class="form-inline"><input class="form-control" placeholder="'.utf8_encode($this->lenguaje->getCadena ('buscar')).'" type="text" id="buscarParametro" onkeyup="buscarBotonesCalculadora(\'botonParametro\' ,\'buscarParametro\')" ></input></div>';
    	$cadena.=' <div class="tabOverflow">';
    	
    	 if($this->getListaElementos('parametro',array('','','','','','',1))){
    	 	foreach ($this->listaElementos as $elemento){
    	 		$cadena.=' <div style="overflow: hidden;" onclick="insertarValorTextBox(\'_'.$elemento['nombre'].'_\')" class="botonParametro elementoListaCalculadora"><a  title="insertar '.$elemento['nombre'].' , valor:'.base64_decode($elemento['valor']).' ,tipo:'.Tipos::getTipoAlias($elemento['tipo']).' " >'.$elemento['nombre'].'</a></div>';
    	 	} 	
    	 }
    
    	$cadena.=' </div>';
    	$cadena.=' </div>';
    	$cadena.=' </div>';

    	
    	//variables
    	 
    	$cadena.=' <div  id="tabs-variables">';
    	$cadena.=' <div >';
    	$cadena.=' <div class="form-inline"><input class="form-control" placeholder="'.utf8_encode($this->lenguaje->getCadena ('buscar')).'" type="text" id="buscarVariable" onkeyup="buscarBotonesCalculadora(\'botonVariable\' ,\'buscarVariable\')" ></input></div>';
    	$cadena.=' <div class="tabOverflow">';
    	if($this->getListaElementos('variable',array('','','','','','',1))){
    		foreach ($this->listaElementos as $elemento){
    			$cadena.=' <div style="overflow: hidden;" onclick="insertarValorTextBox(\''.$elemento['nombre'].'\')" class="botonVariable elementoListaCalculadora"><a  title="insertar '.$elemento['nombre'].' , valor:'.base64_decode($elemento['valor']).' ,tipo:'.Tipos::getTipoAlias($elemento['tipo']).' "  >'.$elemento['nombre'].'</a></div>';
    		}
    	}
    	 
    	$cadena.=' </div>';
    	$cadena.=' </div>';
    	$cadena.=' </div>';
    	 
    	
    	
    	//if($regla===true){
    		//funciones
    		
    		$cadena.=' <div  id="tabs-funciones">';
    		$cadena.=' <div >';
    		$cadena.=' <div class="form-inline"><input class="form-control" placeholder="'.utf8_encode($this->lenguaje->getCadena ('buscar')).'" type="text" id="buscarFuncion" onkeyup="buscarBotonesCalculadora(\'botonFuncion\' ,\'buscarFuncion\')" ></input></div>';
    		$cadena.=' <div class="tabOverflow">';
    		if($this->getListaElementos('funcion',array('','','','','1'))){
    			
    			foreach ($this->listaElementos as $elemento){
    				
    				$v2 = $this->getVariablesListaDelTexto(base64_decode($elemento['valor']));
    				
    				$valorParametrosFuncion='';
    				if(is_array($v2)){
    					foreach ($v2 as $v) $valorParametrosFuncion.=$v[0].",";
    				}
    				$valorParametrosFuncion = trim($valorParametrosFuncion, ",");
    				$cadena.=' <div style="overflow: hidden;" onclick="insertarValorTextBox(\''.$elemento['nombre'].'('.$valorParametrosFuncion.')\')" class="botonFuncion elementoListaCalculadora"><a  title="insertar '.$elemento['nombre'].'('.$valorParametrosFuncion.') ,tipo:'.Tipos::getTipoAlias($elemento['tipo']).'"  >'.$elemento['nombre'].'</a></div>';
    			}
    		}
    		
    		$cadena.=' </div>';
    		$cadena.=' </div>';
    		$cadena.=' </div>';
    	//}
    	
    	$cadena.=' </div>';
    	
    	return $cadena; 
    }
    
    private function textAreaElemento($elemento='elemento', $requerido = false, $codificada =  false){
    	$cadena= '';
    	$cadenaHidden= '';
    	$valor = '';
    	$textos = array();
    	$textos[0] = utf8_encode($this->lenguaje->getCadena ($elemento));
    	$textos[1] = utf8_encode($this->lenguaje->getCadena ($elemento."Titulo"));
    	$cadena .='<div class="form-group" >';
    	
    	
    	
    	$cadena .= '<label for="'.$textos[0].'">';
    	$cadena .= ucfirst(strtolower($textos[0]));
    	$cadena .= '</label>';
    	$cadena .= '<span style="white-space:pre;"> </span>';
    	
    	 
    	
    	if(strtolower($this->objetoAlias)=='reglas'&&$elemento=='valor'){
    		$cadena .="<br>".$this->listasInput(true);
    	}
    	
    	if(strtolower($this->objetoAlias)=='funciones'&&$elemento=='valor'){
    		$cadena .="<br>".$this->listasInput(false);
    	}
    	
    	
    	$cadena .= ' ';
    	if($elemento!='valor'&&strtolower($this->objetoAlias)!='reglas'&&strtolower($this->objetoAlias)!='funciones'){
    		//echo "defecto".$elemento;
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}elseif ($elemento!='valor'&&strtolower($this->objetoAlias)=='reglas'){
    		//echo "reglas".$elemento;
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}elseif ($elemento!='valor'&&strtolower($this->objetoAlias)=='funciones'){
    		//echo "funciones".$elemento;
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}elseif($elemento=='valor'&&strtolower($this->objetoAlias)=='reglas'){
    		//echo "valorReglas".$elemento;
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}elseif($elemento=='valor'&&strtolower($this->objetoAlias)=='funciones'){
    		//echo "valorFunciones".$elemento;
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}elseif ($elemento=='valor'&&strtolower($this->objetoAlias)!='funciones'&&strtolower($this->objetoAlias)!='reglas'){
    		$cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	}else $cadena .= '<textarea rows="4" cols="50" class="form-control ';
    	
    	//$cadena .= '<textarea rows="4" cols="50"  ';
    	
    	//if($elemento!='valor'&&strtolower($this->objetoAlias)!='reglas'&&strtolower($this->objetoAlias)!='funciones') $cadena .= 'class="ui-corner-all ';
    	//else $cadena .= 'class="  contenedorValor  ';
    	
    	if($requerido) $cadena .= ' validate[required] ';
    	$cadena .='" title="'.$textos[1].'" name="'.$elemento.'" id="'.$elemento.'"  placeholder="'.ucfirst($textos[0]).'" ';
    	
    	if(!isset($_REQUEST[$elemento])&&!$codificada) $valor .='  >';
    	if(!isset($_REQUEST[$elemento])&&$codificada) $valor =' onchange="codificarValor(\''.$elemento.'\')"  >';
    	if(isset($_REQUEST[$elemento])&&!$codificada) $valor =' value="'.$_REQUEST[$elemento].'" >'.$_REQUEST[$elemento];
    	if(isset($_REQUEST[$elemento])&&$codificada) $valor =' value="'.base64_decode($_REQUEST[$elemento]).'" onchange="codificarValor(\''.$elemento.'\')" >'.base64_decode($_REQUEST[$elemento]);
    	 
    	$cadena .=$valor;
    	 
    	$cadena .= '</textarea>';
    	
    	
    	//input hidden codificado
    	if($codificada) {
    		$cadena.= '<input type="hidden" id="'.$elemento.'Codificado" name="'.$elemento.'Codificado" ';
    		if(isset($_REQUEST[$elemento])) $cadena .=' value="'.$_REQUEST[$elemento].'" ';
    		else $cadena .=' value="" ';
    		$cadena .=' >';
    	}
    	
    	$cadena .= '</div>';
    	
    	 
    	
    	
    	
    	return $cadena;
    }
    
    
    private function rangeSliderElemento($elemento='elemento', $requerido = false, $codificada =  false){
    	$cadena= '';
    	$cadenaHidden= '';
    	$valor = '';
    	
    	$textos = array();
    	
    	$textos[0] = utf8_encode($this->lenguaje->getCadena ($elemento));
    	$textos[1] = utf8_encode($this->lenguaje->getCadena ($elemento."Titulo"));
    	$texto[2] = utf8_encode($this->lenguaje->getCadena ('minimo'));
    	$texto[3] = utf8_encode($this->lenguaje->getCadena ('maximo'));
    	
    	$cadena .='<br><div class="form-group" >';
    	
    	$cadena .= '<label for="'.$textos[0].'">';
    	$cadena .= ucfirst(strtolower($textos[0]));
    	$cadena .= '</label>';
    	
    	 
    	if($requerido) $requeridoTexto = ' validate[required] form-control';
    	else $requeridoTexto = ' form-control ';
    	
    	$cadena .= '<div >';
    	
    	$cadena .= '<div >';
    	//input  minimo
    	$valorMinimo = 0;
    	$valorMaximo = 1;
    	$muestraBi = true;
    	$valorMinimo = 0;
    	$valorMaximo = 1;
    	$muestraBi = array('inline','none');
    	$lista = isset($_REQUEST[$elemento])?  explode( ',', $_REQUEST[$elemento] ): false;;
    	if(isset($_REQUEST[$elemento])&&is_array($lista)){
    	
    		
    		if(count($lista)==2) {
    			$_REQUEST['min'.ucfirst($elemento)] = $lista[0];
    			$_REQUEST['max'.ucfirst($elemento)] = $lista[1];
    			$muestraBi = array('inline','none');
    		}else $muestraBi = $muestraBi = array('none','inline');;
    	
    	}
    	if(isset($_REQUEST['min'.ucfirst($elemento)])) $valorMinimo = $_REQUEST['min'.ucfirst($elemento)];
    	if(isset($_REQUEST['max'.ucfirst($elemento)])) $valorMaximo = $_REQUEST['max'.ucfirst($elemento)];
    	$cadena .= '<input style="float:left;display:'.$muestraBi[0].';" class="'.$requeridoTexto.'" onchange="setRango(\''.$elemento.'\')" id ="min'.ucfirst($elemento).'" placeholder="'.$texto[2].'"  type="text"';
    	$cadena .= 'value ="'.$valorMinimo.'" ></input>';
    
    	//input  maximo
    	$cadena .= '<input style="float:left;display:'.$muestraBi[0].';" class="'.$requeridoTexto.'" onchange="setRango(\''.$elemento.'\')" id ="max'.ucfirst($elemento).'" placeholder="'.$texto[3].'"  type="text"';
    	$cadena .= 'value ="'.$valorMaximo.'" ></input>';
    	
    	$cadena .= '</div>';
    	
    	$cadena .= '<div >';
    	$cadena.= '<input class="'.$requeridoTexto.'" title="'.$textos[1].'" type="text" style="float:left;display:'.$muestraBi[1].';"  id="'.$elemento.'" name="'.$elemento.'" ';
    	if(isset($_REQUEST[$elemento])) $cadena .=' value="'.$_REQUEST[$elemento].'" ';
    	else $cadena .=' value="0,1" ';
    	$cadena .=' >';

    	$cadena .= '</div>';
    	
    	$cadena .= '</div>';
    
    	
    	$cadena .= '</div>';
    	$cadena .= '<br>';
    		
    	
    	 
    	
    	//input hidden 
    	
    	
    	return $cadena;
    }
    
    
    private  function formularioElementos(){
    	$textos = array();
    	
    	//inicio
    	$cadena = '<div class="">';
    	$cadena .= '<form  role="form" name="formularioCreacionEdicion" id="formularioCreacionEdicion">';
    	
    	$textos[1] = $this->lenguaje->getCadena ($this->metodoValidar.'Formulario'). " ".$this->objetoAlias;
    	$cadena .='<div>';
    	$cadena .='<fieldset class="">';
    	$cadena .='<legend>'.$textos[1].'</legend>';
    	 
    	
    	$nombre = 'nombre';
    	$requerido = 'requerido_crear';
    	$codificado = 'codificada';
    	$autocompletar = 'autocompletar';
    	
    	//crea formularios
    	foreach ($this->listaAtributosParametros as $elemento){

    		switch($elemento['input']){
    			case 'text':
    				$cadena .= $this->textElemento($elemento[$nombre],$this->setBool($elemento[$requerido]),$this->setBool($elemento[$codificado]),$this->setBool($elemento[$autocompletar])); 
    				break;
    			case 'select':
    				$cadena .= $this->selectElemento($elemento[$nombre],$this->setBool($elemento[$requerido]));
    				break;
    			case 'textarea':
    				
    				$cadena .= $this->textAreaElemento($elemento[$nombre],$this->setBool($elemento[$requerido]),$this->setBool($elemento[$codificado]));
    				break;
    			case 'rangeSlider':
    				$cadena .= $this->rangeSliderElemento($elemento[$nombre],$this->setBool($elemento[$requerido]),$this->setBool($elemento[$codificado]));
    				break;
    			default:
    				break;
    		}
    		
    	}
    	
    	$cadena .= '</fieldset>';
    	$cadena .='</div>';
    	//Botones
    	$textos[0] = $this->lenguaje->getCadena ('guardar');
    	$textos[1] = $this->lenguaje->getCadena ('reiniciar');
    	$textos[2] = $this->lenguaje->getCadena ('limpiar');
    	$textos[3] = $this->lenguaje->getCadena ('evaluar');
    	$cadena .= '<div id="botones"  class="marcoBotones">';
    	
    	$cadena .= '<div class="campoBoton">';
    	$cadena .= '<button  onclick="guardarElemento(false)" type="button" tabindex="2" id="botonConsultar" value="'.$textos[0].'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.ucfirst($textos[0]).'</button>';
    	$cadena .= '</div>';
    	
    	$cadena .= '<div class="campoBoton">';
    	$cadena .= '<button type="reset" tabindex="2" onclick="formularioReset(\'formularioCreacionEdicion\')" id="botonReiniciar" value="'.$textos[1].'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.ucfirst($textos[1]).'</button>';
    	$cadena .= '</div>';
    	
    	$cadena .= '<div class="campoBoton">';
    	$cadena .= '<button type="button" onclick="formularioClean(\'formularioCreacionEdicion\')" tabindex="2" id="botonLimpiar" value="'.$textos[1].'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.ucfirst($textos[2]).'</button>';
    	$cadena .= '</div>';
    	
    	if(strtolower($this->objetoAliasSingular=='funcion')||
    	   strtolower($this->objetoAliasSingular=='regla')||
    	   strtolower($this->objetoAliasSingular=='parametro')||
    	   strtolower($this->objetoAliasSingular=='variable')){

    		$cadena .= '<div class="campoBoton">';
    		$cadena .= '<button type="button" onclick="evaluarTexto()" tabindex="2" id="botonEvaluarFormulario" value="'.$textos[3].'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.ucfirst($textos[3]).'</button>';
    		$cadena .= '</div>';
    		 
    	}
    	
    	$cadena .= '</div>';
    	 
    	//fin
    	$cadena .= '</form>';
    	$cadena .= '</div>';
    	$cadena .='<br>';
    	$cadena .='<br>';
    	

        return $cadena;
    }

    function formulario() {
    	
    	//1. Captura  variables de la operacion del $_REQUEST
    	
    	
    	$verifica =  true;
    	//2. Seleccionar objeto, popular datos
    	if(!$this->seleccionarObjeto()||!$this->getAtributosObjeto($this->objetoId)||!$this->getListaElementos()){    	
    		$verifica =  false;
    	}
    	
    	
        //muestra el formulario
    	echo '<div id="contenedorFormularioCreacion">';
    	echo $this->formularioElementos();
    	echo '</div>';
    	
    	if(!$verifica){
    		echo $this->mensaje->getLastMensaje();
    		return false; 
    	}
    	
    	
    	//Muestra la tbla
    	echo '<div id="resultado">'; 
    	   //echo $this->dibujarTabla();
    	echo '</div>';
    	
    	
    	
    	
    	
    }

    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );

             
        }

        return true;

    }

}

$miFormulario = new FormularioCrear ( $this->lenguaje,$objetoId );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>