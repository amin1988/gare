<?php
session_start();

require_once("../config.inc.php");
include_controller("resp/accorpa");
include_view("Header", "Template", "accorpa");
$lang = Lingua::getParole();

$ctrl = new Accorpa();
$view = new AccorpaView($ctrl);
$head = Header::titolo($lang["accorpa_titolo"], $ctrl->getGara()->getNome());
$head->addIndietro("resp",$lang["lista_gare"]);
if ($ctrl->getGara()->isIndividuale())
	$pag = "riepilogo";
else
	$pag = "riepilogosq";
$head->addIndietro("resp/$pag.php?id=".$ctrl->getGara()->getChiave(),$lang["riepilogo_titolo"]);
$templ = new Template($head);
$templ->includeJs("accorpa", "popup");

$templ->stampaTagHead(false);
?>
<link href="<?php echo _PATH_ROOT_; ?>css/accorpa.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function testoSeleziona() {
	return "<?php echo $lang["seleziona_accorpamento"]; ?>";
}

function testoSepara(numcat, totpart, partxcat) {
	var txt = "<?php echo $lang["descrizione_separa"]; ?>";
	txt = txt.replace("<NUMCAT>", numcat);
	return txt.replace("<PARTPERCAT>", partxcat);
}

function testoSpostata() {
	return "<?php echo $lang["accorpamento_spostata"]; ?>";
}

function testoRaggruppate() {
	return "<?php echo $lang["accorpamento_raggruppate"]; ?>";
}

<?php $view->stampaCategorieJavascript(); ?>
</script>
</head>

<?php 
$templ->apriBody();
?>
<form accept-charset="UTF-8"  method="post">
<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" />
	
<?php $view->pulsanti(); ?>
<br></br>

<?php if ($ctrl->getGara()->isIndividuale() && $ctrl->getGara()->isSquadre()) {?>
<p><a href="#indiv"><?php echo $lang["accorpa_indiv"]?></a><br>
<a href="#squadre"><?php echo $lang["accorpa_squadre"]?></a></p>
<?php } //if isIndividuale && isSquadre
if ($ctrl->getGara()->isIndividuale()) { ?>
<a name="indiv"></a><h1><?php echo $lang["accorpa_indiv"]?></h1>
<?php 
	$view->stampaCategorie($ctrl->getCategorieIndividuali(), true); 
}//if isIndividuale
if ($ctrl->getGara()->isSquadre()) { ?>
<a name="squadre"></a><h1><?php echo $lang["accorpa_squadre"]?></h1>
<?php 
	$view->stampaCategorie($ctrl->getCategorieSquadre(), false); 
}//if isSquadre

$view->pulsanti(); 
echo "</form>";

$templ->chiudiBody();
?>