<?php
if (!isset($_GET["id"])) exit("0");

session_start();
require_once '../config.inc.php';
include_model("Amministratore");

if (is_null(Amministratore::crea())) exit("0");
if (Utente::getIdAccesso() == $_GET["id"]) exit("0");

$ut = Utente::crea($_GET["id"]);
if (is_null($ut)) exit("0");

$ut->disattiva();

exit("1");
?>