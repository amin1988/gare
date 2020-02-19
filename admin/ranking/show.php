<?php
require_once("../../config.inc.php");
include_view("ShowRanking","Template","Header");
include_menu();

$lang = Lingua::getParole();

$head = Header::titolo("Ranking",$lang["show_rank"]);//TODO aggiungere alla tabella lingua
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/ranking","Ranking");
$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX,"rank");
$templ->stampaTagHead(true);

$templ->apriBody();

$upr = new ShowRankingView();
$upr->stampa();

$templ->chiudiBody();