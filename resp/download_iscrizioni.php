<?php
session_start();
require_once("../config.inc.php");
include_controller("resp/download_iscrizioni");

$ctrl = new DownloadIscrizioni();
if ($ctrl->datiInviati()) {
	$ctrl->eseguiModulo();
} else {
	include_view("Template", "download_iscrizioni");
	$view = new DownloadIscrizioniView($ctrl);
	$templ = Template::titolo(Lingua::getParola("scarica_iscrizioni"), $ctrl->getNomeGara());
	$templ->stampaTagHead();
	
	$templ->apriBody();
	$view->stampa();
	$templ->chiudiBody();
}

?>