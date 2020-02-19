<?php
session_start();
require_once("../config.inc.php");

if (isset($_GET["idg"])) {
	redirect("soc/scegli.php?id=$_GET[idg]");
	exit();
}

include_view("Header", "Template");
include_model("Gara");
include_controller("VerificaPaginaIndividuale");

if (isset($_GET["id"]))
	$id=$_GET["id"];
else {
	redirect("soc");
	exit();
}
	
$gara = new Gara($id);
if (!$gara->esiste() || $gara->passata()) {
	redirect("soc");
	exit();
}
if ($gara->isIndividuale() && !$gara->isSquadre()) {
	redirect("soc/iscrivi.php?id=$_GET[id]");
	exit();
}
if (!$gara->isIndividuale() && $gara->isSquadre()) {
	redirect("soc/iscrivisq.php?id=$_GET[id]");
	exit();
}
Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($gara));

$lang = Lingua::getParole();
$head = Header::titolo($lang["iscrizioni_titolo"], $gara->getNome());
$head->setIndietro("soc",$lang["lista_gare"]);
$templ = new Template($head);

$templ->stampaTagHead();
$templ->apriBody();
?>

<br><br>
<a href="iscrivi.php?id=<?php echo $id; ?>" class='pulsante_noInput'><?php echo $lang["iscrizioni_individuali"]; ?></a>
<br><br><br>
<a href="iscrivisq.php?id=<?php echo $id; ?>" class='pulsante_noInput'><?php echo $lang["iscrizioni_squadre"]; ?></a>

<?php 
$templ->chiudiBody();
?>