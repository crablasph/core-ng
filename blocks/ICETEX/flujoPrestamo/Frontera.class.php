<?
include_once("core/manager/Configurador.class.php");

class FronteraflujoPrestamo{

	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	
	var $miConfigurador;
	
	function __construct()
	{
	
		$this->miConfigurador=Configurador::singleton();		
	}

	public function setRuta($unaRuta){
		$this->ruta=$unaRuta;
	}

	public function setLenguaje($lenguaje){
		$this->lenguaje=$lenguaje;
	}

	public function setFormulario($formulario){
		$this->formulario=$formulario;
	}

	function frontera()
	{
		$this->html();
	}

	function setSql($a)
	{
		$this->sql=$a;

	}

	function setFuncion($funcion)
	{
		$this->funcion=$funcion;

	}

	function html()
	{
		
		include_once("core/builder/FormularioHtml.class.php");
		
		$this->ruta=$this->miConfigurador->getVariableConfiguracion("rutaBloque");
		
		
		$this->miFormulario=new formularioHtml();
		
		//Valida si la sesion esta permitida
		include_once($this->ruta."/funcion/validarSession.php");
		
		if(isset($_REQUEST['opcion'])){

			switch($_REQUEST['opcion']){
				
				case "getVariables":
					//include_once($this->ruta."/formulario/ejemplo.php");
					//echo "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXYYYY";
					break;

				
			}
		}else{
			$_REQUEST['opcion']="mostrar";
			include_once($this->ruta."/formulario/mostrar.php");
		}


	}





}
?>
