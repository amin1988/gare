<?php
if (!defined("_BASEDIR_")) exit();
include_model("Societa");
include_controller("admin/gestione_esterna");

class ModificaEsterna extends GestioneEsterna {
	/**
	 * @var Societa
	 */
	private $soc;
	
	public function __construct() {
		parent::__construct(true);
	}
	
	protected function controlli() {
		$this->soc = new Societa($_GET["id"]);
		if (!$this->soc->esiste()) return false;
		return true;
	}
	
	protected function salvaSocieta() {
		$s = $this->soc;
		$s->setNome($_POST["nome"]);
		$s->setNomeBreve($_POST["nomebreve"]);
		$s->setStile($_POST["stile"]);
		$s->setZona(GestioneEsterna::getZonaPost());
		$s->setFedEst($_POST["fed_est"]);
		$s->salva();
		
		redirect("admin/est/");
		exit();
	}

	public function getNome() {
		return $this->getValue("nome", $this->soc->getNome());
	}
	
	public function getNomeBreve() {
		return $this->getValue("nomebreve", $this->soc->getNomeBreve());
	}
	
	public function getStile() {
		return $this->getValue("stile", $this->soc->getStile());
	}
	
	public function getFedEst() {
		return $this->getValue("fed_est", $this->soc->getFedEst());
	}
	
	public function getZona() {
		if (!isset($_POST["zona"])) return $this->soc->getZona();
		return GestioneAffiliata::getZonaPost();
	}
}