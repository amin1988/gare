<?php

require_once("../../config.inc.php");

include_view("classifica_ranking","Template","Header");

include_menu();
include_model("UtSocieta");

$ut = UtSocieta::crea();
if ( empty($ut))
{
     $tipo_utente = 4;   
}
else{
$tipo_utente = $ut->getTipo();
}

$lang = Lingua::getParole();



$head = Header::titolo("Ranking",$lang["update_rank"]);//TODO aggiungere alla tabella lingua

if ( $tipo_utente==4)
{
        $head->addIndietro("admin",$lang["admin_titolo"]);
        $head->addIndietro("admin/ranking/gare_esterne_addrisultati.php","Gare Esterne");
}
if ( $tipo_utente==1)
{
        $head->addIndietro("soc/index.php","Lista Gare");
}

$templ = new Template($head);

$templ->includeJs(Template::CHECKBOX,"rank");

$templ->stampaTagHead(true);


$templ->apriBody();

$upr = new ClassificaView();

$upr->stampa();



$templ->chiudiBody();