<?php
if (!defined("_BASEDIR_")) exit();
include_view("SelectZone");

class GestioneUtenteView {
	const SOC_COLS = 4;
	
	/**
	 * @var GestioneUtente
	 */
	private $ctrl;
	/**
	 * @var VerificaUtente
	 */
	private $err;
	
	/**
	 * @var SelectZone
	 */
	private $zonesel;
	
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
		$this->err = $ctrl->getErrori();
		$this->zonesel = new SelectZone(false,true);
	}
	
	public function stampaJavascript($tipo=NULL) {
		$this->zonesel->stampaJavascript();
?>
<script type="text/javascript" src="<?php echo _PATH_ROOT_; ?>js/ajax.js"></script> 
<script type="text/javascript">
var socload=<?php if ($this->ctrl->isSocietaSelezionata()) echo "true"; else echo "false"; ?>;

function mostraErr(id,mostra) {
	if (mostra)
		document.getElementById(id).style.display = "inline";
	else
		document.getElementById(id).style.display = "none";
	return !mostra;
}

function checkCampi() {
	var corretto = true;
	//TODO controllo username doppio
	
	//controllo password diverse
	var psw1 = document.getElementById("psw").value;
	var psw2 = document.getElementById("psw2").value;
	corretto = mostraErr("<?php echo VerificaUtente::PSW_DIFF; ?>", (psw1 != psw2)) && corretto;

	<?php if (is_null($tipo)) { ?>
	var tipo = document.getElementById("tipo").value;
	<?php } else { ?>
	var tipo = <?php echo $tipo; ?>;
	<?php } //if tipo == NULL ?>
	if (tipo == <?php echo Utente::SOCIETA ?>) {
		//controllo societa
		socrad = document.getElementById("form_utente").elements.soc;
		socsel = false;
		if (socrad != undefined) {
			for(i in socrad) {
				if (socrad[i].checked) {
					socsel = true;
					break;
				}
			}
		}
		corretto = socsel && corretto;
		mostraErr("soc", !socsel);
	} else if (tipo == <?php echo Utente::ORGANIZZATORE ?> 
			|| tipo == <?php echo Utente::RESPONSABILE; ?>
			|| tipo == <?php echo Utente::VISUALIZZA; ?>) {
		//controllo zone
		zchk = document.getElementsByTagName("input");
		zonasel = false;
		for(i=0; i<zchk.length; i++) {
			if (zchk[i].name.substr(0,5) == "zona[" && zchk[i].checked) {
				zonasel = true;
				break;
			}
		}
		corretto = zonasel && corretto;
		mostraErr("zona", !zonasel);
	}
	return corretto;
}

function addZona(idsel) {
	var sel = document.getElementById("subzone_"+idsel);
	if (sel.value == "") return;
	var id = "zona_"+sel.value;
	var old = document.getElementById(id);
	if (old != null) {
		old.checked = true;
		return;
	}
	var lista = document.getElementById("listazone");
	var chk = document.createElement("input");
	chk.type = "checkbox";
	chk.name="zona["+sel.value+"]";
	chk.id = id;
	chk.value = sel.value;
	chk.checked=true;
	lista.appendChild(chk);
	var label = document.createElement("label");
	label.setAttribute("for",id);
	label.innerHTML = sel.options[sel.selectedIndex].text;
	lista.appendChild(label);
	lista.appendChild(document.createElement("br"));
}
 
function mostraZone(nome, sel, el, val, liv) {
	var span = document.createElement("span");
	span.className = "selzone";
	var nomesp = document.createElement("span");
	nomesp.innerHTML = nome;
	span.appendChild(nomesp);
	span.appendChild(sel);
	var but = document.createElement("input");
	but.type = "button";
	but.setAttribute("onclick","addZona('"+val+"')");
	but.value='<?php echo str_replace("<LEVEL>", "'+nome+'", Lingua::getParola("aggiungi_zona")) ?>';
	span.appendChild(but);
	span.appendChild(document.createElement("br"));
	el.parentNode.insertBefore(span,el.nextSibling);
	return span;
}

function cambiaTipo() {
	var sel = document.getElementById("tipo");
	var zone="none";
	var soc="none";
	switch(sel.value){
	case "<?php echo Utente::SOCIETA; ?>":
		soc="table-row";
		loadSoc();
		break;
	case "<?php echo Utente::ORGANIZZATORE; ?>":
	case "<?php echo Utente::RESPONSABILE; ?>":
	case "<?php echo Utente::VISUALIZZA; ?>":
		zone="table-row";
		break;
	}
	document.getElementById("tr_zone").style.display=zone;
	document.getElementById("tr_soc").style.display=soc;
}

function loadSoc(){
	if (socload) return;
	socload=true;
	ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/societa.php", null, writeSoc);
}

function writeSoc(json,args) {
	var numcol=<?php echo self::SOC_COLS; ?>;
	var res = JSON.parse(json);
	var tab = document.getElementById("soctable");
	var i=0;
	var tr=null;
	for (s in res) {
		if (i%numcol == 0) tr = document.createElement("tr");
		td = document.createElement("td");
		td.style.width = (100/numcol)+"%";
		radio = document.createElement("input");
		radio.type = "radio";
		radio.name = "soc";
		radio.id = "soc_"+res[s].id;
		radio.value = res[s].id;
		td.appendChild(radio);
		label = document.createElement("label");
		label.setAttribute("for",radio.id);
		label.innerHTML = res[s].nome;
		td.appendChild(label);
		tr.appendChild(td);
		i++;
		if(i%numcol == 0) tab.appendChild(tr);
	}
	if (i%numcol != 0) {
		for(i = i%numcol; i<numcol; i++) 
			tr.insertCell(-1);
		tab.appendChild(tr);
	}
	var wait = document.getElementById("socwait");
	wait.parentNode.removeChild(wait);
}
</script>
<?php 
	} //function stampaJavascript

	public function stampaStile() { 
		echo "<style>\n";
		if (!$this->ctrl->isTipoZonaSelezionato())
			echo "#tr_zone { display:none; }\n";
		if (!$this->ctrl->isSocietaSelezionata())
			echo "#tr_soc { display:none; }\n";
		echo "</style>";
	}
	
	public function stampaInizioForm() {
		echo '<form accept-charset="UTF-8" action="" method="post" enctype="multipart/form-data" id="form_utente" onsubmit="return checkCampi()">';
		echo '<input type="hidden" name="pageid" value="'.md5(time()).'">';
	}

	public function stampaPulsante($testo) {
		echo '<input type="submit" value="'.$testo.'" id="inputGestGara"/>';
	}
	
	public function stampaApertura($nome, $id) {
		echo "\n<tr id=\"tr_$id\">";
		echo '<th width="40%"><div class="thAtleti thAtletiDx">'.$nome;
		echo ':</div></th><td>';
	}
	
	public function stampaChiusura() {
		echo '</td></tr>';
	}
	
	private function stampaErrore($id, $testo) {
		echo "<span class=\"err\" id=\"$id\" ";
		if (!$this->err->isErrato($id))
			echo 'style="display:none;" ';
		echo ">$testo</span>";
	}
	
	private function stampaTextbox($campo, $valore, $required=true, $pattern=NULL, $tipo="text") {
		echo "<input type=\"$tipo\" name=\"$campo\" id=\"$campo\" value=\"$valore\" class=\"inputGestGara";
		if ($this->err->isErrato($campo)) echo ' err';
		if (!is_null($pattern)) echo "\" pattern=\"$pattern";
		if ($required) echo '" required="required';
		echo '" size="50" />';
	}
	
	public function stampaUsername() {
		$this->stampaApertura(Lingua::getParola("username"),"username");
		$this->stampaTextbox("username", $this->ctrl->getUsername());
		$this->stampaErrore(VerificaUtente::USER_EXIST, Lingua::getParola("username_gia_esiste"));
		$this->stampaChiusura();
	}
	
	public function stampaUsernameFisso() {
		$this->stampaApertura(Lingua::getParola("username"),"username");
		echo $this->ctrl->getUsername();
		$this->stampaChiusura();
	}
	
	public function stampaPassword($required=true) {
		$this->stampaApertura(Lingua::getParola("password"),"psw");
		$this->stampaTextbox("psw", "", $required, NULL, "password");
		$this->stampaErrore(VerificaUtente::PSW_DIFF, Lingua::getParola("password_diverse"));
		$this->stampaChiusura();
	}
	
	public function stampaConfPassword($required=true) {
		$this->stampaApertura(Lingua::getParola("conf_password"),"psw2");
		$this->stampaTextbox("psw2", "", $required, NULL, "password");
		$this->stampaChiusura();
	}
	
	public function stampaNome() {
		$this->stampaApertura(Lingua::getParola("contatto"),"nome");
		$this->stampaTextbox("nome", $this->ctrl->getNome());
		$this->stampaChiusura();
	}
	
	public function stampaEmail() {
		$this->stampaApertura(Lingua::getParola("email"),"email");
		$this->stampaTextbox("email", $this->ctrl->getEmail(), true, GestioneUtente::EMAIL_REGEX);
		$this->stampaChiusura();
	}
	
	public function stampaSocietaNascosta() {
		$this->stampaTextbox("tipo", Utente::SOCIETA, false, NULL, "hidden");
		$this->stampaTextbox("soc", $_GET["idsoc"], false, NULL, "hidden");
	}
	
	public function stampaTipo() {
		$this->stampaApertura(Lingua::getParola("tipo_utente"),"tipo");
		echo '<select name="tipo" id="tipo" required="required" ';
		if ($this->err->isErrato("tipo")) echo 'class="err" ';
		echo 'onchange="cambiaTipo()"><option value=""></option>';
		foreach ($this->ctrl->getTipiUtente() as $idtipo) {
			$nome = Lingua::getParola("#tipoutente_$idtipo");
			echo "<option value=\"$idtipo\"";
			if ($idtipo == $this->ctrl->getTipo()) echo ' selected="selected"';
			echo ">$nome</option>\n";
		}
		echo '</select>';
		$this->stampaChiusura();
	}

	public function stampaTipoFisso() {
		$this->stampaApertura(Lingua::getParola("tipo_utente"),"tipo");
		echo Lingua::getParola("#tipoutente_".$this->ctrl->getTipo());;
		$this->stampaChiusura();
	}
	
	public function stampaRigaCheck($nome, $valore, $testo, $checked) {
		echo '<div style="position:relative;width:90%;height:30px;display:block;left:50px;">';
		echo '<div class="inputGestGara" style="width:40px;height:100%;text-align:center;float:left;line-height:40px">';
		echo "<input type=\"checkbox\" name=\"$nome\" value=\"$valore\" ";
		if ($checked) echo 'checked="checked" ';
		echo 'style="position:relative;top:5px">';
		echo '</div><div class="inputGestGara" style="margin-left:5px;height:100%;float:left;width:80%;line-height:30px">';
		echo $testo;
		echo '</div></div><br>';
	}
	
	public function stampaSocieta() {
		echo '<tr id="tr_soc"><td colspan="2">';
		$this->stampaErrore("soc", Lingua::getParola("utente_societa_non_selezionata"));
		echo '<fieldset class="inputGestGara">';
		echo "\n<legend>".Lingua::getParola("societa").":</legend>\n";
		if (!$this->ctrl->isSocietaSelezionata())
			echo '<div align="center"><img id="socwait" src="'._PATH_ROOT_.'img/wait.gif"></div>';
		echo '<table id="soctable" width="100%">';
		if ($this->ctrl->isSocietaSelezionata()) {
			include_model("Societa");
			$idsoc = $this->ctrl->getSocieta();
			$soc = Societa::lista(NULL, "nome");
			$width = 100/self::SOC_COLS;
			$i=0;
			foreach ($soc as $s) {
				/* @var $s Societa */
				$ids = $s->getChiave();
				$nome = $s->getNome();
				if ($i%self::SOC_COLS == 0) echo '<tr>';
				echo "<td style=\"width: $width%;\">";
				echo '<input type="radio" name="soc"';
				if ($ids == $idsoc) echo ' checked="checked"';
				echo " value=\"$ids\" id=\"soc_$ids\">";
				echo "<label for=\"soc_$ids\">$nome</label></td>\n";
				$i++;
				if ($i%self::SOC_COLS == 0) echo '</tr>';
			}
			if ($i%self::SOC_COLS != 0) {
				for($i = $i%self::SOC_COLS; $i < self::SOC_COLS; $i++) 
					echo '<td></td>';
				echo '</tr>';
			}
		}
		echo '</table></fieldset></td></tr>';
	}
	
	public function stampaZone() {
		$nometop = $this->zonesel->getNomeLivelloTop();
		echo '<tr id="tr_zone"><td colspan="2">';
		$this->stampaErrore("zona", Lingua::getParola("utente_zona_non_selezionata"));
		echo '<fieldset class="inputGestGara">';
		echo "\n<legend>".Lingua::getParola("zone_utente").":</legend>\n";
		echo "<span class=\"selzone\">$nometop: ";
		$this->zonesel->stampaSelectTop();
		$but = str_replace("<LEVEL>", $nometop, Lingua::getParola("aggiungi_zona"));
		echo "<input type=\"button\" value=\"$but\" onclick=\"addZona('top');\"><br></span>";
		echo '<div id="listazone">';
		$zone = $this->ctrl->getZone();
		foreach ($zone as $z) {
			/* @var $z Zona */
			$idz = $z->getChiave();
			$n = $z->getNome();
			echo "<input value=\"$idz\" id=\"zona_$idz\" name=\"zona[$idz]\" checked=\"checked\" type=\"checkbox\">";
			echo "<label for=\"zona_$idz\">$n</label><br>";
		}
		echo '</div></fieldset></td></tr>';
	}

}