<?php
if (!isset($_GET["id"])) exit("0");

session_start();
require_once '../config.inc.php';
include_model("UtSocieta", "Squadra");

$ut = UtSocieta::crea();
if (is_null($ut)) exit("0");
$sq = new Squadra($_GET["id"]);

if (!$sq->esiste() || $sq->getSocieta() != $ut->getIdSocieta()) exit("0");

$sq->elimina();

exit("1");
?>