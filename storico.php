<?php
session_start();

require_once("config.inc.php");
include_controller("storico");
include_view("Header", "Template");
$lang = Lingua::getParole();

$ctrl = new Storico();
$head = Header::titolo($lang["storico"]);
$head->setIndietroHome($ctrl->getUtente());
$head->setLogout($ctrl->loginEffettuato());

$templ = new Template($head);

$templ->stampaTagHead();
$templ->apriBody();
?>
<div id="Left" style="width:70%; border:none;">
<?php 
$list = $ctrl->getGare();
if (count($list) == 0)
	echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";
else {
	$tipout = $ctrl->getTipoUtente();
	foreach ($list as $gara) {
		$id = $gara->getChiave();
		$desc = $gara->getNome();
		$locandina= $gara->getLocandina();
		if ($gara->isIndividuale())
			$pag = "riepilogo";
		else
			$pag = "riepilogosq";
		switch ($tipout) {
			case Utente::SOCIETA:
				$url = "soc/{$pag}_completo.php";
				break;
			case Utente::RESPONSABILE:
				$url = "resp/$pag.php";
				break;
			case Utente::ORGANIZZATORE:
				$url = "org/$pag.php";
				break;
			default:
				$url = "";
				break;
		}
		
// 		echo "<li>";
// 		echo "<span class='tDescr'>$desc</span>";
// 		echo "<a  href=\""._PATH_ROOT_."dettagli.php?id=$id\">$lang[gara_dettagli]</a>";
// 		if ($url != "")
// 			echo "<a href=\"$url?id=$id\">$lang[elenco_partecipanti]</a>";
// 		echo "</li>";
		
			
		echo "<a name=\"gara$id\"></a><div id='Gare'>"; //TODO convertire id in class
		
		echo '<img src="'._PATH_ROOT_.$gara->getLocandina();
		echo '" style="'.$ctrl->getLocandinaSize($gara).':100px" class="locandina" >';
		echo "<h1>$desc</h1>";
		echo "<ul class='Gare_soc'>";
		if ($url != "")
			echo "<a href=\"$url?from=storico&id=$id\">$lang[elenco_partecipanti]</a>";
		echo "<a href=\""._PATH_ROOT_."dettagli.php?from=storico&id=$id\">$lang[gara_dettagli]</a>";
		echo "</ul>";
		echo "<div style='clear:both'></div>";
		
		echo "</div>";
		echo "<div style='clear:both'></div>";
	}
}
?>
</div>
<script type="text/javascript">
if (location.hash == "") {
	var patt=/id=(\d+)/;
	var result=patt.exec(location.search);
	if (result != null)
		location.hash = "gara"+result[1];
}
</script>
<?php
$templ->chiudiBody();
?>