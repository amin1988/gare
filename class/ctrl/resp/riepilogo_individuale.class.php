<?php
if (!defined("_BASEDIR_")) exit();
include_controller("resp/riepilogo_backend");
include_model("IscrittoIndividuale", "Cintura", "Stile");

//TODO eliminare
class RiepilogoIndividualeBackend extends RiepilogoBackend {
	private $unita;
	
	/**
	 * @var IscrittoIndividuale[][][] formato: idcategoria => pool => IscrittoIndividuale[]
	 */
	private $iscritti;
	
	public function __construct() {
		parent::__construct();
		if (!$this->gara->isIndividuale()) {
			redirect("resp/riepilogosq.php?id=".$_GET["id"]);
			exit();
		}
		
		if ($this->gara->usaPeso()) {
			$this->unita = " Kg";
		} else {
			$this->unita = " cm";
		}
		
		//lettura iscritti
		$iscr = IscrittoIndividuale::listaGara($this->gara->getChiave());
		
		$this->caricaCategorie($iscr);
		$this->caricaAtleti($iscr);
	}
	
	/**
	 * @param IscrittoIndividuale[] $iscr
	 */
	private function caricaAtleti($iscr) {
		$socid = array();
		$this->iscritti = array();
		foreach ($iscr as $value) {
			/* @var $value IscrittoIndividuale */
			$ida = $value->getAtleta();
			$ids = $value->getSocieta();
			$socid[$ids][$ida] = $ida;
			$this->iscritti[$value->getCategoriaFinale()][$value->getPool()][] = $value;
		}
		$this->caricaSocieta($socid);
			
		//ordina gli iscritti
		foreach ($this->iscritti as $idc => $pools) {
			foreach (array_keys($pools) as $pool)
				usort($this->iscritti[$idc][$pool],array($this, "compareIsc"));
		}
	}
	
	/**
	 * @param IscrittoIdividuale $a
	 * @param IscrittoIdividuale $b
	 */
	private function compareIsc($a, $b) {
		$aa = $this->getAtleta($a);
		$ab = $this->getAtleta($b);
		$c = strcasecmp($aa->getCognome(), $ab->getCognome());
		if ($c != 0) return $c;
		return strcasecmp($aa->getNome(), $ab->getNome());
	}
	
	/**
	 * @param int $idcat
	 * @return IscrittoIndividuale[][] formato: pool => IscrittoIndividuale[]
	 */
	public function getIscritti($idcat) {
		return $this->iscritti[$idcat];
	}
	
	/**
	 * @param IscrittoIndividuale $isc
	 * @return Atleta
	 */
	public function getAtleta($isc) {
		return $this->atleti[$isc->getSocieta()][$isc->getAtleta()];
	}
	
	/**
	 * @param IscrittoIndividuale $i
	 * @return string
	 */
	public function getNomeCintura($i) {
		return Cintura::getCintura($i->getCintura())->getNome();
	}
	
	/**
	 * @param IscrittoIndividuale $i
	 * @return string
	 */
	public function getStile($i) {
		if (is_null($i->getStile()))
			return "";
		else
			return Stile::getStile($i->getStile())->getNome();
	}
	
	/**
	 * @param IscrittoIndividuale $i
	 * @return string
	 */
	public function getPeso($i) {
		if (is_null($i->getPeso()))
			return "";
		else
			return $i->getPeso() . $this->unita;
	}
	
	/**
	 * @param Categoria $cat
	 * @return boolean
	 */
	public function mostraStile($cat) {
		return $cat->getTipo() == 0;
	}
	
	/**
	 * @param Categoria $cat
	 * @return boolean
	 */
	public function mostraPeso($cat) {
		return $cat->getTipo() == 1;
	}
	
}