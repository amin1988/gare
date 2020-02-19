<?php
if (!defined("_BASEDIR_")) exit();

class RiepilogoCompletoView {
	/**
	 * @var RiepilogoCompleto
	 */
	protected $ctrl;
	/**
	 * @var int id societa
	 */
	protected $idsoc;
	
	/**
	 * @param RiepilogoCompleto $ctrl
	 * @param int $idsoc
	 */
	public function __construct($ctrl, $idsoc = NULL) {
		$this->ctrl = $ctrl;
		$this->idsoc = $idsoc;
		$ctrl->setPropriPartecipanti($idsoc);
	}
	
	public function stampaJavascript() { ?>
<script type="text/javascript">
function mostraCatOriginale(nome) {
	showPopup("<?php echo Lingua::getParola("categoria_originale"); ?>:<br>"+nome);
}
</script>
<?php 
	}
	
	public function noPartecipanti() {
		echo '<h1>'.Lingua::getParola("no_partecipanti").'</h1>';
	}
	
	/**
	 * @param boolean $squadre true se bisogna mostrare le squadre
	 * @param boolean $resp true per mostrare i pulsanti da responsabile
	 */
	public function altroRiepilogo($squadre, $resp=false) {
		if (!$resp) return;
		if (!$this->ctrl->getGara()->iscrizioniChiuse()) return;
		if ($this->ctrl->getGara()->passata()) return;
		
		$idg = $this->ctrl->getGara()->getChiave();
		echo "<div class=\"pulsante tr nostampa\" style=\"text-align:center\">";
		$url = _PATH_ROOT_."resp/accorpa.php?id=$idg";
		$testo = Lingua::getParola("accorpa_titolo");
		echo "<a href=\"$url\" class='pulsante_noInput'>$testo</a>\n";
		echo "<br></div><br><br>";
	}
	
	public function tabellaCategorie() {
		$propri = !is_null($this->idsoc);
		echo '<h1>'.Lingua::getParola("riepilogo_categorie").'</h1>';
		?>
		<table class="atleti" >
		<tr  class="tr">
		<th><div class='thAtleti'></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("categoria"); ?></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_partecipanti_cat"); ?></div></th>
		<?php if ($propri) { ?>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_partecipanti_propri"); ?></div></th>
		<?php } //if idsoc != null ?>
		<th class="nostampa"><div class='thAtleti'></div></th>
		</tr>
		
		<?php 
		
		$count=0;
		$numcat = 1;
		$dettagli = Lingua::getParola("dettagli_categoria");
		foreach ($this->ctrl->getCategorie() as $c) {
			$id = $c->getChiave();
			$num = $this->ctrl->getNumPartecipanti($id);
			if (isset($num[1])) {
				ksort($num);
				foreach ($num as $pool => $pnum) {
					$this->stampaRigaCat($c, $id, $dettagli, $pnum, $pool, $propri, $count, $numcat);
					if ($pool > 0) $numcat++;
				}
				$count++;
			} else if ($this->stampaRigaCat($c, $id, $dettagli, $num[0], -1, $propri, $count, $numcat)) {
				$count++;
				$numcat++;
			}
		}
		
		echo '</table>';
	}
	
	private function stampaRigaCat($c, $id, $dettagli, $num, $pool, $propri, $count, $numcat) {
		if ($num == 0) return false;
		if (($count % 2) == 0) $classe = "riga1";
		else $classe = "riga2";
		
		$ancora = "cat$id";
		$nome = $c->getNome();
		if ($pool > 0) {
			$ancora .= "p$pool";
			$nome .= " - " . str_replace('<NUM>',$pool,Lingua::getParola("pool"));
		}
		
		echo "<tr class=\"$classe\">\n";
		if ($pool == 0) 
			echo "<td class=\"riepilogo_center\"></td>\n";
		else
			echo "<td class=\"riepilogo_center\">$numcat</td>\n";
		echo "<td class=\"riepilogo_center\">$nome</td>\n";
		if ($pool == 0) {
			echo "<td class=\"riepilogo_center\">($num)</td>\n";
			if ($propri)
				echo "<td class=\"riepilogo_center\"></td>\n";
			echo "<td class=\"riepilogo_center nostampa\"></td>\n";
		} else {
			echo "<td class=\"riepilogo_center\">$num</td>\n";
			if ($propri) {
				$num = $this->ctrl->getPropriPartecipanti($id,$pool);
				if ($num == 0) $num = "";
				echo "<td class=\"riepilogo_center\">$num</td>\n";
			}
			echo "<td class=\"riepilogo_center nostampa\"><a class=\"smallBut\" href=\"#$ancora\">$dettagli</a></td>\n";
		}
		echo '</tr>';
		return true;
	}
	
	public function stampaInizioCategoria($c, $pool) {
		$ancora = 'cat'.$c->getChiave();
		$nome = $c->getNome();
		if ($pool > 0) {
			$ancora .= "p$pool";
			$nome .= " - " . str_replace('<NUM>',$pool,Lingua::getParola("pool"));
		}
		echo "<a name=\"$ancora\"></a><h1>$nome</h1>\n";
	}
	
	/**
	 * @param Iscritto $i
	 * @param Atleta $a
	 */
	public function stampaIconaAccorpato($i, $proprio) {
		if ($proprio)
			$img = "proprio_spostato.png";
		else
			$img = "spostato.png";
		$nomeorig = $this->ctrl->getNomeCategoria($i->getCategoria());
		echo ' <img style="cursor:pointer;" src="';
		echo _PATH_ROOT_."img/$img\" title=\"$nomeorig\" onclick=\"javascript:mostraCatOriginale('$nomeorig')\">";
	}
}
?>