<?php
$indice=0;
$estilo[$indice++]="estiloBloque.css";
$estilo[$indice++]="validationEngine.jquery.css";
$estilo[$indice++]="jquery.dataTables_themeroller.css";
$estilo[$indice++]="jquery-linedtextarea.css";
$estilo[$indice++]="bootstrap.css";
$estilo[$indice++]="dataTables.responsive.css";

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
