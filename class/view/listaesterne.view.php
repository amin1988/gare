<?php
if (!defined("_BASEDIR_")) exit();

class ListaEsterneView {
	/**
	 * @var ListaAffiliate
	 */
	private $ctrl;
	
	/**
	 * @param ListaUtenti $ctrl
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
	}
	/*
	public function stampaNonInserite() {
		$lista = $this->ctrl->getNonInserite();
		if (count($lista) == 0) return;
		$this->stampaInizioTabella(Lingua::getParola("affiliate_non_inserite"));
		$count = 0;
		foreach ($lista as $id => $nome) {
			$this->stampaInizioRiga($count, $nome);
			$this->stampaPulsante(Lingua::getParola("aggiungi_affiliata"), "aggiungi.php?id=$id");
			$this->stampaFineRiga();
			$count++;
		}
		$this->stampaFineTabella();
	}
	*/
	public function stampaInserite() {
		$lista = $this->ctrl->getInserite();
		if (count($lista) == 0) return;
		$this->stampaInizioTabella(Lingua::getParola("esterne_inserite"));
		$count = 0;
		foreach ($lista as $s) {
			$id = $s->getChiave();
			$nome = $s->getNome();
			$this->stampaInizioRiga($count, $nome);
			$this->stampaPulsante(Lingua::getParola("modifica_affiliata"), "modifica.php?id=$id");
// 			$this->stampaPulsante(Lingua::getParola("nuovo_utente_affiliata"), "nuovo_utente.php?idsoc=$id");
			$this->stampaFineRiga();
			$count++;
		}
		$this->stampaFineTabella();
	}
	
	private function stampaInizioTabella($nometab) {
		echo '<div class="Gare_soc_right">';
		echo "<h1>$nometab</h1>\n";
		
// 		echo "<h1>$nometab</h1>\n";
// 		echo '<table width="100%" ><tr class="tr">';
// 		echo '<th><div class="thAtleti">'.Lingua::getParola("societa").'</div></th>';
// 		echo "<th style='width:30%'><div class='thAtleti'></div></th>";
// 		echo '</tr>';
	}
	
	private function stampaFineTabella() {
		echo "</div>\n\n";
// 		echo "</table>\n\n";
	}
	
	private function stampaInizioRiga($count, $nome) {
		if (($count % 2) == 0) $classe = "riga1";
		else $classe = "riga2";
		
		echo "<li class=\"$classe\">";
		echo "<span class='tDescr'>$nome</span>";
			
// 		echo "<tr class=\"$classe\">\n";
// 		echo "<td class=\"riepilogo_center\">$nome</td>\n";
// 		echo '<td class="riepilogo_center">';
	}
	
	private function stampaFineRiga() {
		echo "</li>\n";
// 		echo '</td></tr>';
	}
	
	private function stampaPulsante($testo, $link) {
		echo "<a href=\"$link\">$testo</a>";
// 		echo "<a class=\"smallBut\" href=\"$link\">$testo</a>";
	}


}