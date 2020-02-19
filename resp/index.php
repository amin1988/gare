<?php
//STAMPA
session_start();

require_once("../config.inc.php");
include_controller("listagare_backend");
include_view("Template");
$lang = Lingua::getParole();

/**
 * @param Gara $gara
 */
function stampaRigaGara($gara, $desc, $chiusa) {
	global $lang;
	
	$id = $gara->getChiave();
	$data = $gara->getDataGara()->format("d/m/Y");
	if ($gara->isIndividuale()) {
		if ($gara->isSquadre()) {
			$js = "riepilogoDoppio($id)";
		} else {
			$js = "riepilogoSingolo($id,true)";
		}
	} else {
		$js = "riepilogoSingolo($id,false)";
	}
	
	echo '<li class="hilight"><table width="100%"><tr><td>';
	echo "<span class='tDescr'>$data - $desc</span></td><td class=\"liButton\">";
	echo "<a href=\"download_iscrizioni.php?id=$id\">$lang[scarica_iscrizioni]</a>";
	if ($chiusa)
		echo "<a href=\"accorpa.php?id=$id\">$lang[accorpa_titolo]</a> ";
	echo "<a href=\"javascript:$js\">$lang[gara_riepilogo]</a> ";
	echo "<a  href=\""._PATH_ROOT_."dettagli.php?id=$id\">$lang[gara_dettagli]</a> ";
	echo "</td></tr></table></li>";
}

$ctrl = new ListaGareBackend(true);
$templ = Template::titolo($lang["lista_gare"]);
$templ->includeJs("popup");

$templ->stampaTagHead(false);
?>
<script type="text/javascript">
function riepilogoSingolo(id, indiv) {
	if (indiv)
		url = 'riepilogo.php?id='+id;
	else
		url = 'riepilogosq.php?id='+id;
	showList('',['<?php echo $lang["riepilogo_societa"] ?>', 
	         	'<?php echo $lang["riepilogo_categorie"] ?>',
	         	'<?php echo $lang["statistiche_titolo"] ?>'],
			['riepilogo_soc.php?id='+id, url, 'stat.php?id='+id]);
}
function riepilogoDoppio(id) {
	showList('',['<?php echo $lang["riepilogo_societa"] ?>', 
		         	'<?php echo $lang["riepilogo_individuali"] ?>',
		         	'<?php echo $lang["riepilogo_squadre"] ?>',
		         	'<?php echo $lang["statistiche_titolo"] ?>'],
			['riepilogo_soc.php?id='+id, 'riepilogo.php?id='+id,
			 'riepilogosq.php?id='+id, 'stat.php?id='+id]);
}
</script>
</head>
<?php 
$templ->apriBody();
?>
      <div id="Right" style="width:90%;">

        <div class='Gare_soc_right'>
<?php 
$chiuse = $ctrl->getGareChiuse();
if (count($chiuse) > 0) {
	echo "<h1>$lang[iscrizioni_chiuse]</h1>";
	foreach ($ctrl->getGareChiuse() as $gara) {
		stampaRigaGara($gara, $gara->getNome(), true);
	}
}

$attive = $ctrl->getGareAttive();
if (count($attive) > 0) {
	echo "<h1>$lang[gare_attive]</h1>";
	$chlow = strtolower($lang["chiusura_iscrizioni"]);
	foreach ($ctrl->getGareAttive() as $gara) {
		$c = $gara->getChiusura()->format("d/m");
		stampaRigaGara($gara, $gara->getNome() . " ($chlow $c)", false);
	}
}
?>
        </div>
      </div>
<div class="pulsante tr" style="text-align:center">
<a href="<?php echo _PATH_ROOT_; ?>storico.php" class="pulsante_noInput"><?php echo $lang["storico"]; ?></a>
</div>

<?php 
$templ->chiudiBody();
?>