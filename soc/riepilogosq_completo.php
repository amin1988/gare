<?php
session_start();

require_once("../config.inc.php");
include_controller("riepilogo_comp_sq");
include_view("Header", "Template", "riepilogo/squadre");
$lang = Lingua::getParole();

$ctrl = new RiepilogoSquadreCompleto(Utente::SOCIETA, "soc/riepilogo_completo.php");
if ($ctrl->getGara()->isIndividuale())
	$titolo = $lang["riepilogo_squadre"];
else 
	$titolo = $lang["riepilogo_titolo"];
$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
if (isset($_GET["from"]) && $_GET["from"] == "storico") {
	$head->addIndietro("soc",$lang["lista_gare"]);
	$head->addIndietro("storico.php#gara".$ctrl->getGara()->getChiave(), $lang["storico"]);
} else 
	$head->setIndietro("soc",$lang["lista_gare"]);
$head->setStampa(true);
$templ = new Template($head);
$templ->includeJs("popup");

$view = new RiepilogoCompletoSquadreView($ctrl, $ctrl->getUtente()->getIdSocieta());

$templ->stampaTagHead(false);
$view->stampaJavascript();
echo '</head>';
$templ->apriBody();

$view->stampaCorpo(false, false);

$templ->chiudiBody();
?>