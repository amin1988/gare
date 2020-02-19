<?php
session_start();
require_once("config.inc.php");
include_controller("download_allegato");

$ctrl = new DownloadAllegato();
$ctrl->setHeader();

$ctrl->stampaContenuto();
exit();

?>