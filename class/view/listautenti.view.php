<?php
if (!defined("_BASEDIR_")) exit();

class ListaUtentiView {
	/**
	 * @var ListaUtenti
	 */
	private $ctrl;
	
	/**
	 * @param ListaUtenti $ctrl
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
	}
	
	/**
	 * @param Utente[] $lista
	 * @param string $nome
	 * @param boolean $zone true per stampare l'elenco delle zone
	 * @param boolean $soc true per stamapre il nome della società
	 */
	private function stampaListaUtenti($anchor, $lista, $nome, $zone, $soc) {
		if (count($lista) == 0) return;
		echo "<a name=\"$anchor\"></a>";
		echo '<h1>'.Lingua::getParola($nome)."</h1>\n";
		echo '<table width="100%" ><tr class="tr">';
		echo '<th><div class="thAtleti">'.Lingua::getParola("username").'</div></th>';
		if ($zone) echo '<th><div class="thAtleti">'.Lingua::getParola("zone_utente").'</div></th>';
		if ($soc)
			echo '<th><div class="thAtleti">'.Lingua::getParola("societa").'</div></th>';
		echo "<th style='width:30%'><div class='thAtleti'></div></th>";
		echo '</tr>';
		
		$count = 0;
		foreach ($lista as $u) {
			$id = $u->getChiave();
			$nome = $u->getNome();
			if (($count % 2) == 0) $classe = "riga1";
			else $classe = "riga2";
			
			echo "<tr id=\"utente_$id\" class=\"$classe\">\n";
			echo "<td class=\"riepilogo_center\">$nome</td>\n";
			if ($zone) {
				echo '<td class="riepilogo_center">';
				$this->stampaZone($u,5);
				echo "</td>\n";
			}
			if ($soc)
				echo '<td class="riepilogo_center">'.$this->ctrl->getNomeSocieta($u)."</td>\n";
			//pulsanti
			echo '<td class="riepilogo_center">';
			echo $this->stampaPulsante(Lingua::getParola("modifica_utente"), "modifica.php?id=$id");
			if (!$this->ctrl->utenteLoggato($u)) 
				$this->stampaPulsante(Lingua::getParola("elimina_utente"), "javascript:elimina($id,'$nome');");
			if ($soc)
				$this->stampaPulsante(Lingua::getParola("modifica_societa_utente"),$this->ctrl->getUrlModSocieta($u));
			echo "</td>\n";
			echo '</tr>';
			$count++;
		}
		echo "</table>\n\n";
	}
	
	private function stampaPulsante($testo, $link) {
		echo "<a class=\"smallBut\" href=\"$link\">$testo</a> ";
	}
	
	/**
	 * @param UtGare $ut
	 */
	private function stampaZone($ut, $max=1000, $span=true) {
		$zone = $ut->getZone();
		if ($span) { 
			if(count($zone) > $max) {
				echo '<span title="';
				$this->stampaZone($ut, count($zone)+10, false);
				echo '">';
			} else 
				echo '<span>';
		}
		
		$count = 0;
		foreach ($ut->getZone() as $z) {
			if ($count >= $max) {
				echo "...";
				break;
			}
			if ($count > 0) echo ", ";
			echo $this->ctrl->getNomeZona($z);
			$count++;
		}
		if ($span) echo '</span>';
	}
	
	public function stampaAdmin() {
		$this->stampaListaUtenti("admin", $this->ctrl->getAdmin(), "lista_admin", false, false);
	}

	public function stampaResponsabili() {
		$this->stampaListaUtenti("resp", $this->ctrl->getResponsabili(), "lista_responsabili", true, false);
	}

	public function stampaVisualizzatori() {
		$this->stampaListaUtenti("vis", $this->ctrl->getVisualizzatori(), "lista_visualizzatori", true, false);
	}
	
	public function stampaOrganizzatori() {
		$this->stampaListaUtenti("org", $this->ctrl->getOrganizzatori(), "lista_organizzatori", true, false);
	}
	
	public function stampaSocieta() {
		$this->stampaListaUtenti("soc", $this->ctrl->getSocieta(), "lista_societa", false, true);
	}
}