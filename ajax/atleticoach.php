<?php
if (!isset($_GET["id"])) exit("null");
if (!is_numeric($_GET["id"])) exit("null");
$idg = intval($_GET["id"]);

session_start();
require_once '../config.inc.php';
include_model("UtSocieta", "Coach");

$ut = UtSocieta::crea();
if (is_null($ut)) exit("null");

$soc = $ut->getSocieta();
$ach = $soc->getAltriCoach();

//toglie coach
foreach (Coach::lista($idg, $soc->getChiave()) as $c) {
	/* @var $c Coach */
	unset($ach[$c->getPersona()]);
}

echo "[";
$primo = true;
foreach ($ach as $p) {
	/* @var $p Persona */
	$id = $p->getChiave();
	$nome = $p->getCognome() . " " . $p->getNome();
	$fobj = Foto::persona($p);
	if ($fobj->esiste()) 
		$foto = $fobj->getFoto();
	else
		$foto = ""; 
	
	if ($primo) $primo=false;
	else echo ",";
	echo "{\"id\":\"$id\",\"nome\":\"$nome\",\"foto\":\"$foto\"}";
}
echo "]";

// echo json_encode($res);
exit();
?>