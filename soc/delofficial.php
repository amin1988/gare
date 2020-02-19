<?php
session_start();
require_once("../config.inc.php");
include_model("Official","UtSocieta");

check_get("id");

$ut = UtSocieta::crea();
$off = Official::fromId($_GET["id"]);

if($off->getIDSocieta() !== $ut->getIdSocieta())
	homeutente($ut);

Official::deleteOfficial($off->getChiave(), $off->getIDSocieta());
redirect("soc/eleofficial.php");