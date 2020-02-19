<?php
if (!defined("_BASEDIR_")) exit();

class StatView {
	/**
	 * @var Statistiche
	 */
	private $ctrl;
	
	private $tipo;

	
	public static function paginaIntera($tipout) {
		include_view("Header", "Template");
		include_controller("stat");
	
		$ctrl = new Statistiche($tipout);
		$view = new StatView($ctrl);
		$lang = Lingua::getParole();
// 		$head = Header::titolo($lang["statistiche_titolo"], $ctrl->getNomeGara());
// 		$head->setIndietro($ind_url, $lang[$ind_testo]);
		$head = Header::titolo(NULL, $ctrl->getNomeGara());
		$head->setStampa(true);
		$templ = new Template($head);
	
		$templ->stampaTagHead();
		$templ->apriBody();
	
		$view->stampaListe();
	
		$templ->chiudiBody();
	}
	
	/**
	 * @param Statistiche $this->ctrl
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
		//TODO fare meglio nomi tipi
		$this->tipo = array(0 => "Kata", 1 => "Sanbon", 2 => "Ippon");
	}
	
	public function stampaListe() {
		$lang = Lingua::getParole();
		//atleti
		echo '<br><div id="Right" style="width:90%;"><div class="Gare_soc_right">';
		echo "<h1>$lang[atleti_stat]: ";
		echo $this->ctrl->getConteggio(2);
		echo "</h1>";
		$this->atleti(true);
		$this->atleti(false);
		echo '</div></div>';
		
		//partecipanti
		$this->blocco(0);
		
		//categorie
		$this->blocco(1);
		
		//medaglie agonisti
		echo '<br><div id="Right" style="width:90%;"><div class="Gare_soc_right">';
		echo "<h1>$lang[medaglie_sezione] $lang[agonisti_stat]</h1>";
		$valori = array(Statistiche::ORO, Statistiche::ARGENTO, Statistiche::BRONZO, Statistiche::QUARTO);
		foreach ($valori as $val) {
			$num = $this->ctrl->getMedaglie($val);
			if ($num == 0) continue;
			$nomemed = $lang["medaglia_".$val];
			echo "<li><b>$nomemed: $num</b></li>\n";
			foreach (array(false, true) as $nere) {
				$num = $this->ctrl->getMedaglie($val, $nere);
				if ($num == 0) continue;
				if ($nere) //TODO lingua
					$nomenere = "Marroni-Nere";
				else
					$nomenere = "Colorate";
				echo "<li class=\"sublist\">{$nomenere}: $num</li>\n";
				foreach ($this->tipo as $t => $nt) {
					$num = $this->ctrl->getMedaglie($val, $nere, $t);
					if ($num == 0) continue;
					echo "<li class=\"subsublist\">{$this->tipo[$t]}: $num</li>\n";
				}
			}
		}
		echo '</div></div>';
		
		//medaglie non agonisti
		echo '<br><div id="Right" style="width:90%;"><div class="Gare_soc_right">';
		echo "<h1>$lang[medaglie_sezione] $lang[preagonisti_stat]</h1>";
		$valori = array(Statistiche::ORO, Statistiche::ARGENTO, Statistiche::BRONZO, Statistiche::QUARTO);
		foreach ($valori as $val) {
		$num = $this->ctrl->getMedaglieNA($val);
		if ($num == 0) continue;
		$nomemed = $lang["medaglia_".$val];
		echo "<li><b>$nomemed: $num</b></li>\n";
		foreach (array(false, true) as $nere) {
		$num = $this->ctrl->getMedaglieNA($val, $nere);
		if ($num == 0) continue;
		if ($nere) //TODO lingua
		$nomenere = "Marroni-Nere";
		else
			$nomenere = "Colorate";
				echo "<li class=\"sublist\">{$nomenere}: $num</li>\n";
						foreach ($this->tipo as $t => $nt) {
						$num = $this->ctrl->getMedaglieNA($val, $nere, $t);
						if ($num == 0) continue;
						echo "<li class=\"subsublist\">{$this->tipo[$t]}: $num</li>\n";
		}
		}
		}
		echo '</div></div>';
	}
	
	private function atleti($ind) {
		$num = $this->ctrl->getConteggio(2, $ind);
		if ($num == 0) return;
		echo "<li><b>";
		if ($ind)
			echo Lingua::getParola("atl_indiv_stat");
		else
			echo Lingua::getParola("atl_sq_stat");
		echo ": $num</b></li>\n";
		for($i=1; $i>=0; $i--) {
			$num = $this->ctrl->getConteggio(2, $ind, $i);
			if ($num == 0) continue;
			echo "<li class=\"sublist\">";
			if ($i)
				echo Lingua::getParola("agonisti_stat");
			else
				echo Lingua::getParola("preagonisti_stat");
			echo ": $num</li>\n";
		}
	} 
		
	/**
	 * @param int $tipo 0 = iscritti, 1 = categorie, 2 = atleti
	 */
	private function blocco($tipo) {
		if ($this->ctrl->getConteggio($tipo) == 0) return;
		
		$ind = $this->ctrl->isGaraIndividuale();
		$sq = $this->ctrl->isGaraSquadre();
		
		echo '<br><div id="Right" style="width:90%;"><div class="Gare_soc_right">';
		if ($ind)
			$this->individuali($tipo, true);
		if ($ind && $sq)
			echo '<br>';
		if ($sq)
			$this->individuali($tipo, false);
		echo '</div></div>';
	}
	
	/**
	 * @param boolean $cat true per stampare le categorie, false per stampare gli atleti
	 * @param boolean $indiv true per stampare gli individuali, false per stampare le squadre
	 */
	private function individuali($cat, $indiv) {
		$num = $this->ctrl->getConteggio($cat, $indiv);
		if ($num == 0) return;
		
		if ($indiv) {
			if (!$this->ctrl->isGaraIndividuale()) return;
			$strind = "indiv";
		} else {
			if (!$this->ctrl->isGaraSquadre()) return;
			$strind = "squadre";
		}
		
		if ($cat)
			$strcat = "categorie";
		else
			$strcat = "iscritti";
		
		echo "<h1>".Lingua::getParola("{$strcat}_{$strind}_stat").": $num</h1>";
		$this->agonisti($cat, $indiv, true);
		$this->agonisti($cat, $indiv, false);
	}
	
	private function agonisti($cat, $indiv, $ag) {
		$num = $this->ctrl->getConteggio($cat, $indiv, $ag);
		if ($num == 0) return;
		
		echo '<li><b>';
		if ($ag)
			echo Lingua::getParola("agonisti_stat");
		else
			echo Lingua::getParola("preagonisti_stat");
		echo ": $num</b></li>\n";
		foreach ($this->tipo as $t=>$nt) {
			$this->tipogara($cat, $indiv, $ag, $t);
		}
	}
	
	private function tipogara($cat, $indiv, $ag, $tipo) {
		$num = $this->ctrl->getConteggio($cat, $indiv, $ag, $tipo);
		if ($num == 0) return;
		
		echo "<li class=\"sublist\">{$this->tipo[$tipo]}: $num</li>\n";
		return;
	}
}
