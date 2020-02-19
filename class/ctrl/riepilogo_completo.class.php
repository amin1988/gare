<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente", "Gara", "Cintura", "Stile", "Societa");
include_class("Sesso");
include_controller("VerificaPaginaIndividuale");

abstract class RiepilogoCompleto {
	protected $ut;
	protected $altrapag;
	/** @var Gara */
	protected $gara;
	
	/** @var Categoria[] */
	protected $categorie;
	
	/**
	 *  @var Categoria[] formato: idcategoria => int
	 */
	protected $catcount;
	
	/**
	 * numero propri partecipanti
	 * @var int[][] formato: id categoria => pool => int
	 */
	protected $propricount;

	/**
	 * @var Atleta[][] formato: idsocieta => idatleta => Atleta
	 */
	protected $atleti;
	/**
	 * @var string[] nomi delle societa
	 */
	protected $soc;
	
	/**
	 * @param int $tipout tipo utente o array tipi utenti accettati
	 */
	public function __construct($tipout) {
		$this->ut = Utente::crea(NULL, true);
		if (is_null($this->ut)) nologin();
		
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		$tipook = false;
		if (is_array($tipout)) {
			$tu = $this->ut->getTipo();
			foreach ($tipout as $t) {
				if ($tu == $t) {
					$tipook = true;
					break;
				}
			}
		} else {
			$tipook = ($this->ut->getTipo() == $tipout);
		}
		if (!$tipook) {
			homeutente($this->ut);
			exit();
		}
		
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste()) {
			homeutente($this->ut);
			exit();
		}
		if ($this->ut->getTipo() == Utente::SOCIETA && !$this->gara->iscrizioniChiuse()) {
			homeutente($this->ut);
			exit();			
		}
		
		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));
	}
	
	
	/**
	 * @return boolean true se devono essere mostrati gli accorpamenti
	 */
	public function mostraAccorpamenti() {
		if (isset($_GET["orig"])) return false;
		//mostra gli accorpamenti alle società solo se le liste sono pubblicate
		return $this->ut->getTipo() != Utente::SOCIETA || $this->gara->listaPubblicata();
	}
	
	/**
	 * @param Iscritto[] $iscr
	 */
	protected function caricaCategorie($iscr) {
		$mostraAcc = $this->mostraAccorpamenti();
		$idcats = array();
		if (count($iscr) == 0) {
			$this->categorie = array();
		} else {
			foreach ($iscr as $i) {
				/* @var $i Iscritto */
				$idc = $i->getCategoria();
				$ida = $i->getAccorpamento();
				if ($mostraAcc && !is_null($ida)) {
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
					if (!isset($this->catcount[$idc][$i->getPool()])) {
						$this->catcount[$idc][$i->getPool()] = 1;
					} else {
						$this->catcount[$idc][$i->getPool()]++;
					}
				}
			}
			$this->categorie = Categoria::listaGara($this->gara->getChiave(), false, $idcats);
// 			$this->categorie = Categoria::lista($idcats);
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
	 * @return int o int[] se la categoria � divisa in pool
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

	/**
	 * @param int $idc
	 * @param int $pool
	 */
	public function getPropriPartecipanti($idc, $pool) {
		if ($pool < 0) $pool = 0;
		return $this->propricount[$idc][$pool];
	}
	
	/**
	 * @return Iscritto[][][] formato: idcategoria => pool => Iscritto[]
	 */
	protected abstract function getIscrittiPerCat();
	
	public function setPropriPartecipanti($idsoc) {
		foreach ($this->getIscrittiPerCat() as $idc => $poollist) {
			foreach($poollist as $pool => $isclist) {
				$this->propricount[$idc][$pool] = 0;
				foreach($isclist as $i) {
					/* @var $i IscrittoIndividuale */
					if ($i->getSocieta() == $idsoc)
						$this->propricount[$idc][$pool]++;
				}
			}
		}
	}
	
}