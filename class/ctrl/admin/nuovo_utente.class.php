<?php
if (!defined("_BASEDIR_")) exit();
include_model("Zona");
include_controller("admin/gestione_utente");

class NuovoUtente extends GestioneUtente {
	/**
	 * @var boolean se è un utente di una società specifica
	 */
	private $utsoc;
	/**
	 * @var Societa
	 */
	private $soc = NULL;
	
	/**
	 * @param boolean $utsoc se è un utente di una società specifica
	 */
	public function __construct($utsoc=false) {
		$this->utsoc = $utsoc;
		parent::__construct(true);
	}
	
	protected function controlli() {
		if (!$this->utsoc) return true;
		include_model("Societa");
		if (!isset($_GET["idsoc"])) return false;
		$this->soc = new Societa($_GET["idsoc"]);
		if (!$this->soc->esiste()) return false;
		 
		return true;
	}
	
	public function getUsername() {
		return $this->getValue("username");
	}
	
	public function getNome() {
		return $this->getValue("nome");
	}
	
	public function getEmail() {
		return $this->getValue("email");
	}
	
	public function getTipo() {
		if ($this->utsoc)
			return Utente::SOCIETA;
		else
			return $this->getValue("tipo");
	}
	
	public function getSocieta() {
		if ($this->utsoc)
			return $this->soc->getChiave();
		else
			return $this->getValue("soc",NULL);
	}
	
	public function getNomeSocieta() {
		if ($this->utsoc)
			return $this->soc->getNome();
		else
			return "";
	}
	
	public function getZone() {
		if (!isset($_POST["zona"])) return array();
		return Zona::listaZone($_POST["zona"]);
	}
	
	protected function salvaUtente() {
		if ($this->utsoc) {
			$_POST["soc"] = $this->soc->getChiave();
			$_POST["tipo"] = Utente::SOCIETA;
		}
		$dati = array("user" => $_POST["username"], "nome" => $_POST["nome"], "email" => $_POST["email"]);
		$psw = $_POST["psw"];
		switch ($_POST["tipo"]) {
			case Utente::ADMIN:
				include_model("Amministratore");
				$u = Amministratore::nuovo($psw, $dati);
				break;
			case Utente::ORGANIZZATORE:
				include_model("Organizzatore");
				$u = Organizzatore::nuovo($psw, $dati, $_POST["zona"]);
				break;
			case Utente::RESPONSABILE:
				include_model("Responsabile");
				$u = Responsabile::nuovo($psw, $dati, $_POST["zona"]);
				break;
			case Utente::VISUALIZZA:
				include_model("Visualizzatore");
				$u = Visualizzatore::nuovo($psw, $dati, $_POST["zona"]);
				break;
			case Utente::SOCIETA:
				include_model("UtSocieta");
				$u = UtSocieta::nuovo($_POST["soc"], $psw, $dati);
				break;
		}
		
		$u->salva();
		if ($this->utsoc)
			redirect("admin/aff");
		else
			$this->redirect($u);
	}
}