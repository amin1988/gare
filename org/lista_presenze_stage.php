<?php

require_once("../config.inc.php");

include_view("lista_presenze_stage","Template","Header");

include_menu();

$lang = Lingua::getParole();



if(isset($_GET['id']))

	$gara = $_GET['id'];



$templ = new Template();

$templ->includeJs(Template::CHECKBOX);

$templ->stampaTagHead();

$templ->apriBody();


$cv = new PresenzeStageView($gara);

$cv->stampa();



$templ->chiudiBody();