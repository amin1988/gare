<?php
session_start();

require_once("../config.inc.php");
include_controller("soc/modifica_squadre");
include_view("Header", "Template", "modifica_squadre");
$lang = Lingua::getParole();

$ctrl = new ModificaSquadre();
$view = new ModificaSquadreView($ctrl);
if ($ctrl->isNuova())
	$titolo = $lang["nuova_squadra"];
else
	$titolo = str_replace("<NUM>", $ctrl->getSquadra()->getNumero(), $lang["modifica_squadra_titolo"]);
$head = Header::titolo($titolo, $ctrl->getGara()->getNome());
$head->addIndietro("soc",$lang["lista_gare"]);
$head->addIndietro("soc/iscrivisq.php?id=".$ctrl->getGara()->getChiave(), $lang["iscrizioni_squadre"]);
$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX, "ajax", "popup", "cinture");

$templ->stampaTagHead(false);
?>
<style type="text/css" id="tipostyle">
<?php 
if ($ctrl->haDati()) { 
	$tipi = $ctrl->getTipiGara();
	$nome = strtolower($tipi[$ctrl->getTipo()]);
	echo ".no$nome { display: none; }\n";
	echo "#tab_comp .no$nome { display: table-row; color: red; }";
} else {
?> 
/*.nokata { display: none; }
.nosanbon { display: none; }
.noippon { display: none; }*/
.contipo { display: none; }
<?php } ?>
</style>
<script type="text/javascript">
var loadcat = true;
var tiposel = <?php 
		$tipo = $ctrl->getTipo();
		if ($tipo === NULL)
			echo '-1';
		else
			echo $tipo;
?>;

function setTipo(tipo,id) {
	<?php if(!_WKC_MODE_) { ?>
	document.getElementById("attesa_prestiti").style.display = "none";
	document.getElementById("no_prestiti").style.display = "none";
	document.getElementById("prestiti").style.display = "none";
	<?php } ?>
	tiposel = id;
	css = '.no'+tipo+' { display: none; }\n#tab_comp .no'+tipo+' { display: table-row; color: red; }\n';
	var style = document.getElementById("tipostyle");

	if(style.styleSheet){
	    style.styleSheet.cssText = css;
	}else{
	    style.replaceChild(document.createTextNode(css),style.childNodes[0]);
	}
}
/*
 
bisogna far sparire solo quello selezionato e lasciare gli altri...
bella rdc



 */ 

function fuoriquota(id,el) {
	var items = document.getElementsByClassName("FQ"),i;
	var atleta = document.getElementById("atl_"+id);
	var comp = document.getElementById("comp_"+id);
	if(el.className == "aggiungi") {
		if(atleta.classList.contains("FQ"))
			{//se è un fuoriquota faccio sparire gli altri
				for(i=0; items.length; i++)
				{
					if((items[i].id != atleta.id) && (items[i].id != comp.id))
						{
							items[i].style.display = "none";
						}
				}
			}
		else
			{//non faccio sparire nessuno, non faccio nulla!
			}
	}
	
	if(el.className == "rimuovi")
	{
		if(atleta.classList.contains("FQ"))
			{//se è un fuoriquota faccio ricomparire gli altri
				for(i=0; items.length; i++)
				{
					if((items[i].id != atleta.id) /*&& (items[i].id != comp.id)*/)
						{
							items[i].style.display = "";
						}
				}
			}
		else
			{//non faccio ricomparire nessuno nessuno, non faccio nulla!
			}
	}
}

function modificaComponente(id,el) {
	if (el.className == "aggiungi") {
		var atl = document.getElementById("atl_"+id);
		var comp = atl.cloneNode(true);
		atl.style.display="none";
		
		comp.id = "comp_"+id;
		//comp.className="riga1";
		
		var modcol = comp.getElementsByClassName("aggiungi")[0];
		modcol.className="rimuovi";
		var hidden = document.createElement("input");
		hidden.type = "hidden";
		hidden.name = "comp["+id+"]";
		hidden.value = id;
		modcol.appendChild(hidden);
		
		document.getElementById("tab_comp").appendChild(comp);
	} else {
		var atl = document.getElementById("atl_"+id);
		atl.style.display = "";
		var comp = document.getElementById("comp_"+id);
		comp.parentNode.removeChild(comp);
	}
}

function indietro() {
	showConfirm("<?php echo $lang["annulla_squadra"]; ?>", function(val) {
		if (val == 0) return;
		window.open("<?php echo _PATH_ROOT_."soc/iscrivisq.php?id=".$ctrl->getIdGara(); ?>","_self");
	});
}

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
		cont.innerHTML = '<div align="center"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif"></div>';
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/categorie.php?id=<?php echo $ctrl->getIdGara(); ?>", cont, mostraCat);
	}
}

function mostraCat(json, cont) {
	if (json == "null") return;
	
	var res = JSON.parse(json);
	var catlist = res[0];
	var txt = "<ul>";
	for (i in catlist) {
		txt += '<li class="';
		switch(catlist[i].tipo) {
			case 0:
				txt += "nosanbon noippon";
				break;
			case 1:
				txt += "nokata noippon";
				break;
			case 2:
				txt += "nokata nosanbon";
				break;
		}
		txt += '">'+catlist[i].nome;
	}
	cont.innerHTML = txt+"</ul>";
	loadcat = false;
}

function nonverificato(url) {
	txt  = "<?php echo addslashes($lang["non_verificato"]); ?>";
	showPopup(txt.replace("<URL>",url));
}

function baseUrl() {
	return "<?php echo _PATH_ROOT_; ?>";
}

function uscito() { //TODO lingua
	showPopup("Questo atleta &egrave; stato prestato ad un'altra societ&agrave;");
}

<?php if(!_WKC_MODE_){?>

function cercaPrestito() {
	var nome = document.getElementById("ricerca_prestito").value;
	document.getElementById("attesa_prestiti").style.display = "block";
	document.getElementById("no_prestiti").style.display = "none";
	document.getElementById("prestiti").style.display = "none";
	var params = "idg=<?php echo $ctrl->getIdGara(); ?>&tipo="+tiposel
			+ "&ids=<?php echo $ctrl->getIdSocieta(); ?>&ricerca="+escape(nome);
<?php 
	if (!$ctrl->isNuova())
		echo 'params += "&idsq='.$ctrl->getSquadra()->getChiave().'";';
?>
	ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/cercaatl.php?"+params, null, risultatiPrestito);
}

function risultatiPrestito(json, dummy) {
	var res = JSON.parse(json);

	document.getElementById("attesa_prestiti").style.display = "none";
	var tab = document.getElementById("prestiti");
	tab.tBodies[0].innerHTML = "";
	if (res.length == 0) {
		document.getElementById("no_prestiti").style.display = "block";
		tab.style.display = "none";
		return;
	}
	document.getElementById("no_prestiti").style.display = "none";
	tab.style.display = "table";
	for(var i=0; i<res.length; i++) {
		var id = res[i].id;
		var row = tab.tBodies[0].insertRow(-1);
		row.className = "riga1";
		row.id = "cerca_"+id;
		if (document.getElementById("pres_"+id) != null)
			row.style.display = "none";
		
		var td = row.insertCell(-1);
		if (res[i].stato == 1) {
			td.className="aggiungi";
			td.setAttribute("onclick","modificaPrestito("+id+",this)");
			var hidden = document.createElement("input");
			hidden.type = "hidden";
			hidden.name = "pres_soc["+id+"]";
			hidden.value = res[i].idsoc;
			td.appendChild(hidden);
		} else {
			td.className="bloccato";
			td.onclick = function() { showPopup("Questo atleta non pu&ograve; essere prestato"); } //TODO lingua
		}
		
		td = row.insertCell(-1);
		td.className="cognome";
		td.innerHTML = res[i].cognome;
		
		td = row.insertCell(-1);
		td.className="nome";
		td.innerHTML = res[i].nome;
		
		td = row.insertCell(-1);
		td.className="sesso";
		td.innerHTML = res[i].sesso;
		
		td = row.insertCell(-1);
		td.className="nascita";
		td.innerHTML = res[i].nascita;
		
		td = row.insertCell(-1);
		td.className="cintura";
		td.innerHTML = res[i].cintura;
		
		td = row.insertCell(-1);
		td.className="nome";
		td.innerHTML = res[i].societa;
	}
}

function modificaPrestito(id,el) {
	if (el.className == "aggiungi") {
		var atl = document.getElementById("cerca_"+id);
		var comp = atl.cloneNode(true);
		atl.style.display="none";
		
		comp.id = "pres_"+id;
		comp.deleteCell(-1);
		//comp.className="riga1";

		var img = document.createElement("img");
		img.src = baseUrl()+"img/prestito.gif";
		img.style.marginRight = "5px";
		img.title = "Prestito"; //TODO lingua
		var modcol = comp.getElementsByClassName("cognome")[0];
		modcol.insertBefore(img, modcol.childNodes[0]);
		
		modcol = comp.getElementsByClassName("aggiungi")[0];
		modcol.className="rimuovi";
		
		var hidden = document.createElement("input");
		hidden.type = "hidden";
		hidden.name = "pres["+id+"]";
		hidden.value = id;
		modcol.appendChild(hidden);
		
		document.getElementById("tab_comp").appendChild(comp);
	} else if (el.className == "rimuovi") {
		var comp = document.getElementById("pres_"+id);
		comp.parentNode.removeChild(comp);
		var atl = document.getElementById("cerca_"+id);
		if (atl != null) atl.style.display = "";
		
	}
}
<?php }?>
</script>
</head>

<?php 
$templ->apriBody();
?>

<form accept-charset="UTF-8" method="post">
<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" />
<div class="pulsante tr">
<input  type="button" value="<?php echo $lang["indietro"]; ?>" onclick="indietro()" />
<div class="separatore_pulsante"></div>
<input type="submit" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
</div>
<br><br>
<?php
echo "$lang[tipo_gara]: ";
$view->stampaTipi(); 
?>

<p class="nokata nosanbon noippon"><?php echo $lang["selezionare_tipo_squadra"]?></p>

<h2>
<img src="<?php echo _PATH_ROOT_; ?>img/down.png" class="mostra" id="mostra_cat" onclick="javascript:apriCat();">
<?php echo $lang["lista_categorie"]; ?></h2>
<div id="catlist"></div>

<?php 
$err = $ctrl->getErrori();
if ($err->haErrori()) {
	if ($err->isErrato(VerificaSquadra::MULTI_CAT))
		$view->stampaListaCategorie();
	else
		echo '<p class="err">'.$err->toString().'</p>';
}
?>
<div class="contipo">
<div class="Gare_soc_right"><h1><?php echo $lang["componenti_squadra"]?></h1></div>
<?php
$view->stampaComponenti();

$desc = str_replace("<ADD>", '<img src="'._PATH_ROOT_.'img/add.png" style="vertical-align: middle">', $lang["spiegazione_modifica_squadre"]);
$desc = str_replace("<DEL>", '<img src="'._PATH_ROOT_.'img/del.png" style="vertical-align: middle">', $desc);
echo "<p>$desc</p>";
?>
</form>

<br><br>
<?php if(!_WKC_MODE_){?>
<div class="Gare_soc_right"><h1>Prestiti</h1></div> <!-- //TODO lingua -->
<form onsubmit="cercaPrestito(); return false;">
Nome: <input type="text" id="ricerca_prestito"> <input type="submit" value="Cerca">
</form>
<div align="center" id="attesa_prestiti" style="display:none"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif"></div>
<div align="center" id="no_prestiti" style="display:none">Nessun risultato</div>
<?php $view->iniziaTabella("prestiti","display:none",true); ?>
<tbody></tbody>
</table>
<?php }?>

<br><br>
<div class="Gare_soc_right"><h1><?php echo $lang["altri_atleti"]?></h1></div>
<?php $view->stampaAtleti(); ?>
</div>

<?php 
$templ->chiudiBody();
?>