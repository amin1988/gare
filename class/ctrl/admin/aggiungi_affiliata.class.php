<?php
if (!defined("_BASEDIR_")) exit();
include_model("Societa");
include_esterni("SocietaAffiliata");
include_controller("admin/gestione_affiliata");

class AggiungiAffiliata extends GestioneAffiliata {
	/**
	 * @var SocietaAffiliata
	 */
	private $soc;
	
	public function __construct() {
		parent::__construct(true);
	}
	
	protected function controlli() {
		$this->soc = new SocietaAffiliata($_GET["id"]);
		if (!$this->soc->esiste()) return false;
		if (Societa::isAffiliataInserita($this->soc->getChiave()))
			return false;
		
		return true;
	}
	
	protected function salvaSocieta() {
		$dati = array(
				"nome" => $_POST["nome"],
				"nomebreve" => $_POST["nomebreve"], 
				"zona" => GestioneAffiliata::getZonaPost(), 
				"stile" => $_POST["stile"],
				"affiliata" => $this->soc->getChiave()
				);
		$s = Societa::nuovo($dati);
		$s->salva();
		
		if (isset($_POST["user"])) {
			include_model("UtSocieta");
			$sf = $this->soc;
			$datiut = array(
					"user" => $sf->getUsername(), 
					"nome" => $sf->getContatto(), 
					"email" => $sf->getEmail()
					);
			$user = UtSocieta::nuovo($s->getChiave(), $sf->getPassword(), $datiut);
			$user->salva();
		}
		
		if (isset($_POST["user"]))
			redirect("admin/aff/");
		else
			redirect("admin/aff/nuovo_utente.php?idsoc=".$s->getChiave());
		exit();
	}

	public function getNome() {
		return $this->getValue("nome", $this->soc->getNome());
	}
	
	public function getNomeBreve() {
		return $this->getValue("nomebreve");
	}
	
	public function getStile() {
		return $this->getValue("stile");
	}
	
	public function getZona() {
		if (!isset($_POST["zona"])) return $this->soc->getZona();
		return GestioneAffiliata::getZonaPost();
	}
}