<?php
//MODIFICA STILE
session_start();

function stampaPulsanti() {
	global $ctrl, $lang;
?>
<div class="pulsante tr" style="text-align:center">
<a href="modsq.php?idg=<?php echo $ctrl->getGara()->getChiave(); ?>" class='pulsante_noInput'><?php echo $lang["nuova_squadra"]; ?></a>
<?php if ($ctrl->haSquadre()) { ?> 
<div class="separatore_pulsante"></div>
<a href="riepilogosq.php?id=<?php echo $ctrl->getGara()->getChiave(); ?>" class='pulsante_noInput'><?php echo $lang["gara_riepilogo"]; ?></a><br>
<?php } //if haSquadre ?>
</div>

<?php
} //stampaPulsanti

require_once("../config.inc.php");
include_controller("soc/iscrivisq");
include_view("coach", "Header", "Template","arbitro");
$lang = Lingua::getParole();

$ctrl = new IscriviSquadre();
$coachView = new CoachView($ctrl, "coachChange()");
$arbView = new ArbitroView($ctrl);
if ($ctrl->getGara()->isIndividuale()) {
	$titolo = $lang["iscrizioni_squadre"];
	$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
	$head->addIndietro("soc",$lang["lista_gare"]);
	$head->addIndietro("soc/scegli.php?id=".$_GET["id"],$lang["iscrizioni_titolo"]);
} else { 
	$titolo = $lang["iscrizioni_titolo"];
	$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
	$head->setIndietro("soc",$lang["lista_gare"]);
}
$templ = new Template($head);
$templ->includeJs("ajax","popup",Template::CHECKBOX);

$templ->stampaTagHead(false);

$coachView->stampaJs();
?>
<script type="text/javascript">
var coachmod = false;

function bloccaLink() {
	var x=document.getElementsByTagName("a");
	for(var i=0; i<x.length; i++) {
		if (x[i].onclick == null && x[i].href.indexOf("javascript:") != 0) 
			x[i].setAttribute("onclick","return checkExit(\""+x[i].href+"\")");
	}
}

function pageUnload() {
	if (coachmod) return "<?php echo $lang["coach_modificati"]; ?>";
}

function checkExit(url) {
	if (!coachmod) return true;
	var r = confirm("<?php echo $lang["coach_modificati_domanda"]; ?>");
	if (r) coachmod = false; //ignora le modifiche
	return r;
}

function coachChange() {
	coachmod = true;
}

function mostraComp(id) {
	var comp = document.getElementById("comp_"+id);
	var img = document.getElementById("mostra_"+id);
	if (comp.style.display == "block") {
		comp.style.display="none";
		img.src = "<?php echo _PATH_ROOT_; ?>img/down.png";
	} else {
		if (comp.innerHTML == "") caricaComp(comp,id);
		comp.style.display="block";
		img.src = "<?php echo _PATH_ROOT_; ?>img/up.png";
	}
}

function caricaComp(cont,id) {
	cont.innerHTML = '<ul><li><div align="center"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif"></div></ul>';
	ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/compsq.php?id="+id, cont, stampaComp);
}

function stampaComp(json,cont) {
	if (json == "null") {
		cont.innerHTML = "&nbsp;";
		return;
	} 
	var res = JSON.parse(json);
	var txt = "<ul>";
	for(i in res) {
		txt += "<li>" + res[i].cognome + " " + res[i].nome;
	}
	cont.innerHTML = txt + "</ul>";
}

function elimina(id,num) {
	msg = "<?php echo $lang["elimina_squadra_domanda"]; ?>";
	showConfirm(msg.replace("<NUM>",num), function(val){
		if (val == 0) return;
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/delsq.php?id="+id, id, cancSquadra);
	});
}

function cancSquadra(resp, id) {
	if (resp != "1") return;
	var el = document.getElementById("squadra_"+id);
	el.style.display="none";
	el = document.getElementById("comp_"+id);
	el.style.display="none";
	showPopup("<?php echo $lang["squadra_eliminata"]; ?>");
}
</script>
</head>

<?php 
$templ->apriBody('onload="Custom.init(); bloccaLink();" onbeforeunload="return pageUnload();"');
stampaPulsanti(); 
?>
<br><br>

<?php if(!_WKC_MODE_) { ?>

<div id="Right" style="width:95%;">
<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["arbitri"] ?></h1></div>
<form accept-charset="UTF-8"  method="post" onsubmit="coachmod=false;" enctype="multipart/form-data">
	<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" /> 
<?php 
$ut = Utente::crea();
$id_s = $ut->getIdSoc();
$id_a = Societa::idAffFromId($id_s);
if ($ctrl->getErroriCoach()->haErroreNum()) {
	echo '<span style="color:red;">'.$ctrl->getErroriCoach()->toStringNum().'</span>'; //TODO ARBITRI
}
$arbView->stampaArbitri($ctrl->getGara()->getChiave(),$id_a);//DEBUG
?>
<div class="pulsante tr">
<input type="submit" name="salva_arb" value="<?php echo $lang["salva_arb"]; ?>" />
</div>
<br><br>

</form>

<?php } ?>

<div class="Gare_soc_right"><h1><?php echo $lang["coach"]; ?></h1></div>
<form accept-charset="UTF-8"  method="post" onsubmit="coachmod=false;" enctype="multipart/form-data">
	<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" />
	
<?php 
if ($ctrl->getErroriCoach()->haErroreNum()) {
	echo '<span style="color:red;">'.$ctrl->getErroriCoach()->toStringNum().'</span>';
}
if($ctrl->getUtenteSocieta()->getSocieta()->isAffiliata())
	$coachView->stampaSelezioneCoach();
else 
	$coachView->stampaSelezioneCoachEsterna();
?>
<div class="pulsante tr">
<input type="submit"  name="salva_coach" value="<?php echo $lang["salva_coach"]; ?>" />
</div>
<br><br>

</form>
<?php 
if ($ctrl->haSquadre()) { 
	echo '<br><br><div id="Right" style="width:90%;">';
	echo '<div class="Gare_soc_right">';
	echo "<h1>$lang[squadre]</h1>";
	foreach ($ctrl->getSquadre() as $id => $sq) {
		$num = $sq->getNumero();
		$cat = $ctrl->getNomeCategoria($sq);
		$squad = Lingua::getParola("squadra");
		echo "<li id=\"squadra_$id\">";
		echo "<img src=\""._PATH_ROOT_."img/down.png\" class=\"mostra\" id=\"mostra_$id\" onclick=\"javascript:mostraComp('$id');\">";
		echo "<span class='tDescr'> $squad $num - $cat</span>";
		echo "<a  href=\"#\" onclick=\"elimina($id,'$num')\">$lang[elimina_squadra]</a>";
		echo " <a href=\"modsq.php?ids=$id\">$lang[modifica_squadra]</a> ";
		echo "</li>\n<div class=\"componenti\" id=\"comp_$id\" style=\"display:none;\">";
		echo "</div>\n";
	}
	echo '</div></div>';
	stampaPulsanti();
}
$templ->chiudiBody(); 
?>
