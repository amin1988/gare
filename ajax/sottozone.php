<?php
if (!isset($_GET["id"])) exit("null");

require_once '../config.inc.php';
include_model("Zona", "LivelloZona");

$zone = Zona::getSottozone($_GET["id"]);
if (count($zone) == 0) {
	//zona base
	echo "null";
	exit();
}

foreach ($zone as $z) {
	$liv = new LivelloZona($z->getLivello());
	break;
}
//$ret["liv"] = $liv->getNome();

$json = '{"liv":"'.$liv->getNome().'","zone":[';
$primo = true;

foreach ($zone as $id => $z) {
	//$ret["zone"][] = array("id" => $id, "nome" => $z->getNome());
	if ($primo) $primo = false;
	else $json .= ",";
	$json .= '{"id":'.$id.',"nome":"'.$z->getNome().'"}';
}
$json .= "]}";

header("Cache-Control: public");
header('Expires: Thu, 15 Apr 2100 20:00:00 GMT"');

echo $json;
exit();

?>