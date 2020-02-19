<?php
if (!defined("_BASEDIR_")) exit();
include_view("SelectZone");

class GestioneEsternaView {
	/**
	 * @var GestioneEsterna
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
		$this->zonesel = new SelectZone(true,false);
		$this->zonesel->setZona($this->ctrl->getZona(),2);
	}
	
	public function stampaJavascript() {
		$this->zonesel->stampaJavascript();
?>
<script type="text/javascript">
function mostraZone(nome, sel, el, val, liv) {
	sel.name = "zona["+liv+"]";
	var tr = document.createElement("tr");
	var th = document.createElement("th");
	var div = document.createElement("div");
	div.className = "thAtleti thAtletiDx";
	div.innerHTML = nome+":";
	th.appendChild(div);
	tr.appendChild(th);
	var td = document.createElement("td");
	td.appendChild(sel);
	tr.appendChild(td);

	el.parentNode.parentNode.insertBefore(tr,el.parentNode.nextSibling)
	return tr;
}
</script>
<?php 
	} //function stampaJavascript
	
	public function stampaInizioForm() {
		echo '<form accept-charset="UTF-8" action="" method="post" enctype="multipart/form-data" id="form_affiliata" onsubmit="return checkCampi()">';
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
		echo "<span id=\"$id\" ";
		if (!$this->err->isErrato($id))
			echo 'style="display:none;" ';
		echo ">$testo</span>";
	}
	
	private function stampaTextbox($campo, $valore, $required=true, $maxlen=NULL, $tipo="text") {
		echo "<input type=\"$tipo\" name=\"$campo\" id=\"$campo\" value=\"$valore\" class=\"inputGestGara";
		if ($this->err->isErrato($campo)) echo ' err';
		if (!is_null($maxlen)) echo "\" maxlength=\"$maxlen";
		if ($required) echo '" required="required';
		echo '" size="50" />';
	}
	
	public function stampaNome() {
		$this->stampaApertura(Lingua::getParola("societa"),"nome");
		$this->stampaTextbox("nome", $this->ctrl->getNome());
		$this->stampaChiusura();
	}
	
	public function stampaNomeBreve() {
		$this->stampaApertura(Lingua::getParola("abbrevia"),"nomebreve");
 		$this->stampaTextbox("nomebreve", $this->ctrl->getNomeBreve(), true, GestioneEsterna::LEN_BREVE);
		$this->stampaChiusura();
	}
	
	public function stampaFedEst() {
		$this->stampaApertura(Lingua::getParola("fed_est"),"fed_est");
		$this->stampaTextbox("fed_est", $this->ctrl->getFedEst());
		$this->stampaChiusura();
	}
	
	public function stampaStile() {
		$this->stampaApertura(Lingua::getParola("stile_societa"),"stile");
		echo '<select name="stile" id="stile" required="required" ';
		if ($this->err->isErrato("tipo")) echo 'class="err" ';
		echo '><option value=""></option>';
		foreach ($this->ctrl->getListaStili() as $stile) {
			/* @var $stile Stile */
			$id = $stile->getChiave();
			$nome = $stile->getNome();
			echo "<option value=\"$id\"";
			if ($id == $this->ctrl->getStile()) echo ' selected="selected"';
			echo ">$nome</option>\n";
		}
		echo '</select>';
		$this->stampaChiusura();
	}
	
	public function stampaUtente() {
		$this->stampaApertura(Lingua::getParola("utente_auto"), "user");
		echo '<input type="checkbox" name="user" id="user" value="1"';
		if ($this->ctrl->getUtenteAuto()) echo ' checked="checked"';
		echo ' class="styled" />';
		$this->stampaChiusura();
	}
	
	public function stampaZone() {
		$this->stampaApertura($this->zonesel->getNomeLivelloTop(), "zona_top");
		$this->zonesel->stampaSelectTop("zona[0]");
		$this->stampaChiusura();
		$sub = $this->zonesel->getNumSubzone();
		for($i = 1; $i <= $sub; $i++) {
			$this->stampaApertura($this->zonesel->getNomeLivelloSub($i), "zona_$i");
			$this->zonesel->stampaSelectSub($i, "zona[$i]");
			$this->stampaChiusura();
		}
	}

}