<?php

require_once("../../config.inc.php");

include_view("convalida_result_ranking", "Template", "Header");

include_menu();



$lang = Lingua::getParole();



$head = Header::titolo("Ranking", $lang["update_rank"]); //TODO aggiungere alla tabella lingua

$head->addIndietro("admin", $lang["admin_titolo"]);

$head->addIndietro("admin/ranking/gare_esterne_addrisultati.php", "Gare Esterne");

$templ = new Template($head);

$templ->includeJs(Template::CHECKBOX, "rank");

$templ->stampaTagHead(true);


$templ->apriBody();

$upr = new GareEsterneConvalidaRisView();

$tipo_gara = isset($_GET['tipo_gara']) ? $_GET['tipo_gara'] : NULL;
if (!empty($tipo_gara))
{
    $upr->redirectListaStage();
} else
    $upr->stampa();

$templ->chiudiBody();
