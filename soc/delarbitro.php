<?php
session_start();
require_once("../config.inc.php");
include_model("ArbitroEsterno","UtSocieta");

check_get("id");

$ut = UtSocieta::crea();
$arb = ArbitroEsterno::fromId($_GET["id"]);

if($arb->getIDSocieta() !== $ut->getIdSocieta())
	homeutente($ut);

ArbitroEsterno::deleteArbitro($arb->getChiave());
redirect("soc/elearbitro.php");