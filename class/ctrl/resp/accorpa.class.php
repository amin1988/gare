<?php
if (!defined("_BASEDIR_")) exit();
include_model("Responsabile", "Gara", "IscrittoIndividuale", "Squadra", "Categoria");
include_class("Accorpamenti");

class Accorpa {
	/**
	 * @var Responsabile
	 */
	private $ut; 
	/**
	 * @var Gara
	 */
	private $gara;
	
	/**
	 * @var Categoria[][] formato individuali[0|1] => idcategoria => Categoria
	 */
	private $cat;
	/**
	 * @var int[] formato idcategoria => num partecipanti
	 */
	private $npart;
	/**
	 * Elenco di categorie accorpabili per ogni categoria
	 * @var Categoria[][] formato: idcategoria => Categoria[]
	 */
	private $vicine;
	/**
	 * @var int[][] formato: idcategoria destinazione => idcategoria[] sorgenti
	 */
	private $accorpate_d;
	/**
	 * @var int[] formato: idcategoria sorgente => idcategoria destinazione
	 */
	private $accorpate_s;
	/**
	 * @var int[] formato: idcategoria => int
	 */
	private $status;
	/**
	 * @var boolean
	 */
	private $forza_accor;
	
	public function __construct() {
		$this->ut = Responsabile::crea();
		if (is_null($this->ut)) nologin();
		
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste() || $this->gara->passata() || !$this->gara->iscrizioniChiuse()) {
			homeutente($this->ut);
			exit();
		}
		
		if(isset($_SESSION['SuperAccorpa'])) {
			if($_SESSION['SuperAccorpa'])
				$this->forza_accor = true;
			else 
				$this->forza_accor = false;
		}
		else 
			$this->forza_accor = false;
		
		$iscind = IscrittoIndividuale::listaGara($this->gara->getChiave());
		$iscsq = Squadra::listaGara($this->gara->getChiave());
		$tipocat = array();
		$this->npart = array();
		$this->status = array();
		$this->accorpate_d = array();
		$this->accorpate_s = array();
		$this->addCat($iscind, 1, $tipocat);
		$this->addCat($iscsq, 0, $tipocat);
		
// 		$tmpcat = Categoria::lista(array_keys($tipocat));
// 		$this->cat[0] = array();
// 		$this->cat[1] = array();
// 		foreach ($tmpcat as $id => $c) {
// 			$this->cat[$tipocat[$id]][$id] = $c;
// 		}
		
// 		$this->cat[0] = array_intersect_key($this->gara->getCategorieSquadre(), $this->npart);
//  	$this->cat[1] = array_intersect_key($this->gara->getCategorieIndiv(), $this->npart);

		$this->cat = Categoria::listaGara($this->gara->getChiave(), true, array_keys($this->npart));
		
		uasort($this->cat[0], array($this, "compareCat"));
		uasort($this->cat[1], array($this, "compareCat"));
		
		//calcolo accorpabili
		$this->setAccorpabili($this->cat[0]);
		$this->setAccorpabili($this->cat[1]);
		
		if (isset($_POST["pageid"])) {
			$this->salva();
		}
	}
	
	private function addCat($iscritti, $indiv, &$tipocat) {
		foreach ($iscritti as $isc) {
			/* @var $isc Iscritto */
			$idc = $isc->getCategoria();
			$ida = $isc->getAccorpamento();
			if (!is_null($ida)) {
				//$idc è accorpata eliminata, $ida è accorpata principale
				$this->status[$idc] = 1;
				$this->status[$ida] = 2;
				$this->accorpate_d[$ida][$idc] = $idc;
				$this->accorpate_s[$idc] = $ida;
			} else if ($isc->isSeparato()) {
				//separata
				$this->status[$idc] = 3;
			} else {
				//categoria pura
				if (!isset($this->status[$idc]))
					$this->status[$idc] = 0;
			}
			if (isset($this->npart[$idc]))
				$this->npart[$idc] = $this->npart[$idc]+1;
			else {
				$this->npart[$idc] = 1;
				$tipocat[$idc] = $indiv;
			}
		}
	}
	
	/**
	 * @param Categoria $ca
	 * @param Categoria $cb
	 */
	private function compareCat($ca, $cb) {
		$diff = $this->npart[$ca->getChiave()] - $this->npart[$cb->getChiave()];
		if ($diff == 0)
			return Categoria::compare($ca, $cb);
		else
			return $diff;
	}
	
	/**
	 * @param Categoria $cat
	 */
	private function setAccorpabili($lista) {
		foreach ($lista as $idc => $cat) {
			/* @var $cat Categoria */
			$this->vicine[$idc] = Accorpamenti::ordina($cat, $lista,$this->forza_accor);
		}
	}
	
	/**
	 * @return Gara
	 */
	public function getGara() {
		return $this->gara;
	}

	/**
	 * @return Categoria[] formato idcategoria => Categoria
	 */
	public function getCategorie() {
		return $this->cat[0]+$this->cat[1];
	}
	
	/**
	 * @return Categoria[] formato idcategoria => Categoria
	 */
	public function getCategorieIndividuali() {
		return $this->cat[1];
	}
	
	/**
	 * @return Categoria[] formato idcategoria => Categoria
	 */
	public function getCategorieSquadre() {
		return $this->cat[0];
	}
	
	/**
	 * @param int $catid
	 * @return int
	 */
	public function getNumPartecipanti($catid) {
		return $this->npart[$catid];
	}
	
	/**
	 * @param int $catid
	 * @return int
	 */
	public function getNumTotale($catid) {
		$tot = $this->npart[$catid];
		foreach ($this->accorpate_d[$catid] as $ida) {
			$tot += $this->npart[$ida];
		}
		return $tot;
	}
	
	/**
	 * @param int $catid
	 * @return Categoria[]
	 */
	public function getAccorpabili($catid) {
		return $this->vicine[$catid];
	}
	
	/**
	 * @param int $catid
	 * @return boolean
	 */
	public function puoAccorpare($catid) {
		return count($this->vicine[$catid]) > 0;
	}
	
	/**
	 * @param int $catid
	 * @return boolean
	 */
	public function puoSeparare($catid) {
		return $this->npart[$catid] > 16;
	}
	
	/**
	 * @param int $catid
	 * @return int
	 */
	public function getStatus($catid) {
		return $this->status[$catid];
	}
	
	/**
	 * @param int $catid
	 * @return int
	 */
	public function isSeparata($catid) {
		return $this->status[$catid] == 3; //TODO usata da qualche parte?
	}
	
	/**
	 * @param int $catid
	 * @return int[]
	 */
	public function getAccorpateSrc($catid) {
		if (isset($this->accorpate_d[$catid]))
			return $this->accorpate_d[$catid];
		else
			return array();
	}
	
	/**
	 * @param int $catid
	 * @return string
	 */
	public function getAccorpataDest($catid) {
		if (isset($this->accorpate_s[$catid]))
			return $this->accorpate_s[$catid];
		else
			return "";
	}
	
	private function salva() {
		//calcola categorie individuali o squadre
		foreach ($this->cat as $tipo => $carr) {
			foreach ($carr as $id => $c) {
				$indiv[$id] = $tipo; 
			}
		}
		$idg = $this->gara->getChiave();
		$salva = isset($_POST["salva"]);
		
		//pubblicazione
		if ($this->gara->listaPubblicata()) {
			if (isset($_POST["nopubbl"])) {
				$this->gara->setListaPubblicata(false);
				$this->gara->salva();
			}
		} else {
			if ((isset($_POST["pubbl"]) || isset($_POST["salva_pubbl"]))) {
				$this->gara->setListaPubblicata(true);
				$this->gara->salva();
				if (isset($_POST["salva_pubbl"])) 
					$salva = true;
			}
		}
		
		if (!$salva) return;
		
		//accorpamenti
		if (isset($_POST["accorpa"])) {
			foreach ($_POST["accorpa"] as $id=>$val) {
				$do = false;
				if ($this->status[$id] != 1 && trim($val) != "" && is_numeric($val)) {
					//nuovo accorpamento
					$do = true;
				} else if ($this->status[$id] == 1 && $val != $this->accorpate_s[$id] && is_numeric($val)) {
					//accorpamento modificato
					$do = true;
				} else if ($this->status[$id] == 1 && trim($val) == "") {
					//accorpamento annullato
					$do = true;
					$val = NULL;
				}
				if ($do) {
					if ($indiv[$id] == 1)
						IscrittoIndividuale::accorpa($idg, $id, $val);
					else
						Squadra::accorpa($idg, $id, $val);
				}
			}
		}
		
		//separazioni
		if (isset($_POST["separa"])) {
			foreach ($_POST["separa"] as $id=>$val) {
				if ($this->status[$id] != 3 && $val == 1) {
					//separa
					$numcat = intval(ceil($this->npart[$id] / 16.0));
					$isc = $this->getIscritti($id, $indiv[$id]);
					//raggruppa per societa
					$soc = array();
					foreach ($isc as $i) {
						/* @var $i Iscritto */
						$soc[$i->getSocieta()][$i->getChiave()] = $i;
					}
					shuffle($soc);
					$count=0;
					foreach ($soc as $si) {
						shuffle($si);
						foreach ($si as $i) {
							/* @var $i Iscritto */
							$i->setPool(($count % $numcat)+1);
							$i->salva();
							$count++;
						}
					}
					//TODO raggruppa per societa e assegna pool
				} else if ($this->status[$id] == 3 && $val == 0) {
					//riunisci
					$isc = $this->getIscritti($id, $indiv[$id]);
					foreach ($isc as $i) {
						/* @var $i Iscritto */
						$i->setPool(0);
						$i->salva();
					}
				}
			}
		}
		
		//sistema status e accorpamenti interni
		$this->status = array();
		$this->accorpate_d = array();
		$this->accorpate_s = array();
		foreach ($this->getCategorie() as $c) {
			$id = $c->getChiave();
			if (isset($_POST["separa"][$id]) && $_POST["separa"][$id] == 1) {
				//separata
				$this->status[$id] = 3;
			} else if (isset($_POST["accorpa"][$id])) {
				$idd = trim($_POST["accorpa"][$id]);
				if ($idd != "" && is_numeric($idd)) {
					//accorpata eliminata
					$this->status[$id] = 1;
					$this->status[$idd] = 2;
					$this->accorpate_s[$id] = $idd;
					$this->accorpate_d[$idd][$id] = $id;
				} else {
					//pura o accorpata principale
					if (!isset($this->status[$id]))
						$this->status[$id] = 0;
				}
			} else {
				if (!isset($this->status[$id]))
						$this->status[$id] = 0;
			}
		}
	}
	
	/**
	 * @param int $idc
	 * @param int $indiv 1 individuale, 0 squadre
	 * @return Iscritto[]
	 */
	private function getIscritti($idc, $indiv) {
		if ($indiv == 1)
			return IscrittoIndividuale::listaGara($this->gara->getChiave(), NULL, $idc);
		else
			return Squadra::listaGara($this->gara->getChiave(), $idc);
	}
}

?>