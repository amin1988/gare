<?php
require_once("config.inc.php");
include_view("registrazioneestres","Template","Header");
include_menu();
$lang = Lingua::getParole();

$head = new Header();
$head = Header::titolo("");
$head->setIndietro("", "Home");
$head->setLogout(false);
$templ = new Template($head);
//$templ->includeJs(Template::CHECKBOX);
$templ->stampaTagHead();
$templ->apriBody();

check_get("ids");
$cv = new RegistraEstResView($_GET["ids"]);
$cv->stampa();

$templ->chiudiBody();