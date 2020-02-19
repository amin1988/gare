<?php
if (!defined("_BASEDIR_")) exit();

//TODO eliminare
class RiepilogoBackendView {
	/**
	 * @var RiepilogoBackend
	 */
	private $ctrl;
	
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
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
	 */
	public function altroRiepilogo($squadre) {
		if ($squadre) {
			$altro = $this->ctrl->getGara()->isSquadre();
			$pag = "riepilogosq";
			$lch = "riepilogo_squadre";
		} else {
			$altro = $this->ctrl->getGara()->isIndividuale();
			$pag = "riepilogo"; 
			$lch = "riepilogo_individuali";
		}
		
		$idg = $this->ctrl->getGara()->getChiave();
		$url = $url = _PATH_ROOT_."resp/download_iscrizioni.php?id=$idg";
		$testo = Lingua::getParola("scarica_iscrizioni");
		echo "<div class=\"pulsante tr nostampa\" style=\"text-align:center\">";
		echo "<a href=\"$url\" target=\"_blank\" class='pulsante_noInput'>$testo</a>\n";
		if ($this->ctrl->getGara()->iscrizioniChiuse()) {
			$url = _PATH_ROOT_."resp/accorpa.php?id=$idg";
			$testo = Lingua::getParola("accorpa_titolo");
			echo '<div class="separatore_pulsante"></div>';
			echo "<a href=\"$url\" class='pulsante_noInput'>$testo</a>\n";
		}
		if ($altro) {
			$url = _PATH_ROOT_."resp/$pag.php?id=$idg";
			$testo = Lingua::getParola($lch);
			echo '<div class="separatore_pulsante"></div>';
			echo "<a href=\"$url\" class='pulsante_noInput'>$testo</a>\n";
		}
		echo "<br></div><br><br>";
		
	}
	
	public function tabellaCategorie() {
		echo '<h1>'.Lingua::getParola("riepilogo_categorie").'</h1>';
		?>
		<table class="atleti" >
		<tr  class="tr">
		<th><div class='thAtleti'><?php echo Lingua::getParola("categoria"); ?></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_partecipanti_cat"); ?></div></th>
		<th class="nostampa"><div class='thAtleti'></div></th>
		</tr>
		
		<?php 
		
		$count=0;
		$dettagli = Lingua::getParola("dettagli_categoria");
		foreach ($this->ctrl->getCategorie() as $c) {
			$id = $c->getChiave();
			$num = $this->ctrl->getNumPartecipanti($id);
			if (isset($num[1])) {
				ksort($num);
				foreach ($num as $pool => $pnum) {
					$this->stampaRigaCat($c, $id, $dettagli, $pnum, $pool, $count);
				}
				$count++;
			} else 
				if ($this->stampaRigaCat($c, $id, $dettagli, $num[0], -1, $count))
					$count++;
// 			if ($num > 0) {
// 				if (($count % 2) == 0) $classe = "riga1";
// 				else $classe = "riga2";
				
// 				echo "<tr class=\"$classe\">\n";	
// 				echo '<td class="riepilogo_center">'.$c->getNome()."</td>\n";
// 				echo "<td class=\"riepilogo_center\">$num</td>\n";
// 				echo "<td class=\"riepilogo_center nostampa\"><a class=\"smallBut\" href=\"#cat$id\">$dettagli</a></td>\n";
// 				echo '</tr>';
// 				$count++;
// 			}
		}
		
		echo '</table>';
	}
	
	private function stampaRigaCat($c, $id, $dettagli, $num, $pool, &$count) {
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
		echo "<td class=\"riepilogo_center\">$nome</td>\n";
		if ($pool == 0) {
			echo "<td class=\"riepilogo_center\">($num)</td>\n";
			echo "<td class=\"riepilogo_center nostampa\"></td>\n";
		} else {
			echo "<td class=\"riepilogo_center\">$num</td>\n";
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
	 */
	public function stampaIconaAccorpato($i) {
		$nomeorig = $this->ctrl->getNomeCategoria($i->getCategoria());
		echo ' <img style="cursor:pointer;" src="';
		echo _PATH_ROOT_."img/spostato.png\" title=\"$nomeorig\" onclick=\"javascript:mostraCatOriginale('$nomeorig')\">";
	}
}
?>