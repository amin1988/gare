<?php
session_start();
require_once("../config.inc.php");
include_model("CoachEsterno","UtSocieta");

check_get("id");

$ut = UtSocieta::crea();
$coa = CoachEsterno::fromId($_GET["id"]);

if($coa->getIDSocieta() !== $ut->getIdSocieta())
	homeutente($ut);

CoachEsterno::deleteCoach($coa->getChiave());
redirect("soc/elecoach.php");