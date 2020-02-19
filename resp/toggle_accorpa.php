<?php
session_start();
require_once("../config.inc.php");

if (!defined("_BASEDIR_")) exit();
include_model("Responsabile");

$ut = Responsabile::crea();

if (is_null($ut)) nologin();

if (!isset($_GET["id"])) {
	homeutente($this->ut);
	exit();
}
else 
{
	$id = $_GET["id"];
}

if(isset($_SESSION['SuperAccorpa']))
{
	if($_SESSION['SuperAccorpa'] == true)
		$_SESSION['SuperAccorpa'] = false;
	else 
		$_SESSION['SuperAccorpa'] = true;
}
else 
	$_SESSION['SuperAccorpa'] = true;

redirect("resp/accorpa.php?id=$id");