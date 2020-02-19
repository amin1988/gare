<?php
if (!defined("_BASEDIR_")) exit();
include_model("Amministratore");
include_errori("VerificaUtente");

abstract class GestioneUtente {
	const EMAIL_REGEX = '[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}';
	
	/** @var Amministratore */
	protected $ut;
	protected $errori;
	
	public function __construct($nuovo) {
		$this->ut = Amministratore::crea();
		if (is_null($this->ut)) nologin();
		
		if (!$this->controlli()) {
			homeutente($this->ut);
			exit();
		}
		
		//verifica errori
		$this->errori = new VerificaUtente($nuovo);
		if (isset($_POST["pageid"]) && !$this->errori->haErrori())
	 		$this->salvaUtente();
	}
	
	protected function controlli() {
		return true;
	}
	
	public function getTipiUtente() {
		return Utente::getTipiUtente();
	}
	
	public function getErrori() {
		return $this->errori;
	}
	
	protected abstract function salvaUtente();
	
	public function getUtente() {
		return $this->ut;
	}
	
	protected function getValue($campo, $default="") {
		if (isset($_POST[$campo])) return $_POST[$campo];
		else return $default;
	}
	
	protected function getArrayValue($campo, $id, $default="") {
		if (isset($_POST[$campo]) && isset($_POST[$campo][$id])) return $_POST[$campo][$id];
		else return $default;
	}
	
	public abstract function getUsername();
	
	public abstract function getNome();
	
	public abstract function getEmail();
	
	public abstract function getTipo();
	
	/**
	 * @return int idsocieta o NULL
	 */
	public abstract function getSocieta();
	
	public function isSocietaSelezionata() {
		return $this->getTipo() == Utente::SOCIETA;
	}
	
	public function isTipoZonaSelezionato() {
		return $this->getTipo() == Utente::ORGANIZZATORE 
			|| $this->getTipo() == Utente::RESPONSABILE
			|| $this->getTipo() == Utente::VISUALIZZA;
	}
	
	/**
	 * @return Zona[]
	 */
	public abstract function getZone();
	
	/**
	 * @param Utente $ut
	 */
	protected function redirect($ut) {
		switch ($ut->getTipo()) {
			case Utente::ADMIN:
				$anc = "admin";
				break;
			case Utente::ORGANIZZATORE:
				$anc = "org";
				break;
			case Utente::RESPONSABILE:
				$anc = "resp";
				break;
			case Utente::SOCIETA:
				$anc = "soc";
				break;
			case Utente::VISUALIZZA:
				$anc = "vis";
				break;
		}
		redirect("admin/ut/#$anc");
	}
}