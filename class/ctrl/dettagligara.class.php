<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente","Gara","Allegato");

class DettagliGara {
	/**
	 * @var Gara
	 */
	private $gara;
	/** @var Utente */
	private $ut;
	
	public function __construct() {
		$this->ut = Utente::crea();
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste() || (is_null($this->ut) && !$this->gara->isPubblica())) {
			homeutente($this->ut);
			exit();
		}
	}
	
	public function loginEffettuato() {
		return !is_null($this->ut);
	}
	
	public function mostraModulo() {
		return (!$this->gara->iscrizioniChiuse() && ($this->ut == null || $this->ut->getTipo() == Utente::SOCIETA));
	}
	
	public function mostraModifica() {
		return ($this->ut != null && $this->ut->getTipo() == Utente::ORGANIZZATORE);
	}
	
	/**
	 * @return Gara
	 */
	public function getGara() {
		return $this->gara;
	}
	
	/**
	 * @return int
	 */
	public function getIdGara() {
		return $this->gara->getChiave();
	}
	
	/**
	 * @return boolean
	 */
	public function isDoppioTipo() {
		return $this->gara->isSquadre() && $this->gara->isIndividuale();
	}
	
	/**
	 * @return boolean
	 */
	public function isIndividuale() {
		return $this->gara->isIndividuale();
	}
	
	/**
	 * @return Utente
	 */
	public function getUtente() {
		return $this->ut;
	}
	
	/**
	 * @return string
	 */
	public function getLocandinaUrl() {
		return _PATH_ROOT_.$this->gara->getLocandina();
	}
	
	/**
	 * @return boolean
	 */
	public function haAllegati() {
		return count($this->gara->getAllegati()) > 0;
	}
	
	/**
	 * @return Allegato[]
	 */
	public function getAllegati() {
		return $this->gara->getAllegati();
	}
	
	/**
	 * @param Allegato $doc
	 */
	public function getAllegatoUrl($doc) {
		return _PATH_ROOT_."download_allegato.php?id=".$doc->getChiave();
	}
}
?>