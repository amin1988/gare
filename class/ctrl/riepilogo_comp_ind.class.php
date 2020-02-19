<?php
if (!defined("_BASEDIR_")) exit();
include_controller("riepilogo_completo");
include_model("IscrittoIndividuale", "Cintura", "Stile");

class RiepilogoIndividualeCompleto extends RiepilogoCompleto {
	private $unita;
	
	/**
	 * @var IscrittoIndividuale[][][] formato: idcategoria => pool => IscrittoIndividuale[]
	 */
	private $iscritti;
	
	/**
	 * @param int $tipout tipo utente
	 * @param string $altrapag url della pagina di riepilogo 
	 * dell'altro tipo (individuale/squadre)
	 */
	public function __construct($tipout) {
		parent::__construct($tipout);
		if (!$this->gara->isIndividuale()) {
			homeutente($this->ut);
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
	
	public function getUtente() {
		return $this->ut;
	}
	
	/**
	 * @param IscrittoIndividuale[] $iscr
	 */
	private function caricaAtleti($iscr) {
		$socid = array();
		$this->iscritti = array();
		$mostraAcc = $this->mostraAccorpamenti();
		foreach ($iscr as $value) {
			/* @var $value IscrittoIndividuale */
			$ida = $value->getAtleta();
			$ids = $value->getSocieta();
			$socid[$ids][$ida] = $ida;
			if ($mostraAcc)
				$idc = $value->getCategoriaFinale();
			else 
				$idc = $value->getCategoria();
			$this->iscritti[$idc][$value->getPool()][] = $value;
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
		return ($cat->getTipo() == 1) || ($cat->getGruppo() == 29);
	}
	
	protected function getIscrittiPerCat() {
		return $this->iscritti;
	}
	
}