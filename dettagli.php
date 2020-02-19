<?php
session_start();

require_once("config.inc.php");
include_controller("dettagligara");
include_view("Header", "Template");
$lang = Lingua::getParole();

$ctrl = new DettagliGara();
$head = Header::titolo($lang["gara_dettagli"]);
if (isset($_GET["from"])) $from = $_GET["from"];
else $from = "";
switch ($from) {
	case "storico":
		$head->setIndietro("storico.php#gara".$_GET["id"]);
		break;
	default:
		$head->setIndietroHome($ctrl->getUtente());
}
$head->setLogout($ctrl->loginEffettuato());

$templ = new Template($head);
$templ->includeJs("ajax");
$templ->setBodyDiv(false);

$templ->stampaTagHead(false);
?>
<script type="text/javascript">
var loadcat = true;

function apriCat() {
	var cont = document.getElementById("catlist");
	var img = document.getElementById("mostra_cat");
	if (cont.style.display == "block") {
		cont.style.display="none";
		img.src = "<?php echo _PATH_ROOT_; ?>img/down.png";
	} else {
		cont.style.display="block";
		img.src = "<?php echo _PATH_ROOT_; ?>img/up.png";
	}
	if (loadcat) {
		cont.innerHTML = '<div align="center"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif" style="float: none;top: 0px;"></div>';
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/categorie.php?id=<?php echo $ctrl->getIdGara(); ?>", cont, mostraCat);
	}
}

function mostraCat(json, cont) {
	if (json == "null") return;
	
	var res = JSON.parse(json);
	txt = "";
	<?php if ($ctrl->isDoppioTipo()) {?>
	//sia individuale che squadre
	txt += "<b><?php echo $lang["gruppicat_gara_indiv"]; ?></b>";
	txt +=stampaCat(res[1]);
	txt += "<b><?php echo $lang["gruppicat_gara_squadre"]; ?></b>";
	txt +=stampaCat(res[0]);
		<?php } else {
		if ($ctrl->isIndividuale()) { ?>
	//solo individuale
	txt += stampaCat(res[1]);
	<?php } else { ?>
	//solo squadre
	txt += stampaCat(res[0]);
	<?php } }?>
	cont.innerHTML = txt;
	loadcat = false;
}

function stampaCat(catlist) {
	var txt = "<ul>";
	for (i in catlist) {
		txt += "<li>"+catlist[i].nome;
	}
	return txt+"</ul>";
}
</script>
</head>

<?php 
$templ->apriBody();
?>
<div id="Left" style="width:48%;float:left;left:5px;">



<div class="Titoli_H1"><h1><?php echo $ctrl->getGara()->getNome(); ?></h1></div>
<div class="dettagli">
<p class="dettaglioG"><b><?php echo $lang["data_gara"]; ?>:</b> 
<?php 

$g = $ctrl->getGara();
$di = $g->getDataGara();
$df = $g->getDataFineGara();
if (is_null($df)) {
	echo $di->format("d/m/Y");
} else {
	if ($di->getAnno() != $df->getAnno()) {
		echo $di->format("d/m/Y");
	} else if ($di->getMese() != $df->getMese()) {
		echo $di->format("d/m");
	} else {
		echo $di->format("d");
	}
	echo " - ".$df->format("d/m/Y");
}

?></p>
<p class="dettaglioG"><b><?php echo $lang["chiusura_iscrizioni"]; ?>:</b> 
<?php echo $ctrl->getGara()->getChiusura()->format("d/m/Y") ?></p>

<?php 
if($ctrl->getGara()->getDescrizione()!=''){
?>
<div class="dettaglioG">
<p><?php echo $ctrl->getGara()->getDescrizione(); ?></p>
</div>
<?php
}

?>

<?php if ($ctrl->mostraModulo()) {
	if ($ctrl->isDoppioTipo())
		$url = "scegli";
	else if ($ctrl->isIndividuale())
		$url = "iscrivi";
	else
		$url = "iscrivisq";
	$url .= ".php?id=".$ctrl->getGara()->getChiave(); 
?>
<p class="dettaglioG"><a href="soc/<?php echo $url; ?>"><?php echo $lang["gara_iscrizioni"]; ?></a></p>
<?php } else if ($ctrl->mostraModifica()) {?>
<p class="dettaglioG"><a href="org/modifica.php?id=<?php echo $ctrl->getGara()->getChiave(); ?>"><?php echo $lang["modifica_gara"]; ?></a></p>
<?php } //if modificaModulo ?>
</div>


<?php 
if ($ctrl->haAllegati()) {?>
	<div class="dettagli">
	
	<b><?php echo $lang["documenti"]; ?>:</b>
	<ul><?php
	
	foreach ($ctrl->getAllegati() as $doc) {
		echo "<li class='dettaglioG' style=\"list-style-image: url("._PATH_ROOT_."img/icone/".$doc->getTipo()."); list-style-position:inside;\">";
		echo "<a href=\"".$ctrl->getAllegatoUrl($doc)."\" target=\"_blank\">".$doc->getNome()."</a></li>\n";
	}
	?>
	</ul>
	</div>
<?php } //if haAllegati ?>

<div class="dettagli" >

<img src="<?php echo _PATH_ROOT_; ?>img/down.png" class="mostra" id="mostra_cat" onclick="javascript:apriCat();">

<b style="line-height:24px;margin-left:4px;"><?php echo $lang["lista_categorie"]; ?>:</b>

<div class="dettaglioG">
<div id="catlist"></div>
</div>
</div>

</div>
<div id="Right" style="position:relative;width:50%;top:0px;right:0;float:right">
<img src="<?php echo $ctrl->getLocandinaUrl(); ?>" height="300" />

<div style="clear:both"><br></br></div>

</div>

<br>


<br>
<div style="clear:both"></div>
<?php 
$templ->chiudiBody();
?>