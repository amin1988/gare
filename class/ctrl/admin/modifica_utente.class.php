<?php
if (!defined("_BASEDIR_")) exit();
include_controller("admin/gestione_utente");

class ModificaUtente extends GestioneUtente {
	/** @var Utente */
	private $modut;
	
	public function __construct() {
		parent::__construct(false);
	}
	
	protected function controlli() {
		if (!isset($_GET["id"]))  return false;
		
		$this->modut = Utente::crea($_GET["id"], true);
		if (is_null($this->modut)) return false;
		
		$_POST["tipo"] = $this->modut->getTipo();
		return true;
	}
	
	public function getUsername() {
		return $this->modut->getNome();
	}
	
	public function getNome() {
		return $this->getValue("nome", $this->modut->getContatto());
	}
	
	public function getEmail() {
		return $this->getValue("email", $this->modut->getEmail());
	}
	
	public function getTipo() {
		return $this->modut->getTipo();
	}
	
	public function getSocieta() {
		return $this->getValue("soc",$this->modut->getIdSocieta());
	}
	
	public function getZone() {
		if (isset($_POST["pageid"])) {
			if (!isset($_POST["zona"]))
				return array();
			$zone = $_POST["zona"];
		} else {
			$zone = $this->modut->getZone(); 
		}
		return Zona::listaZone($zone);
	}
	
	public function salvaUtente() {
		$u = $this->modut;
		$u->setContatto(trim($_POST["nome"]));
		$u->setEmail(trim($_POST["email"]));
		if ($_POST["psw"] != "") {
			$u->setPassword($_POST["psw"]);
		}
		switch ($u->getTipo()) {
			case Utente::SOCIETA:
				$u->setIdSocieta($_POST["soc"]);
				break;
			case Utente::ORGANIZZATORE:
			case Utente::RESPONSABILE:
				$u->setZone($_POST["zona"]);
				break;
		}
		
		$u->salva();
		$this->redirect($u);
	}
}