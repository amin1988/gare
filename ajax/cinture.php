<?php
session_start();
require_once '../config.inc.php';
include_model("Cintura");

/*$ut = UtSocieta::crea();
if (is_null($ut)) exit("null");

$cin = $ut->getSocieta()->getCintureAtleti();
if (count($cin) == 0) exit("null");
*/
if (!isset($_SESSION["cintureModificate"])) exit("null");
$cin = $_SESSION["cintureModificate"];
if (count($cin) == 0) exit("null");

$cinobj = Cintura::listaCinture();

echo "[";
$primo = true;
foreach ($cin as $ida=>$idcf) {
	/* @var $a Atleta */
	unset($_SESSION["cintureModificate"][$ida]);
	$idc = AtletaAffiliato::convertiCintura($idcf);
	$ncin = $cinobj[$idc]->getNome();

	if ($primo) $primo=false;
	else echo ",";
	echo "{\"id\":\"$ida\",\"cintura\":\"$ncin\"}";
}
echo "]";

// echo json_encode($res);
exit();
?>