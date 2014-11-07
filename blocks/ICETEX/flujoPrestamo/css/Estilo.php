<?php
$indice=0;
$estilo[$indice++]="jquery-ui.css";
$estilo[$indice++]="validationEngine.jquery.css";
$estilo[$indice++]="listado.css";
$estilo[$indice++]="tablas.css";
$estilo[$indice++]="loading.css";



$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}

foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";

}
?>
