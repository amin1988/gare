<?php
if (!defined("_BASEDIR_")) exit();
include_model("UtSocieta", "Gara", "Squadra", "Zona", "Categoria");
include_controller("soc/iscrivi_base");
include_errori("VerificaCoach");

class IscriviSquadre extends IscriviBase {
	/**
	 * @var Squadra[]
	 */
	private $squadre;
		
	public function __construct() {
		$this->ut = UtSocieta::crea();
		if (is_null($this->ut)) nologin();
		
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}

		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste() || $this->gara->iscrizioniChiuse()) {
			if ($_SESSION["backdoor"] != "aprigara") {
				homeutente($this->ut);
				exit();
			}
		}
		
		if (!$this->gara->isSquadre()) {
			redirect("soc/iscrivi.php?id=".$this->gara->getChiave());
			exit();
		}
		
		$soc = $this->ut->getSocieta();
		
		//controllo zone
		$zonaut  = $soc->getZona();
		$zonegara = $this->gara->getZone();
		$trovata = false;
		while(!is_null($zonaut)) {
			if (in_array($zonaut, $this->gara->getZone())) {
				$trovata = true;
				break;
			}
			$zonaut = Zona::getZona($zonaut)->getPadre();
		}
		if (!$trovata) {
			homeutente($this->ut);
			exit();
		}
		
		$this->caricaCoach();
		$this->nere = $soc->getAtleti(NULL, true);
		
		$this->errCoach = new VerificaCoach($this->gara);
		if($_POST["salva_coach"])
			$this->salvaCoach(NULL, !$this->errCoach->haErroreNum());
		
		$this->caricaArbitri();
		if($_POST["salva_arb"])
			$this->salvaArb(NULL);
		
		//legge squadre
		$this->squadre = Squadra::listaSocieta($soc->getChiave(), $this->gara->getChiave());
		
		$this->pulisciCoach();
		
		
	}
	
	public function getGara() {
		return $this->gara;
	}
	
	public function haSquadre() {
		return count($this->squadre) > 0;
	}
	
	public function getSquadre() {
		return $this->squadre;
	}
	
	/**
	 * @param Squadra $sq
	 */
	public function getNomeCategoria($sq) {
		$idc = $sq->getCategoria();
		$cat = $this->gara->getCategorieSquadre();
		return $cat[$idc]->getNome();
	}
}