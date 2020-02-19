<?php
if (!isset($_GET["id"])) exit("id");
if (!is_numeric($_GET["id"])) exit("n");

session_start();
require_once '../config.inc.php';
include_model("Amministratore", "Gara");

if (is_null(Amministratore::crea())) exit("u");

$gara = new Gara($_GET["id"]);
if (is_null($gara)) exit("g");
$idg = $gara->getChiave();

/* @var $conn Connessione */
$conn = $GLOBALS["connint"];
$conn->connetti();

$num = 1;

$list = $gara->getCategorieIndiv();
if (count($list) > 0) {
	uasort($list, array("Categoria","compare"));
	foreach ($list as $c) {
		/* @var $c Categoria */
		$idc = $c->getChiave();
		$conn->query("UPDATE categoriegara SET numero='$num' WHERE idgara='$idg' AND idcategoria='$idc';");
		$num++;
	}
}

$list = $gara->getCategorieSquadre();
if (count($list) > 0) {
	uasort($list, array("Categoria","compare"));
	foreach ($list as $c) {
		/* @var $c Categoria */
		$idc = $c->getChiave();
		$conn->query("UPDATE categoriegara SET numero='$num' WHERE idgara='$idg' AND idcategoria='$idc';");
		$num++;
	}
}

echo "ok";
?>