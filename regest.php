<?php
require_once("config.inc.php");
include_view("registrazioneesterna","Template","Header");
include_menu();
$lang = Lingua::getParole();

$head = new Header($lang["reg_est"]);
$head = Header::titolo($lang["reg_est"]);
$head->setIndietro("index.php");
$head->setLogout(false);
$templ = new Template($head);
//$templ->includeJs(Template::CHECKBOX);
$templ->stampaTagHead();
$templ->apriBody();

$cv = new RegistraEsternaView();
$cv->stampa();

$templ->chiudiBody();