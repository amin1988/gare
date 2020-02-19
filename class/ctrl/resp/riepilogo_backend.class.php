<?php
if (!defined("_BASEDIR_")) exit();
include_model("Responsabile", "Gara", "Cintura", "Stile", "Societa");
include_class("Sesso");

//TODO eliminare
class RiepilogoBackend {
	/** @var Responsabile */
	protected $ut;
	/** @var Gara */
	protected $gara;
	
	/** @var Categoria[] */
	protected $categorie;
	
	/**
	 *  @var Categoria[] formato: idcategoria => int
	 */
	protected $catcount;

	/**
	 * @var Atleta[][] formato: idsocieta => idatleta => Atleta
	 */
	protected $atleti;
	/**
	 * @var string[] nomi delle societa
	 */
	protected $soc;
	
	public function __construct() {
		$this->ut = Responsabile::crea();
		if (is_null($this->ut)) nologin();
		
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste() || $this->gara->passata()) {
			homeutente($this->ut);
			exit();
		}
	}
	
	/**
	 * @param Iscritto[] $iscr
	 */
	protected function caricaCategorie($iscr) {
		$idcats = array();
		if (count($iscr) == 0) {
			$this->categorie = array();
		} else {
			foreach ($iscr as $i) {
				/* @var $i Iscritto */
				$idc = $i->getCategoria();
				$ida = $i->getAccorpamento();
				if (!is_null($ida)) {
					if (!isset($this->catcount[$idc])) {
						$this->catcount[$idc] = 0;
						$idcats[$idc] = $idc;
					}
					$idc = $ida;
				}
				if (!isset($this->catcount[$idc])) {
					$this->catcount[$idc][0] = 1;
					$idcats[$idc] = $idc;
				} else {
					$this->catcount[$idc][0]++;
				}
				if ($i->isSeparato()) {
					if (!isset($this->catcount[$idc][$i->getPool()])) 
						$this->catcount[$idc][$i->getPool()] = 1;
					else
						$this->catcount[$idc][$i->getPool()]++;
				}
			}
			$this->categorie = Categoria::lista($idcats);
		}
		//uksort($this->categorie, array($this, "compareCat"));
		uasort($this->categorie, array("Categoria", "compare"));
	}
	
	/**
	 * @param int[][] $socid formato idsocieta => idatleti[]
	 */
	protected function caricaSocieta($socid) {
		$soc = Societa::lista(array_keys($socid));
		$this->atleti = array();
		foreach ($soc as $ids => $s) {
			$this->salvaSocieta($ids, $s, $socid[$ids]);
		}
	}
	
	/**
	 * @param int $ids
	 * @param Societa $s
	 */
	protected function salvaSocieta($ids, $s, $atl) {
		$this->soc[$ids] = $s->getNome();
		$this->atleti[$ids] = $s->getAtleti($atl);
	}
	
	public function haIscritti() {
		return count($this->categorie) > 0;
	}
	
	/**
	 * @return Gara
	 */
	public function getGara() {
		return $this->gara;
	}
	
	/**
	 * @return Categoria[]
	 */
	public function getCategorie() {
		return $this->categorie;
	}
	
	/**
	 * @param int $idcat
	 * @return int o int[] se la categoria è divisa in pool
	 */
	public function getNumPartecipanti($idcat) {
		return $this->catcount[$idcat];
	}
	
	/**
	 * @param int $idc
	 * @return string
	 */
	public function getNomeCategoria($idc) {
		return $this->categorie[$idc]->getNome();
	}
	
	/**
	 * @param int $ida
	 * @param int $idb
	 */
	private function compareCat($ida, $idb) { //TODO eliminare
		return $this->catcount[$idb][0] - $this->catcount[$ida][0];
	}
	
	/**
	 * @param Atleta $a
	 * @return string
	 */
	public function getNomeSocieta($a) {
		return $this->soc[$a->getSocieta()];
	}
		
	/**
	 * @param Atleta $a
	 * @return string
	 */
	public function getNomeSesso($a) {
		return Sesso::toStringBreve($a->getSesso());
	}	

}