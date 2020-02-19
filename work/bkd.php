<?php
if (!isset($_GET["door"])) exit("id");

session_start();
require_once '../config.inc.php';
include_model("Amministratore");

if (is_null(Amministratore::crea())) exit("u");

$_SESSION["backdoor"] = $_GET["door"];

echo "<pre>";
print_r($_SESSION);
?>