<?php
if (!isset($_GET["id"])) exit("null");

session_start();
require_once '../config.inc.php';
include_model("UtSocieta", "Squadra", "Gara");

$ut = UtSocieta::crea();
if (is_null($ut)) exit("null");

$sq = new Squadra($_GET["id"]);
$compid = $sq->getComponenti();
if (count($compid) == 0) exit("null");

$comp = $ut->getSocieta()->getAtleti($compid);
if (count($comp) == 0) exit("null");

echo "[";
$primo = true;
foreach ($comp as $a) {
	/* @var $a Atleta */
	$av["nome"] = $a->getNome();
	$av["cognome"] = $a->getCognome();
	$av["sesso"] = Sesso::toStringBreve($a->getSesso());
	$av["nascita"] = $a->getDataNascita()->format('d\/m\/Y');
	$av["cintura"] = Cintura::getCintura($sq->getCinturaComponente($a->getChiave()))->getNome();
	
	$res[] = $av;
	
	if ($primo) $primo=false;
	else echo ",";
	echo "{\"cognome\":\"$av[cognome]\",\"nome\":\"$av[nome]\",\"sesso\":\"$av[sesso]\",";
	echo "\"nascita\":\"$av[nascita]\",\"cintura\":\"$av[cintura]\"}";
}
echo "]";

// echo json_encode($res);
exit();
?>