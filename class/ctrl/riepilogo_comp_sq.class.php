<?php
if (!defined("_BASEDIR_")) exit();
include_controller("riepilogo_completo");
include_model("Squadra", "Cintura");

class RiepilogoSquadreCompleto extends RiepilogoCompleto  {
	
	/**
	 * @var Squadra[][][] formato: idcategoria => pool => Squadra[]
	 */
	private $squadre;
	
	/**
	 * @var string[] formato idsocieta => nome breve
	 */
	private $nomebreve;
	
	/**
	 * @param int $tipout tipo utente
	 * @param string $altrapag url della pagina di riepilogo 
	 * dell'altro tipo (individuale/squadre)
	 */
	public function __construct($tipout, $altrapag) {
		parent::__construct($tipout, $altrapag);
		if (!$this->gara->isSquadre()) {
			redirect("resp/riepilogo.php?id=".$_GET["id"]);
			exit();
		}
		
		//lettura squadre
		$squadre = Squadra::listaGara($this->gara->getChiave());
		
		$this->caricaCategorie($squadre);
		$this->caricaSquadre($squadre);
	}
	
	public function getUtente() {
		return $this->ut;
	}
	
	/**
	 * @param Squadra[] $squadre
	 */
	private function caricaSquadre($squadre) {
		$socid = array();
		$this->squadre = array();
		$mostraAcc = $this->mostraAccorpamenti();
		foreach ($squadre as $value) {
			/* @var $value Squadra */
			$ids = $value->getSocieta();
			foreach ($value->getComponenti() as $ida) {
				$socid[$ids][$ida] = $ida;
			}
			if ($mostraAcc)
				$idc = $value->getCategoriaFinale();
			else
				$idc = $value->getCategoria();
			$this->squadre[$idc][$value->getPool()][] = $value;
		}
		$this->caricaSocieta($socid);
			
		//ordina gli iscritti
		foreach ($this->squadre as $idc => $pools) {
			foreach (array_keys($pools) as $pool)
				usort($this->squadre[$idc][$pool],array($this, "compareSq"));
		}
		
	}
	
	protected function salvaSocieta($ids, $s, $atl) {
		parent::salvaSocieta($ids, $s, $atl);
		$this->nomebreve[$ids] = $s->getNomeBreve();
	}
	
	/**
	 * @param Squadra $a
	 * @param Squadra $b
	 */
	private function compareSq($a, $b) {
		$sa = $a->getSocieta();
		$sb = $b->getSocieta();
		if ($sa == $sb) {
			$na = $a->getNumero();
			$nb = $b->getNumero();
			if ($na > $nb) return 1;
			if ($na < $nb) return -1;
			return 0;
		} else {
			return strcasecmp($this->nomebreve[$sa], $this->nomebreve[$sb]);
		}
	}
	
	/**
	 * @param int $idcat
	 * @return Squadra[][] formato: pool => Squadra[]
	 */
	public function getSquadre($idcat) {
		if (isset($this->squadre[$idcat]))
			return $this->squadre[$idcat];
		else
			return array();
	}
	
	/**
	 * @param Squadra $sq
	 * @return string
	 */
	public function getNomeSquadra($sq) {
		return $this->nomebreve[$sq->getSocieta()].' '.$sq->getNumero();
	}
	
	/**
	 * @param Squadra $sq
	 * @param int $ida
	 * @return Atleta
	 */
	public function getAtleta($sq, $ida) {
		return $this->atleti[$sq->getSocieta()][$ida];
	}
	
	/**
	 * @param Squadra $sq
	 * @param int $ida
	 * @return string
	 */
	public function getNomeCintura($sq, $ida) {
		$idc = $sq->getCinturaComponente($ida);
		return Cintura::getCintura($idc)->getNome();
	}
	
	protected function getIscrittiPerCat() {
		return $this->squadre;
	}
}