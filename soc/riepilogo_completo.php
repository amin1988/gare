<?php
session_start();
if($_GET["id"] == 88)//TODO CAMPIONATO ITALIANO 2015, RIMUOVERE
{
	header('Location: riepilogo.php?id=88');
}
require_once("../config.inc.php");
include_controller("riepilogo_comp_ind");
include_view("Header", "Template", "riepilogo/indiv");
$lang = Lingua::getParole();

$ctrl = new RiepilogoIndividualeCompleto(Utente::SOCIETA, "soc/riepilogosq_completo.php");
if ($ctrl->getGara()->isSquadre())
	$titolo = $lang["riepilogo_individuali"];
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

$view = new RiepilogoCompletoIndividualeView($ctrl, $ctrl->getUtente()->getIdSocieta());

$templ->stampaTagHead(false);
$view->stampaJavascript(); 
echo "</head>";
$templ->apriBody();

$view->stampaCorpo(true, false);

$templ->chiudiBody();
?>