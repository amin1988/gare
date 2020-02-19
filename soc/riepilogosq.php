<?php

function stampaTotali($stampa=true) {
	/* @var $ctrl RiepilogoSquadre */
	global $ctrl;
	global $lang;
	echo '<br><div class="totali';
	if (!$stampa)
		echo ' nostampa';
	echo "\">";
	
	if($ctrl->getNumSquadre() > 0)
		$tot_sq = $ctrl->getNumSquadre() * $ctrl->getPrezzoSquadra();
	else
		$tot_sq = 0;
	
	if($ctrl->getNumArb() > 0)
		$tot_arb = $ctrl->getNumArb() *- $ctrl->getRimborsoArb();
	else
		$tot_arb = 0;
	
	echo "<table>";
	echo "<tr><td align='right'>$lang[num_squadre_soc]: </td><td align='center'>" . $ctrl->getNumSquadre()."</td><td align='center'>$tot_sq &#8364</td></tr>";
	if($ctrl->getNumArb() > 0)
		echo "<tr><td align='right'>$lang[num_arb_conv]: </td><td align='center'>" . $ctrl->getNumArb()."</td><td align='center'>$tot_arb &#8364</td></tr>";
	echo "<tr><th align='right'>$lang[prezzo]:</th><th align='center' colspan=2> " . $ctrl->getPrezzoTotale();
	echo " &#8364;</th></tr>";
	echo "</table>";
	echo "</div><br>";


}

session_start();

require_once("../config.inc.php");
include_controller("soc/riepilogosq");
include_class("Sesso");
include_view("coach", "Header", "Template","arbitro");

$lang = Lingua::getParole();

$ctrl = new RiepilogoSquadre();
$coachView = new CoachView($ctrl);
$arbitroView = new ArbitroView($ctrl);
$head = Header::titolo($lang["riepilogo_titolo"], $ctrl->getGara()->getNome());
$head->setIndietro("soc",$lang["lista_gare"]);
$head->setStampa(true);
$templ = new Template($head);
$templ->includeJs("popup");

$modifica = !$ctrl->getGara()->iscrizioniChiuse();

$templ->stampaTagHead(false);
?>
<script type="text/javascript">
function mostraCatOriginale(nome) {
	showPopup("<?php echo $lang["categoria_originale"]; ?>:<br>"+nome);
}
</script>
</head>

<?php 
$templ->apriBody();

if ($modifica) {
	echo "<div class=\"pulsante tr\" style=\"text-align:center\">";
	echo "<a href=\""._PATH_ROOT_."soc/iscrivisq.php?id=".$ctrl->getGara()->getChiave()."\" class='pulsante_noInput'>$lang[modifica_iscrizioni]</a>\n";
	echo "<br></div><br><br>";
}

stampaTotali(false);
?>
<div class="Gare_soc_right"><h1><?php echo $lang["istr_pag"]; ?></h1></div>
<?php echo $ctrl->getIstruzioniPagamento(); ?>
<br><br>

<?php if(!_WKC_MODE_) { ?>
<div class="Gare_soc_right"><h1><?php echo $lang["arbitri"]; ?></h1></div>
<?php $arbitroView->stampaRiepilogo(); ?>
<br><br>
<?php } ?>

<div class="Gare_soc_right"><h1><?php echo $lang["coach"]; ?></h1></div>
<?php $coachView->stampaRiepilogo(); ?>
<br><br>

<?php 
if (!$ctrl->getGara()->listaPubblicata()) { 
	echo "<h2 style=\"color:red;text-decoration:underline;\">$lang[msg_non_accorpate]</h2>\n";
} 
?>
<div class="Gare_soc_right"><h1><?php echo $lang["squadre"]; ?></h1></div>
<table width="100%" class="atleti" id="atleti" >
<?php
$poolstr = $lang["pool"];
$pubbl = $ctrl->getGara()->listaPubblicata();
foreach ($ctrl->getSquadre() as $sq) {
	/* @var $sq Squadra */
	?>
	<tr class="tr">
	<th colspan="4"><div class='thSquadra'>
	<?php 
	echo $ctrl->getNomeSquadra($sq) .' - '. $ctrl->getNomeCategoria($sq); 
	if ($pubbl) {
		if ($sq->isAccorpato()) {
			$nomeorig = $ctrl->getNomeCategoriaOriginale($sq);
			echo ' <img style="cursor:pointer;" src="';
			echo _PATH_ROOT_."img/spostato.png\" title=\"$nomeorig\" onclick=\"javascript:mostraCatOriginale('$nomeorig')\">";
		} else if ($sq->isSeparato()) {
			echo ' - '.str_replace("<NUM>", $sq->getPool(), $poolstr);
			echo ' <img src="'._PATH_ROOT_.'img/separa.png">';
		}
	}
	?>
	</div></td>
	</tr>
		
<?php 
	$c=0;
	foreach ($ctrl->getComponenti($sq) as $a) {
		/* @var $a Atleta */
		if (($c % 2) == 0) $classe = "riga1";
		else $classe = "riga2";
		
		?>
	<tr class="<?php echo $classe; ?>">
	<td class="riepilogo_center"><?php echo $a->getCognome()." ".$a->getNome(); ?></td>
	<td class='riepilogo_center'><?php echo Sesso::toStringBreve($a->getSesso()); ?></td>
	<td class='riepilogo_center'><?php echo $a->getDataNascita()->format("d/m/Y"); ?></td>
	<td class='riepilogo_center'><?php echo $ctrl->getNomeCintura($sq, $a); ?></td>
	</tr>	
<?php	
		$c++;	
	}
}

echo '</table>';
stampaTotali();
$templ->chiudiBody();
?>