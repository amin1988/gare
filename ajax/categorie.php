<?php
require_once '../config.inc.php';
include_model("Categoria");

if (isset($_GET["id"])) {
	$idg = $_GET["id"];
	if (!is_numeric($idg)) exit("null");
	
	$cat = Categoria::listaGara($idg);
	if (count($cat) == 0) exit("null");
} else if (isset($_GET["gruppo"])) {
	$idg = $_GET["gruppo"];
	if (!is_numeric($idg)) exit("null");
	
	$cat[2] = Categoria::listaGruppo($idg);
	if (count($cat[2]) == 0) exit("null");
} else 
	exit("null");
	
// foreach ($cat as $tipo => $cl) {
// 	if (count($cl) == 0) continue;
// 	$rt = array();
// 	foreach ($cl as $id => $c) {
// 		/* @var $c Categoria */
// 		$rt[] = array("id" => $id, "nome" => $c->getNome());
// 	}
// 	$ret[$tipo] = $rt;
// }

$json = '{';
$primotipo = true;
foreach ($cat as $tipo => $cl) {
	if (count($cl) == 0) continue;
	if ($primotipo) $primotipo = false;
	else $json .= '],';
	$json .= "\"{$tipo}\":[";
	uasort($cl, array("Categoria","compare"));
	$primacat = true;
	foreach ($cl as $id => $c) {
		/* @var $c Categoria */
		if ($primacat) $primacat = false;
		else $json .= ',';
		$nome = $c->getNome();
		$tipo = $c->getTipo();
		$json .= "{\"id\":$id,\"nome\":\"$nome\",\"tipo\":$tipo}";
	}
}
if ($primotipo) $json .= '"0":[],"1":[';
$json .= ']}';

// header("Cache-Control: public");
// header('Expires: Thu, 15 Apr 2100 20:00:00 GMT"');

echo $json;
exit();

?>