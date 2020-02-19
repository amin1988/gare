<?php
session_start();

require_once("config.inc.php");
include_model("Utente");

$ut = Utente::crea();
if (!is_null($ut)) $ut->logout();
redirect("index.php");

?>