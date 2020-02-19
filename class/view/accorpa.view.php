<?php
if (!defined("_BASEDIR_")) exit();

class AccorpaView {
	/**
	 * @var Accorpa
	 */
	private $ctrl;
	private $id;
	
	/**
	 * @param Accorpa $ctrl
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
		$this->id = $ctrl->getGara()->getChiave();
	}
	
	public function pulsanti() {
		$lang = Lingua::getParole();
		?>
		<div class="pulsante tr">
		<input type="submit" name="salva" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
		<div class="separatore_pulsante"></div>
		<?php
		if(isset($_SESSION['SuperAccorpa']))
		{
			if($_SESSION['SuperAccorpa'])
			{
				$str = $lang["super_acc_on"];
				echo "<a href=\"toggle_accorpa.php?id=$this->id\">$str</a>";
			}
			else 
			{
				$str = $lang["super_acc_off"];
				echo "<a href=\"toggle_accorpa.php?id=$this->id\">$str</a>";
			}
		}
		else 
		{
			$str = $lang["super_acc_off"];
			echo "<a href=\"toggle_accorpa.php?id=$this->id\">$str</a>";
		}
		?>
		<?php 
		if ($this->ctrl->getGara()->listaPubblicata()) {
			?>
			<div class="separatore_pulsante"></div>
			<input type="submit" name="nopubbl" value="<?php echo $lang["nascondi_partecipanti"]; ?>" />
			<?php 
		} else { //!lista pubblicata
			?>
			<div class="separatore_pulsante"></div>
			<input type="submit" name="pubbl" value="<?php echo $lang["pubblica_partecipanti"]; ?>" />
			<div class="separatore_pulsante"></div>
			<input type="submit" name="salva_pubbl" value="<?php echo $lang["salva_e_pubblica"]; ?>" />
			<?php 
		} //if !lista pubblicata 
		?>
		</div>
		<?php	
	}
	
	public function stampaCategorieJavascript() {
		echo "var cats = new Object();\n";
		foreach ($this->ctrl->getCategorie() as $id=>$cat) {
			/* @var $cat Categoria */
			$nome = $cat->getNome();
			$num = $this->ctrl->getNumPartecipanti($id);
			$status = $this->ctrl->getStatus($id);
			echo "cats[$id] = new Categoria($id, \"$nome\", $num, $status);\n";
		}
		foreach ($this->ctrl->getCategorie() as $id=>$cat) {
			$acc = $this->ctrl->getAccorpabili($id);
			if (count($acc) > 0) $this->stampaListaCatJs($id, "vicine", $acc);
			switch ($this->ctrl->getStatus($id)) {
				case 1: //accorpata eliminata
					echo "cats[$id].incat = cats[".$this->ctrl->getAccorpataDest($id) . "];\n";
					break;
				case 2: //accorpata principale
					$this->stampaListaCatJs($id, "accorpate", $this->ctrl->getAccorpateSrc($id));
					echo "cats[$id].tot = ".$this->ctrl->getNumTotale($id).";";
					break;
			}
		}
	}
	
	private function stampaListaCatJs($id, $prop, $lista) {
		echo "cats[$id].$prop = [";
		$prima = true;
		foreach ($lista as $c) {
			if ($prima) $prima = false;
			else echo ",";
			if (!is_numeric($c)) $c = $c->getChiave();
			echo "\n  cats[$c]";
		}
		echo "\n];\n";
	}
	
	/**
	 * @param Categoria[] $catlist
	 */
	public function stampaCategorie($catlist, $indiv=true) {
		if (count($catlist) == 0) {
			echo Lingua::getParola("no_partecipanti");
			return;
		}
?>
		<table class="atleti" >
		<tr  class="tr">
		<th colspan="2"><div class='thAtleti'><?php echo Lingua::getParola("categoria"); ?></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_partecipanti_cat"); ?></div></th>
		<th class="nostampa" colspan="2"><div class='thAtleti'></div></th>
		</tr>
		
<?php 
		$accorpa = Lingua::getParola("accorpa_cat");
		$separa = Lingua::getParola("separa_cat");
		$annulla = Lingua::getParola("annulla_accorpamento");
		$idg = $this->ctrl->getGara()->getChiave();
		if ($indiv)
			$pagRiep = "riepilogo";
		else
			$pagRiep = "riepilogosq";
		$count = 0;
		foreach ($catlist as $id => $c) {
			/* @var $c Categoria */
			if (($count % 2) == 0) $classe = "riga1";
			else $classe = "riga2";
			$classe .= " status" . $this->ctrl->getStatus($id);
			
			echo "<tr class=\"$classe\" id=\"cat_$id\">\n";
			echo "<td class=\"riepilogo_center nomecat\" id=\"nomecat_$id\">";
			echo "<a href=\"$pagRiep.php?orig&id=$idg#cat$id\" target=\"_blank\">";
			echo $c->getNome();
			echo "</a></td>\n";
			echo "<td class=\"riepilogo_center logocat\" onclick=\"logoClick('$id')\"></td>\n";
			echo "<td class=\"riepilogo_center\" id=\"numcat_$id\">".$this->ctrl->getNumPartecipanti($id);
			if ($this->ctrl->getStatus($id) == 2)
				echo " (".$this->ctrl->getNumTotale($id).")";
			echo "</td>\n";
			echo '<td class="riepilogo_center nostampa">';
			if ($this->ctrl->puoSeparare($id)) {
				echo "<a class=\"smallBut separa\" href=\"javascript:separa('$id');\">$separa</a>";
				echo "<input type=\"hidden\" value=\"".$this->ctrl->isSeparata($id);
				echo "\" name=\"separa[$id]\" id=\"camposep_$id\">";
			}
			if ($this->ctrl->puoAccorpare($id)) {
				echo "<a class=\"smallBut accorpa\" href=\"javascript:mostraAccorpa('$id');\">$accorpa</a> ";
				echo "<input type=\"hidden\" value=\"".$this->ctrl->getAccorpataDest($id);
				echo "\" name=\"accorpa[$id]\" id=\"campoacc_$id\">";
			}
			echo "<a class=\"smallBut annulla\" href=\"javascript:annulla('$id');\">$annulla</a> ";
			echo "</td>\n";
			echo '</tr>';
			$count++;
		}
		echo "</table>";
	}
}
?>