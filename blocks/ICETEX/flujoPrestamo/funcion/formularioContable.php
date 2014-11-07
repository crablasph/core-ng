<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}
/**
 * * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */
$conexion="icetex";


$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
if (!$esteRecursoDB) {
    //Este se considera un error fatal
    exit;
}

echo "<br>";

echo '<div id="resolucion" style="text-align: left;padding: 10px;">';
echo '<div style="text-align: center;padding: 10px;">';
echo "<h3>".$this->lenguaje->getCadena("tituloformularioContable")."</h3>";
echo '</div>';



echo '<form method="post" enctype="multipart/form-data" name="formularioContable" id="formularioContable">';



echo "<div style =\"font-style:italic;text-align: center;\">".$this->lenguaje->getCadena("notaExcelMarca");
$rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ).'/blocks';
$rutaURL .='/'.$_REQUEST["bloqueGrupo"].'/'.$_REQUEST["bloqueNombre"].'/'.'uploads/PLANTILLA_MARCA.xlsx';
echo '<br><a href="'.$rutaURL.'">'.$this->lenguaje->getCadena("plantillaExcel").'</a>';
echo "</div>";
echo "<p style=\"text-align: center;padding: 5px;\"><b>".$this->lenguaje->getCadena("documentoExcel")."</b>:";
echo '<input type="file" width="80px" id="excelMarca" name="excelMarca" class="ui-widget ui-widget-content ui-corner-all validate[required]" title="'.utf8_encode($this->lenguaje->getCadena("ingreseValorX")).'"></input>';
echo "<br><b>".$this->lenguaje->getCadena("periodo")."</b>:";
//consulta Períodos Actual y anterior
$cadena_sqlL = $this->sql->cadena_sql("periodoActualYAnterior",'');
$registrosL = $esteRecursoDB->ejecutarAcceso($cadena_sqlL,"busqueda");
//string select periodo actual y anterior
$strListado ='<select name="periodo" id="periodo">';
        foreach ($registrosL as $el){
                $strListado .='<option value="'.$el[0].'">';
                $strListado .=$el[0];
                $strListado .='</option>';
        }
$strListado .='</select>';

echo ' '.$strListado.'</td>';
echo "</p>";

echo '<div style="text-align: center;padding: 10px;">';
echo '<input type="button" onclick="enviarMarca(document.formularioContable.periodo.value);" width="30px" id="consultarUsuario" value= "'.$this->lenguaje->getCadena("enviarMarca").'">';
echo '</div>';
echo "</form>";

echo "</div>";
echo "<div id='rconsultas'>";
echo "</div>";





