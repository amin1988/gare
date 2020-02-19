<?php

if (!defined("_BASEDIR_")) exit();

include_class("Sesso");



class IscriviView {

	/**

	 * @var Iscrivi

	 */

	private $ctrl;

	

	public function __construct($ctrl) {

		$this->ctrl = $ctrl;

	}

	

	public function usaCalendario() {

		return $this->ctrl->nuoviCampi() > 0;

	}
        
        
        public function StampaRigaStageAtleta ($ff, $pari=true, $gara, $id_atleta) {
            
                $ff->stampaPrima($pari);

		$ff->stampaTdApri("cognome");

		$ff->stampaCognome();

		$ff->chiudiApri("nome");

		$ff->stampaNome();

		$ff->chiudiApri("sesso");

		$ff->stampaSesso();

		$ff->chiudiApri("nascita");

		$ff->stampaNascita();
		
		$ff->chiudiApri("tipo");

		 //TODO fare meglio
                $tipo_gara = $gara->getTipoGara();
                 $tipo_gara = 'stage_nazionale';
                $stage = new Stage(null, 'partecipanti_stage');
                $checked = "";
                $atleta_selezionato = $stage->searchPartecipanteStage($gara->getIDGara(), $id_atleta);
                if ( $atleta_selezionato)
                {
                    $checked = " checked ";
                }
                //$tipo_gara = "newcheck";
                print '<input type="checkbox" '.$checked.' name="'.$tipo_gara.'[]" value="'.$id_atleta.'">';
		//$ff->stampaTipo("stage_nazionale", 1);

		$ff->stampaTdChiudi();

		$ff->stampaDopo();

		if ($ff->isErrato()) $ff->stampaErrore();
        }
        


	/**

	 * @param FieldFiller $ff

	 * @param boolean $pari se la riga � pari

	 */

	public function stampaRigaAtleta($ff, $pari=true) {

		$ff->stampaPrima($pari);

		if ($this->ctrl->haNonVerificati()) {

			$ff->stampaTdApri("nonverificato");

			$ff->stampaNonVerificato();

			$ff->stampaTdChiudi();

		}

		$ff->stampaTdApri("cognome");

		$ff->stampaCognome();

		$ff->chiudiApri("nome");

		$ff->stampaNome();

		$ff->chiudiApri("sesso");

		$ff->stampaSesso();

		$ff->chiudiApri("nascita");

		$ff->stampaNascita();

		$ff->chiudiApri("cinture");

		$ff->stampaCinture();

		$ff->chiudiApri("tipo");

		 //TODO fare meglio

		$ff->stampaTipo("Kata", 0);

		$ff->chiudiApri("tipo");
                
                
                $ff->stampaTipo("Kata Rengokai", 3);

		$ff->chiudiApri("tipo");
                

		$ff->stampaTipo("Shobu Sanbon", 1);

		$ff->chiudiApri("tipo");
                
                
                $ff->stampaTipo("Shobu Kumite", 4);

		$ff->chiudiApri("tipo");
                

		$ff->stampaTipo("Ippon",2);

		$ff->chiudiApri("handicap");

		$ff->stampaHandicap();

		$ff->chiudiApri("stile");

		$ff->stampaStili();

		$ff->chiudiApri("peso");

		$ff->stampaPeso();

		$ff->stampaTdChiudi();

		$ff->stampaDopo();

		if ($ff->isErrato()) $ff->stampaErrore();

	}

	

	private function getValori($a, $campobase, &$id, &$campo, &$new) {

		$new = is_numeric($a);

		if ($new)

			$id = $a;

		else

			$id = $a->getChiave();

		$campo = $this->ctrl->nomeCampo($campobase, $new);

	}



}



abstract class FieldFiller {

	

	/**

	 * @param array $valori array di valori con metodo getNome()

	 * @param int $selid id da selezionare

	 */

	protected function getElencoCode($valori, $selid) {

		$r = "";

		foreach ($valori as $id => $v) {

			$r .= "<option value=\"$id\"";

			if ($selid == $id) $r .= ' selected="selected"';

			$r .= ">".$v->getNome()."</option>";

		}

		return $r;

	}

	

	/**

	 * @param int $ida id atleta

	 * @param int $pre prefisso del nome del select

	 * @param string $prima in uscita contiene il valore da stampare prima del contenuto

	 * @param string $dopo in uscita contiene il valore da stampare dopo il contenuto 

	 */

	protected function getCintureSelect($ida, $pre, &$prima, &$dopo) {

		if ($this->isErrato("sesso"))

			$prima = '<select class="err"';

		else

			$prima = '<select';

		$prima .= " name=\"{$pre}cintura[$ida]\">";

		$dopo = '</select>';

	}

	

	/**

	 * @param int $ida id atleta

	 * @param int $pre prefisso del nome del select

	 * @param string $prima in uscita contiene il valore da stampare prima del contenuto

	 * @param string $dopo in uscita contiene il valore da stampare dopo il contenuto 

	 */

	protected function getStiliSelect($ida, $pre, &$prima, &$dopo) {

		$prima = "<select name=\"{$pre}stile[$ida]\">";

		$dopo = '</select>';

	}

		

	/**

	 * @param string $nome nome del tipo

	 * @param $value valore del check

	 * @param string $pre prefisso del nome del check

	 * @param int $ida id atleta

	 * @param boolean $disabled true per disabilitare

	 * @param boolean $check true per selezionare

	 */

	protected function getTipoCode($nome, $value, $pre, $ida, $disabled, $check) {

		$campo = "{$pre}tipo";

		$r = '';

		if ($this->isErrato($campo))

			$r .= '<span class="err">';

		$r .= '<input type="checkbox" class="styled"'.

			" name=\"{$campo}[$ida][$value]\" value=\"$value\"";

// 		$r .= ' onclick="checkKumite();" ';

		if ($disabled)

			$r .= ' disabled="disabled"';

		else if ($check)

			$r .= ' checked="checked"';

		$r .= " alt=\"$nome\" />";

		if ($this->isErrato($campo))

			$r .= '</span>';

		return $r;

	}





	/**

	 * @param string $pre prefisso del nome del check

	 * @param int $ida id atleta

	 * @param boolean $disabled true per disabilitare

	 * @param boolean $check true per selezionare

	 */

	protected function getHandicapCode($pre, $ida, $disabled, $check) {

		$campo = "{$pre}hp";

		$r = '';

		$r .= '<input type="checkbox" class="styled"'.

				" name=\"{$campo}[$ida]\" value=\"1\"";

		if ($disabled)

			$r .= ' disabled="disabled"';

		else if ($check)

			$r .= ' checked="checked"';

		$r .= " alt=\"Handicap\" />";

		return $r;

	}

	

	/**

	 * @param string $nome nome del tipo

	 */

	protected function getTipoCodeDisabled($nome) {

		$r = '<input type="checkbox" class="styled" disabled="disabled" ';

		$r .= " alt=\"$nome\" />";

		return $r;

	}

	

	/**

	 * @param $ida

	 * @param string $nome nome del campo

	 * @param string $valore valore del campo

	 */

	protected function getTextboxCode($ida, $nome, $valore, $placeholder, $param="") {

        $r = "<input type=\"text\" placeholder=\"$placeholder\" $param ";

		if ($this->isErrato($nome)) $r .= ' class="err"';

		$r .= " name=\"{$nome}[$ida]\" value=\"$valore\"/>";

		return $r;

	}

	

// 	protected function getNominativoCode($ida, $cognome, $nome) {

// 		$r = $this->getTextboxCode($ida, "cognome", $cognome, Lingua::getParola("iscrivi_cognome"));

// 		return $r . $this->getTextboxCode($ida, "nome", $nome, Lingua::getParola("iscrivi_nome"));

// 	}

	

	protected function getSessoCode($id, $valore) {

		$selm = ""; $self="";

		$r = "";

		switch ($valore) {

			case Sesso::M:

				$selm = ' selected="selected"';

				break;

			case Sesso::F:

				$self = ' selected="selected"';

				break;

		}

		$mstr = Sesso::toStringBreve(Sesso::M);

		$fstr = Sesso::toStringBreve(Sesso::F);

		if ($this->isErrato("sesso"))

			$r = '<select class="err"';

		else

			$r = '<select';

		$r .= " name=\"sesso[$id]\"><option value=\"0\"></option>";

		$r .= "<option value=\"1\"$selm>$mstr</option><option value=\"2\"$self>$fstr</option></select>";

		return $r;

	}

	

	protected function getNascitaCode($ida, $valore) {

		$f = Lingua::getParola("formato_data");

		return $this->getTextboxCode($ida, "nascita", $valore, $f,

				'pattern="\d{1,2}/\d{1,2}/\d{4}" title="'.$f.'" id="nascita_'.$ida.'"');

	}

	

	protected function getPesoCode($ida, $pre, $valore) {

		return $this->getTextboxCode($ida, "{$pre}peso", $valore, "", 'maxlength="3" pattern="\d{1,3}"');

	}

	

	public abstract function isErrato($campo=NULL);

	

	public abstract function stampaPrima($pari=true);

	

	public abstract function stampaDopo();

	

	public abstract function stampaTdApri($class);

	

	public abstract function stampaTdChiudi();

	

	public function chiudiApri($class) {

		$this->stampaTdChiudi();

		$this->stampaTdApri($class);

	}

	

	public function stampaNonVerificato() {}

	

	public abstract function stampaCognome();

		

	public abstract function stampaNome();

		

	public abstract function stampaSesso();

		

	public abstract function stampaNascita();

	

	public abstract function stampaCinture();

	

	public abstract function stampaTipo($nome, $idtipo);

	

	public abstract function stampaHandicap();

	

	public abstract function stampaStili();

	

	public abstract function stampaPeso();

	

	public function stampaErrore() {}

}



abstract class RealFieldFiller extends FieldFiller {

	protected $a;

	/**

	 * @var int

	 */

	protected $ida;

	

	/**

	 * @var Iscrivi

	 */

	protected $ctrl;

	

	/**

	 * @var VerificaIscritti

	 */

	protected $err;

	

	/**

	 * @var string prefisso dei campi

	 */

	protected $pre;

	

	/**

	 * @var boolean

	 */

	private $cinturaVuota;

	

	/**

	 * @param Iscrivi $ctrl

	 * @param string $pre

	 * @param boolean $cinturaVuota

	 */

	public function  __construct($ctrl, $pre, $cinturaVuota) {

		$this->ctrl = $ctrl;

		$this->err = $ctrl->getErrori();

		$this->pre = $pre;

		$this->cinturaVuota = $cinturaVuota;

	}

	

	public function stampaPrima($pari=true) {

		if ($this->isErrato())

			echo '<tr class="err">';

		else if ($pari)

			echo '<tr class="riga1">';

		else

			echo '<tr class="riga2">';

	}

	

	public function stampaDopo() {

		echo "</tr>\n";

	}

	

	public function stampaTdApri($class) {

		echo "<td class=\"$class\">";

	}

	

	public function stampaTdChiudi(){

		echo "</td>";

	}

	

	public function stampaCinture() {

		$this->getCintureSelect($this->ida, $this->pre, $prima, $dopo);

		echo $prima;

		if ($this->cinturaVuota)

			echo '<option value="-1"></option>';

		echo $this->getElencoCode($this->ctrl->getCinture(),

				$this->ctrl->cinturaIscritto($this->a));

		echo $dopo;

	}

	

	public function stampaStili() {

		$this->getStiliSelect($this->ida, $this->pre, $prima, $dopo);

		echo $prima;

		echo $this->getElencoCode($this->ctrl->getStili(),

				$this->ctrl->stileIscritto($this->a));

		echo $dopo;

	}

	

	public function stampaPeso() {

		echo $this->getPesoCode($this->ida, $this->pre, $this->ctrl->pesoIscritto($this->a));

	}

}



class DbFieldFiller extends RealFieldFiller {



	public function __construct($ctrl) {

		parent::__construct($ctrl, "", false);

	}

	

	/**

	 * @param Atleta $a

	 */

	public function setAtleta($a) {

		$this->a = $a;

		$this->ida = $a->getChiave();

	}



	public function isErrato($campo=NULL) {

		return $this->err->isErrato($this->ida, $campo, false);

	}

	

	private function stampaParteNome($testo) {

		if ($this->a->isVerificato())

			echo $testo;

		else

			echo "<span class=\"nonverificato\">$testo</span>";

	}

	

	public function stampaNonVerificato() {

		if ($this->a->isVerificato()) return;

		$url = $this->a->getUrlDettagli();

		echo '<img src="'._PATH_ROOT_.'img/alert.png" ';

		echo "onclick=\"nonverificato('$url')\" style=\"cursor:pointer;margin-left:5px;\">";

	}

	

	public function stampaCognome() {

		$this->stampaParteNome($this->a->getCognome());

	}

	

	public function stampaNome() {

		$this->stampaParteNome($this->a->getNome());

	}

	

	public function stampaSesso(){

		echo Sesso::toStringBreve($this->a->getSesso());

	}



	public function stampaNascita() {

		echo $this->a->getDataNascita()->format("d/m/Y");

	}

	

	public function stampaCinture() {

		if (!$this->ctrl->cintureFisse())

			parent::stampaCinture();

		else if (is_null($this->a->getUrlCintura()))

			echo $this->ctrl->getNomeCintura($this->a->getCintura());

		else {

			echo '<a href="'.$this->a->getUrlCintura().'" target="cambio_cintura" ';

			$id = $this->a->getChiave();

			echo "onclick=\"cambioCintura($id,-1)\" id=\"cintura_$id\">";

			echo $this->ctrl->getNomeCintura($this->a->getCintura());

			echo '</a>';

		}

	}

	

	public function stampaTipo($nome, $idtipo) {

		if ($this->a->isVerificato()) {

			echo $this->getTipoCode($nome, $idtipo, "", $this->ida,

					!$this->ctrl->tipoGaraOk($this->a, $idtipo),

					$this->ctrl->tipoIscritto($this->a, $idtipo));

		} else {

			echo $this->getTipoCodeDisabled($nome);

		}

	}



	public function stampaHandicap() {

		if ($this->a->isVerificato()) {

			echo $this->getHandicapCode("", $this->ida, false, $this->a->isHandicap());

		} else {

			echo $this->getHandicapCode("", 0, true, false);

		}

	}

	

	public function stampaErrore() {

		//TODO fare bene

		echo '<tr class="err"><td colspan="20">';

		echo $this->ctrl->getErrori()->toString($this->ida, false);

		echo "</td></tr>\n";

	}

	

}



class NuovoFieldFiller extends RealFieldFiller {

	

	public function __construct($ctrl) {

		parent::__construct($ctrl, "new", true);

	}

	

	public function setId($ida) {

		$this->a = $ida;

		$this->ida = $ida;

	}



	public function isErrato($campo=NULL) {

		return $this->err->isErrato($this->ida, $campo, true);

	}

	

	public function stampaCognome() {

		echo $this->getTextboxCode($this->ida, "cognome", 

				$this->ctrl->nuovoCampo("cognome", $this->ida), Lingua::getParola("cognome_iscrizioni"));

	}

	

	public function stampaNome() {

		echo $this->getTextboxCode($this->ida, "nome", 

				$this->ctrl->nuovoCampo("nome", $this->ida), Lingua::getParola("nome_iscrizioni"));

	}

	

	public function stampaSesso(){

		echo $this->getSessoCode($this->ida, $this->ctrl->nuovoCampo("sesso", $this->ida));

	}

	

	public function stampaNascita() {

		echo $this->getNascitaCode($this->ida, $this->ctrl->nuovoCampo("nascita", $this->ida));

	}



	public function stampaTipo($nome, $idtipo) {

		echo $this->getTipoCode($nome, $idtipo, "new", $this->ida,

				false, $this->ctrl->tipoIscritto($this->a, $idtipo));

	}



	public function stampaHandicap() {

		echo $this->getHandicapCode("new", $this->ida,

				false, false); //TODO verificare persistenza

	}

	

	public function stampaErrore() {

		//TODO fare bene

		echo '<tr><td colspan="20">';

		echo $this->ctrl->getErrori()->toString($this->ida, true);

		echo "</td></tr>\n";

	}

	

}



class JavascriptFieldFiller extends FieldFiller {

	/**

	 * @var Iscrivi

	 */

	protected $ctrl;

	

	const ID = "' + id + '";

	

	public function __construct($ctrl) {

		$this->ctrl = $ctrl;

	}

	

	public function isErrato($campo=NULL) {

		return false;

	}



	public function stampaPrima($pari=true) {

	}

	

	public function stampaDopo() {

	}

	

	public function stampaTdApri($class) {

		echo 'var td=document.createElement("td");';

		echo "\ntd.className=\"$class\";\n";

		//echo "<td class\"$class\">";

	}

	

	public function stampaTdChiudi(){

		//echo "</td>";

		echo "riga.appendChild(td);\n";

	}

	

	public function stampaCognome() {

		echo "td.innerHTML='";

		echo $this->getTextboxCode(self::ID, "cognome", "", Lingua::getParola("cognome_iscrizioni"));

		echo "';\n";

	}

	

	public function stampaNome() {

		echo "td.innerHTML='";

		echo $this->getTextboxCode(self::ID, "nome", "", Lingua::getParola("nome_iscrizioni"));

		echo "';\n";

	}

	

	public function stampaSesso() {

		echo "td.innerHTML='";

		echo $this->getSessoCode(self::ID, 0);

		echo "';\n";

	}

	

	public function stampaNascita() {

// 		echo "td.innerHTML='";

// 		echo $this->getNascitaCode(self::ID, "");

// 		echo "';\n";

		

		//<input type="text" placeholder="gg/mm/aaaa" pattern="\d{1,2}/\d{1,2}/\d{4}" title="gg/mm/aaaa" id="nascita_0"  name="nascita[0]" value=""/>

		echo 'var nasc=document.createElement("input");';

		echo 'nasc.type="text"; ';

		echo "nasc.name='nascita[".self::ID."]';\n";

		echo "nasc.id='nascita_".self::ID."';\n";

		echo 'setCalendar(nasc); ';

		$tit = Lingua::getParola("formato_data");

		echo 'nasc.placeholder="'.$tit.'"; ';

		echo 'nasc.title="'.$tit.'"; ';

		echo 'nasc.pattern="\\\\d{1,2}/\\\\d{1,2}/\\\\d{4}"; ';

		echo 'td.appendChild(nasc);'."\n";

	}

	

	public function stampaCinture() {

		echo "td.innerHTML='";

		$this->getCintureSelect(self::ID, "new", $prima, $dopo);

		echo $prima;

		echo '<option value="-1"></option>';

		echo $this->getElencoCode($this->ctrl->getCinture(), NULL);

		echo $dopo;

		echo "';\n";

	}

	

	public function stampaTipo($nome, $idtipo) {

// 		echo "td.innerHTML='";

// 		echo '<span class="checked" onchange="Custom.clear" onmousedown="Custom.pushed" onmouseup="Custom.check">';

// 		echo $this->getTipoCode($nome, $idtipo, "new", self::ID, false, false);

// 		echo "';\n";

		echo 'var s=document.createElement("span");'."\n";

		echo 's.className="checkbox";'."\n";

		echo "initSpan(s);\n";

		echo 'var c=document.createElement("input");'."\n";

		echo "c.type='checkbox';\nc.className='styled';\n";

		echo "c.name='newtipo[".self::ID."][$idtipo]';\nc.value='$idtipo';\n";

		echo 'td.appendChild(s);'."\n";

		echo 'td.appendChild(c);'."\n";

		

	}



	public function stampaHandicap() {

		echo 'var s=document.createElement("span");'."\n";

		echo 's.className="checkbox";'."\n";

		echo "initSpan(s);\n";

		echo 'var c=document.createElement("input");'."\n";

		echo "c.type='checkbox';\nc.className='styled';\n";

		echo "c.name='newhp[".self::ID."]';\nc.value='1';\n";

		echo 'td.appendChild(s);'."\n";

		echo 'td.appendChild(c);'."\n";

	

	}

	

	public function stampaStili() {

		echo "td.innerHTML='";

		$this->getStiliSelect(self::ID, "new", $prima, $dopo);

		echo $prima;

		echo $this->getElencoCode($this->ctrl->getStili(), $this->ctrl->getStileDefault());

		echo $dopo;

		echo "';\n";

	}

	

	public function stampaPeso() {

		echo "td.innerHTML='";

		echo str_replace('\\', '\\\\', $this->getPesoCode(self::ID, "new", ""));

		echo "';\n";

	}

}