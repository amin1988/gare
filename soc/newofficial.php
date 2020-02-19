<?php
require_once("../config.inc.php");
include_view("Header","Template","nuovo_official");
include_model("UtSocieta");
include_menu();
$lang = Lingua::getParole();

$ut = UtSocieta::crea();
if (is_null($ut)) nologin();

$head = new Header();
$titolo = $lang["nuovo_official"];
$head = Header::titolo($titolo);
$head->addIndietro("soc",$lang["lista_gare"]);

$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX, Template::CALENDAR);
$templ->stampaTagHead();
$templ->apriBody();


$cv = new NuovoOfficialView();
$cv->stampa();

$templ->chiudiBody();