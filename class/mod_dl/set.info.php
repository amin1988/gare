<?php
/** @var $gara Gara */

$tipigara = array(0, 1, 2, 3, 4, 5, 6);

$info = new Info("SET");
$info->addParam("db", true);
$info->addParam("fotocoach", $gara->isFotoCoachObbligatoria());
$info->addParam("kata", "5", $tipigara);
$info->addParam("sanbon", "0", $tipigara);
$info->addParam("ippon", "3", $tipigara);

$info->setLabels("it", array(
		"db" => "Scarica database",
		"fotocoach" => "Scarica foto coach",
		"kata" => "Tipo gara kata",
		"sanbon" => "Tipo gara sanbon",
		"ippon" => "Tipo gara ippon",
		
		"kata:0" => "Eliminazione",
		"kata:1" => "Girone all'italiana",
		"kata:2" => "Girone all'italiana + Eliminazione",
		"kata:3" => "Eliminazione senza ripescaggi",
		"kata:4" => "Doppia Eliminazione",
		"kata:5" => "Punti",
		"kata:6" => "Eliminazione con Ripescaggio Semifinale",
		
		"sanbon:0" => "Eliminazione",
		"sanbon:1" => "Girone all'italiana",
		"sanbon:2" => "Girone all'italiana + Eliminazione",
		"sanbon:3" => "Eliminazione senza ripescaggi",
		"sanbon:4" => "Doppia Eliminazione",
		"sanbon:5" => "Punti",
		"sanbon:6" => "Eliminazione con Ripescaggio Semifinale",
		
		"ippon:0" => "Eliminazione",
		"ippon:1" => "Girone all'italiana",
		"ippon:2" => "Girone all'italiana + Eliminazione",
		"ippon:3" => "Eliminazione senza ripescaggi",
		"ippon:4" => "Doppia Eliminazione",
		"ippon:5" => "Punti",
		"ippon:6" => "Eliminazione con Ripescaggio Semifinale"
	));
$info->setLabels("en", array(
		"db" => "Download database",
		"fotocoach" => "Download coach photos",
		"kata" => "Kata draw mode",
		"sanbon" => "Sanbon draw mode",
		"ippon" => "Ippon draw mode",
		
		"kata:0" => "Elimination",
		"kata:1" => "Round Robin",
		"kata:2" => "Round Robin + Elimination",
		"kata:3" => "Elimination without Repechage",
		"kata:4" => "Double Elimination",
		"kata:5" => "Points",
		"kata:6" => "Elimination with Semi Final Repechage",
		
		"sanbon:0" => "Elimination",
		"sanbon:1" => "Round Robin",
		"sanbon:2" => "Round Robin + Elimination",
		"sanbon:3" => "Elimination without Repechage",
		"sanbon:4" => "Double Elimination",
		"sanbon:5" => "Points",
		"sanbon:6" => "Elimination with Semi Final Repechage",
		
		"ippon:0" => "Elimination",
		"ippon:1" => "Round Robin",
		"ippon:2" => "Round Robin + Elimination",
		"ippon:3" => "Elimination without Repechage",
		"ippon:4" => "Double Elimination",
		"ippon:5" => "Points",
		"ippon:6" => "Elimination with Semi Final Repechage"
	));