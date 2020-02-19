<?php
require_once("../config.inc.php");
include_view("convocazioni","Template","Header");
include_menu();
$lang = Lingua::getParole();

if(isset($_GET['id']))
	$gara = $_GET['id'];

$templ = new Template();
$templ->includeJs(Template::CHECKBOX);
$templ->stampaTagHead();
$templ->apriBody();

$cv = new ConvocazioniView($gara);
$cv->stampa();

$templ->chiudiBody();