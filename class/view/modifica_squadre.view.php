<?php
if (!defined("_BASEDIR_")) exit();
include_class("Sesso");

class ModificaSquadreView {
	/**
	 * @var ModificaSquadre
	 */
	private $ctrl;
	
	private $FQ;
	
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
		
		$grcat = Categoria::listaGruppiGara($this->ctrl->getGara()->getChiave());
		if(in_array(38,$grcat))//TODO controllare se serve gestione del fuoriquota per altri gruppi di categoria
			$this->FQ = true;
		else 
			$this->FQ = false;
		
// 		var_dump($FQ);
	}
	
	public function stampaTipi() {
		if ($this->ctrl->isNuova()) {
			$sel = $this->ctrl->getTipo();
			foreach ($this->ctrl->getTipiGara() as $id=>$tipo) {
				echo '<input type="radio" name="tipo" class="styled" ';
				if (!is_null($sel) && $id == $sel) echo 'checked="checked" ';
				echo "value=\"$id\" onchange=\"javascript:setTipo('".strtolower($tipo)."',$id);\" ";
				echo "id=\"tipo_$id\" />$tipo";
			}
		} else {
			$tipi = $this->ctrl->getTipiGara();
			echo $tipi[$this->ctrl->getTipo()];
		}
	}
	
	public function iniziaTabella($id, $style="", $soc=false) {
?>
<table class="atleti" id="<?php echo $id; ?>" style="<?php echo $style ?>" >
<thead>
<tr  class="tr">

<th class='thComandoSquadra'></th>
<th><div class='thAtleti'><?php echo Lingua::getParola("cognome_iscrizioni"); ?></div></th>
<th><div class='thAtleti'><?php echo Lingua::getParola("nome_iscrizioni"); ?></div></th>
<th><div class="thAtleti" ><?php echo Lingua::getParola("sesso_iscrizioni"); ?></div></th>
<th><div class='thAtleti'><?php echo Lingua::getParola("nascita_iscrizioni"); ?></div></th>
<th><div class='thAtleti'><?php echo Lingua::getParola("cintura_iscrizioni"); ?></div></th>
<?php if ($soc) {?>
<th><div class='thAtleti'><?php echo Lingua::getParola("societa"); ?></div></th>
<?php } //if $soc?>
</tr>
</thead>
<?php 
	} //iniziaTabella
	
	
	private function inizioRigaAtleta($comp, $tipi, $id, $a, $tipino=NULL) {
		$pres = false;
		if ($comp) {
			$pres = $this->ctrl->isPrestito($id);
			if ($pres)
				$pre = "pres";
			else
				$pre = "comp";
		}else 
			$pre = "atl";
		echo '<tr ';
		$class = "riga1 ";
		/* @var $a Atleta */
		if($this->FQ) //solo per gruppo categorie 38
		{
		if($a->getEta($this->ctrl->getGara()->getDataGara()) < 18)
			$class .= "FQ";
		}
		foreach ($tipi as $idt => $nome) {
			//se quesato tipo è un tipo no
			//oppure se non può partecipare a questo tipo di gara
			if (($tipino !== NULL && isset($tipino[$idt]))
					||(!$pres && !$this->ctrl->tipoGaraOk($a, $idt)))
			{
				$class .= " no".strtolower($nome);
			}
		}
		if ($id !== NULL) {
			if (!$comp && $this->ctrl->isComponente($id))
				echo "style=\"display:none\" ";
			echo "id=\"{$pre}_$id\" ";
		}
		echo "class=\"$class\">";
	}
	
	/**
	 * @param Atleta $a
	 * @param string $cintura
	 */
	private function rigaAtleta($a, $cintura, $prestito=false) {
		if ($a->isVerificato()) {
			echo '<td class="cognome">';
			if ($prestito) //TODO lingua
				echo '<img title="Prestito" style="margin-right: 5px;" src="'._PATH_ROOT_.'img/prestito.gif">';
			echo $a->getCognome().'</td>';
			echo '<td class="nome">'.$a->getNome().'</td>';
		} else {
			echo '<td class="cognome"><span class="nonverificato">'.$a->getCognome().'</span></td>';
			echo '<td class="nome"><span class="nonverificato">'.$a->getNome().'</span></td>';
		}
		$urlcin = $a->getUrlCintura();
		if (!$prestito && !is_null($urlcin)) {
			$id = $a->getChiave();
			$nomecin = $cintura;
			$cintura = "<a href=\"$urlcin\" target=\"cambio_cintura\" ";
			$cintura .= "onclick=\"cambioCintura($id,-1)\" id=\"cintura_$id\">";
			$cintura .= $nomecin.'</a>';
		}
		echo '<td class="sesso">'.Sesso::toStringBreve($a->getSesso()).'</td>';
		echo '<td class="nascita">'.$a->getDataNascita()->format('d/m/Y').'</td>';
		echo "<td class=\"cintura\">$cintura</td>";
	}
	
	public function stampaComponenti() {
		$this->iniziaTabella("tab_comp");
		$tipi = $this->ctrl->getTipiGara();
		foreach ($this->ctrl->getComponenti() as $id => $a) {
			/* @var $a Atleta */
			//echo "<tr id=\"comp_$id\" class=\"riga1\">";
			$this->inizioRigaAtleta(true, $tipi, $id, $a);
			$prestito = $this->ctrl->isPrestito($id);
			if ($prestito) {
				$js = "modificaPrestito";
				$campo = "pres";
			} else {
				$js = "modificaComponente";
				$campo = "comp";
			}
			echo "<td class=\"rimuovi\" onclick=\"$js($id,this)\">";
			if ($prestito) {
				$idps = $this->ctrl->getSocietaPrestito($id);
				echo "<input type=\"hidden\" name=\"pres_soc[$id]\" value=\"$idps\" />";
			}
			echo "<input type=\"hidden\" name=\"{$campo}[$id]\" value=\"$id\" /></td>";
			$this->rigaAtleta($a, $this->ctrl->getCinturaComponente($a), $prestito);
			echo "</tr>\n";
		}
		echo "</table>";
	}
	
	public function stampaAtleti() {
		$this->iniziaTabella("tab_atl");
		$tipi = $this->ctrl->getTipiGara();
		foreach ($this->ctrl->getAtletiOk() as $id => $a) {
			/* @var $a Atleta */
			$usciti = $this->ctrl->tipiUsciti($id);
			if ($usciti === NULL) {
				//nessun prestito
				$this->rigaAtletaCompleta($tipi, $id, $a, false);
			} elseif(count($usciti) == count($tipi)) {
				//tutti prestati
				$this->rigaAtletaCompleta($tipi, $id, $a, true);
			} else {
				//qualche prestito
				$tipiok = array();
				foreach ($tipi as $t=>$n) {
					if (!isset($usciti[$t]))
						$tipiok[$t] = $t;
				}
				$this->rigaAtletaCompleta($tipi, $id, $a, false, $usciti);
				$this->rigaAtletaCompleta($tipi, NULL, $a, true, $tipiok);
			}
			
// 			if ($a->isVerificato()) {
// 				if ($this->ctrl->isUscito($id))
// 					$td = '<td class="bloccato" onclick="uscito()">&nbsp;</td>';
// 				else
// 					$td = '<td class="aggiungi" onclick="modificaComponente('.$id.',this)">&nbsp;</td>';
// 			} else {
// 				$url = $a->getUrlDettagli();
// 				$td = '<td style="text-align:center"><img src="'._PATH_ROOT_.'img/alert.png" '
// 						."onclick=\"nonverificato('$url')\" style=\"cursor:pointer\"></td>";
// 			}
			
// 			$this->inizioRigaAtleta(false, $tipi, $id, $a);
// 			echo $td;
// 			$this->rigaAtleta($a, $this->ctrl->getNomeCintura($a->getCintura()));
// 			echo "</tr>\n";
		}
		echo "</table>";
	}
	
	private function rigaAtletaCompleta($tipi, $id, $a, $uscito, $tipino=NULL) {
		$this->inizioRigaAtleta(false, $tipi, $id, $a, $tipino);
		if ($a->isVerificato()) {
			if ($uscito)
				echo '<td class="bloccato" onclick="uscito()">&nbsp;</td>';
			else
				echo '<td class="aggiungi" onclick="modificaComponente('.$id.',this);fuoriquota('.$id.',this)">&nbsp;</td>';
		} else {
			$url = $a->getUrlDettagli();
			echo '<td style="text-align:center"><img src="'._PATH_ROOT_.'img/alert.png" '
					."onclick=\"nonverificato('$url')\" style=\"cursor:pointer\"></td>";
		}
		$this->rigaAtleta($a, $this->ctrl->getNomeCintura($a->getCintura()));
		echo "</tr>\n";
	}
	
	public function stampaListaCategorie() {
		$cat = $this->ctrl->getMultiCategorie();
		echo '<p id="categorie">';
		echo "<font color=\"red\"><h2>".Lingua::getParola("cat_team_sel")."</h2></font>";
		echo Lingua::getParola("categoria");
		echo '<select name="categoria">';
		foreach ($cat as $c) {
			/* @var $c Categoria */
			echo '<option value="'.$c->getChiave().'">'.$c->getNome()."</option>\n";
		}
		echo '</select></p>';
	}
}
?>