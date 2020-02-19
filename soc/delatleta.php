<?php
session_start();
require_once("../config.inc.php");
include_model("AtletaEsterno","UtSocieta");

check_get("id");

$ut = UtSocieta::crea();
$atl = AtletaEsterno::fromId($_GET["id"]);

if($atl->getSocieta() !== $ut->getIdSocieta())
	homeutente($ut);

AtletaEsterno::deleteAtleta($atl->getChiave(), $ut->getIdSocieta());
redirect("soc/eleatleti.php");