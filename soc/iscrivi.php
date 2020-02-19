<?php
session_start();

require_once("../config.inc.php");
include_controller("soc/iscrivi");
include_view("iscrivi", "coach", "Header", "Template","arbitro");
$lang = Lingua::getParole();

$ctrl = new Iscrivi();
$view = new IscriviView($ctrl);
$coachView = new CoachView($ctrl);
$arbView = new ArbitroView($ctrl);
if ($ctrl->getGara()->isSquadre()) {
	$titolo = $lang["iscrizioni_individuali"];
	$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
	$head->addIndietro("soc",$lang["lista_gare"]);
	$head->addIndietro("soc/scegli.php?id=".$_GET["id"],$lang["iscrizioni_titolo"]);
} else {
	$titolo = $lang["iscrizioni_titolo"];
	$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
	$head->setIndietro("soc",$lang["lista_gare"]);
}
$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX, "ajax", "popup", "cinture");
if ($view->usaCalendario()) {
	$templ->includeJs(Template::CALENDAR);
}

$nuoviCampi = $ctrl->nuoviCampi();

$templ->stampaTagHead(false);

$coachView->stampaJs();

if ($view->usaCalendario()) {
?>
<!-- CALENDARIO -->
<script type="text/javascript">

function setCalendar(txt){
	cal = new CalendarEightysix(txt, {
		 'startMonday': true, 
		 'defaultView': 'decade',
		 'alignX': 'left', 
		 'alignY': 'bottom', 
		 'offsetX': -4, 
		 'format': '%d/%m/%Y',
		 'defaultDate':'',
		 'prefill':false
		 });
	//txt.value="";
	//calendars.push(cal);
}

document.addEvent('domready', function() {
<?php 
	$dl = Lingua::getLinguaDefault();
	if ($dl != "en")
		echo "MooTools.lang.setLanguage('$dl');";
?>
	for(i=0; i < <?php echo $nuoviCampi; ?>; i++) {
		setCalendar(document.getElementById("nascita_"+i));
	}
});	


</script>
<!-- FINE CALENDARIO -->
<?php 
} //if usaCalendario

if ($ctrl->puoAggiungereCampi()) { 
?>
<script type="text/javascript">
<!--
var newid = <?php echo $nuoviCampi; ?>;
var numriga = <?php echo $ctrl->numRigaJs(); ?>;

function aggiungiCampi() {
	var t = document.getElementById('atleti');
	for(i=0; i<10; i++){
		var node=document.createElement("tr");
		creaRiga(node,newid);
		numriga++;
		node.className="riga"+numriga;
		numriga = numriga % 2;
		t.appendChild(node);
		newid++;
	}
}

function creaRiga(riga,id) {
	//return '
	<?php $view->stampaRigaAtleta(new JavascriptFieldFiller($ctrl)) ?>
	//';
}
-->
</script>
<?php } //if puoAggiungereCampi ?>
<script type="text/javascript">
var loadcat = true;

function checkKumite() {
	alert("prova");
}

function apriCat() {
	var cont = document.getElementById("catlist");
	apri(cont,"mostra_cat");
	if (loadcat) {
		cont.innerHTML = '<div align="center"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif"></div>';
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/categorie.php?id=<?php echo $ctrl->getGara()->getChiave(); ?>", cont, mostraCat);
	}
}

function mostraCat(json, cont) {
	if (json == "null") return;
	
	var res = JSON.parse(json);
	var txt = "<ul>";
	for (i=0; i<res[1].length; i++) {
		txt += '<li>'+res[1][i].nome;
	}
	cont.innerHTML = txt+"</ul>";
	loadcat = false;
}


function nonverificato(url) {
	txt  = "<?php echo str_replace('"', '\\"', $lang["non_verificato"]); ?>";
	showPopup(txt.replace("<URL>",url));
}

function baseUrl() {
	return "<?php echo _PATH_ROOT_; ?>";
}
</script>
<style>
tr.err {
background-color: yellow;
}

.err {
background-color: red;
}

<?php if (!$ctrl->haHandicap()) {?>
.handicap {
	display:none !important;
}
<?php } //if !haHandicap?>
</style>
</head>

<?php 
$templ->apriBody();
?>
<form accept-charset="UTF-8"  method="post" enctype="multipart/form-data">
	<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" />
	
	<div class="pulsante tr">
	<input type="submit" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
</div>
<br><br>

<h2>
<img src="<?php echo _PATH_ROOT_; ?>img/down.png" class="mostra" id="mostra_cat" onclick="javascript:apriCat();">
<?php echo $lang["lista_categorie"]; ?></h2>
<div id="catlist" style="display:none"></div>
<br><br>

<?php 
$ut = Utente::crea();
$id_s = $ut->getIdSoc();
$id_a = Societa::idAffFromId($id_s);
if($id_a === NULL)
	$id_a = -1;

if(!_WKC_MODE_)
{
?>

<div id="Right" style="width:95%;">
<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["arbitri"] ?></h1></div> 
<?php 

if ($ctrl->getErroriArbitri() !== NULL) {
	echo '<span style="color:red;">'.$ctrl->getErroriCoach()->toStringNum().'</span>'; //TODO ARBITRI
}
if($id_a != -1)//societ� FIAM
	$arbView->stampaArbitri($ctrl->getGara()->getChiave(),$id_a);//DEBUG
else //societ� esterna
	$arbView->stampaArbitri($ctrl->getGara()->getChiave(),$id_a, $ut->getIdSoc());//echo "<h2>".$lang["non_disp"]."</h2>";
?>
</div>
<br><br>

<?php 
}
?>

<div id="Right" style="width:95%;">
<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["coach"]; ?></h1></div>
<?php 
if ($ctrl->getErroriCoach()->haErroreNum()) {
	echo '<span style="color:red;">'.$ctrl->getErroriCoach()->toStringNum().'</span>';
}
if($id_a != -1)
	$coachView->stampaSelezioneCoach();
else 
	$coachView->stampaSelezioneCoachEsterna();
?>
</div>
<br><br>

<div class="Gare_soc_right"><h1><?php echo $lang["atleti"]; ?></h1></div>
<table class="atleti" id="atleti">
<tr  class="tr">

<!-- <th></th>  -->
<?php if ($ctrl->haNonVerificati()) { ?>
<th><div class='thAtleti '></div></th>
<?php } //if haNonVerificati ?>

<th><div class='thAtleti '><?php echo $lang["cognome_iscrizioni"]; ?></div></th>
<th><div class='thAtleti'><?php echo $lang["nome_iscrizioni"]; ?></div></th>
<th><div class="thAtleti" ><?php echo $lang["sesso_iscrizioni"]; ?></div></th>
<th><div class='thAtleti'><?php echo $lang["nascita_iscrizioni"]; ?></div></th>
<th><div class='thAtleti'><?php echo $lang["cintura_iscrizioni"]; ?></div></th>
<th><div class="thAtleti" >Kata</div></th>
<th><div class="thAtleti" >Kata&nbsp;Rengokai</div></th>
<th><div class="thAtleti">Shobu&nbsp;Sanbon</div></th> 
<th><div class="thAtleti">Shobu&nbsp;Kumite</div></th>
<th><div class="thAtleti">Shobu&nbsp;Ippon</div></th>
<th class="handicap"><div class="thAtleti">Handicap</div></th>
<th><div class='thAtleti'><?php echo $lang["stile_iscrizioni"]; ?></div></th>
<th><div class='thAtleti'><?php if ($ctrl->usaPeso()) echo $lang["peso_iscrizioni"]; else echo $lang["altezza_iscrizioni"]; ?></div></th>


</tr>



<?php



$c = 0;
$ff = new DbFieldFiller($ctrl);
foreach ($ctrl->getAtletiOk() as $a) {
	$ff->setAtleta($a);
	$view->stampaRigaAtleta($ff, ($c%2)==0);
	$c++;
}

$ff = new NuovoFieldFiller($ctrl);
for ($i=0; $i < $nuoviCampi; $i++) {
	$ff->setId($i);
	$view->stampaRigaAtleta($ff, ($c%2)==0);
	$c++;
}
?>
</table>
<?php if ($ctrl->puoAggiungereCampi()) { ?>
<div class="pulsante " style="text-align:left">
<input type="button" onclick="aggiungiCampi();" value="<?php echo $lang["agg_campi"]; ?>" />
<br /><br />
</div>
<?php } //if puoAggiungereCampi ?>
<div class="pulsante tr" style="text-align:center">

	<input type="submit" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
	</div>
	
</form>
<?php 
$templ->chiudiBody();
?>