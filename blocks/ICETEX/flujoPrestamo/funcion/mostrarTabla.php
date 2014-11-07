<?php
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
}

echo '<br><table style="margin: 0 auto;">';

//echo '<tr>';
echo '<thead><tr><th colspan="2"><b>';
echo utf8_encode($this->titulo);
echo '</b></th></tr></thead>';
//echo '</tr>';

foreach ($this->reg[0] as $a=>$b)
//&&$b!=end($this->reg[0])
if(!is_numeric($a)){
	echo '<tr>';
	echo '<td><b>';
	echo utf8_encode($a);
	echo '</b></td>';
	echo '<td>';
	echo $b;
	echo '</td>';
	echo '</tr>';
}



echo '<br></table><br>';

//aqui debe mostrar primero el historico de pagos

//aqui redirige el flujo
$this->workflow();

exit;