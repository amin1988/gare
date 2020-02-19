<?php
/**
 * @param Societa $s1
 * @param Societa $s2
 */
// function cmp($s1, $s2) {
// 	return strcasecmp($s1->getNome(), $s2->getNome());
// }

session_start();
require_once '../config.inc.php';
include_model("Amministratore", "Societa");

if (is_null(Amministratore::crea())) exit("null");

$soc = Societa::lista(NULL, "nome");
//usort($soc, "cmp");

echo "[";
$primo = true;
foreach ($soc as $id => $s) {
	/* @var $s Societa */
	//$resa[] = array("id" => $id, "nome" => $s->getNome());
	if ($primo) $primo=false;
	else echo ",";
	echo "{\"id\":$id,\"nome\":\"".$s->getNome()."\"}";
}
echo "]";

exit();
?>