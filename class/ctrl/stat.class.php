<?php
if (!defined("_BASEDIR_")) exit();
include_model("Gara", "Utente");
include_controller("VerificaPaginaIndividuale");

class Statistiche {
	const ORO = 1;
	const ARGENTO = 2;
	const BRONZO = 3;
	const QUARTO = 4;
	
	const NERE = 1;
	const COLORATE = 0;
	
	private $gara;
	
	private $atlnum;
	private $indnum;
	private $sqnum;
	private $catnum;
	
	/**
	 * @var int[][][] formato: valore => colorate/nere => tipo gara => int
	 */
	private $medaglie;
	
	/**
	 * @var int[][][] formato: valore => colorate/nere => tipo gara => int
	 */
	private $medaglie_pre;
	
	public function __construct($tipout) {
		$ut = Utente::crea();
		if (is_null($ut)) nologin();
		
		$tipook = false;
		if (is_array($tipout)) {
			$tu = $ut->getTipo();
			foreach ($tipout as $t) {
				if ($tu == $t) {
					$tipook = true;
					break;
				}
			}
		} else {
			$tipook = ($ut->getTipo() == $tipout);
		}
		if (!$tipook) {
			homeutente($ut);
			exit();
		}
		
		if (!isset($_GET["id"])) {
			homeutente($ut);
			exit();
		}
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste()) {
			homeutente($ut);
			exit();
		}
		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));
		
		$totcat = new StatConteggioMedaglie();
		$this->atlnum["tot"] = array();
		$this->atlnum[0][0] = array();
		$this->atlnum[0][1] = array();
		$this->atlnum[1][0] = array();
		$this->atlnum[1][1] = array();
		//calcola dati
		if ($this->gara->isIndividuale()) {
			include_model("IscrittoIndividuale");
			$ind = IscrittoIndividuale::listaGara($this->gara->getChiave());
			$cat = $this->gara->getCategorieIndiv();
			$this->indnum = $this->contaIscr($ind, $cat, true, $totcat);
		}
		if ($this->gara->isSquadre()) {
			include_model("Squadra");
			$sq = Squadra::listaGara($this->gara->getChiave());
			$cat = $this->gara->getCategorieSquadre();
			$this->sqnum = $this->contaIscr($sq, $cat, false, $totcat);
		}
		
		//calcola atleti
		$this->atlnum["tot"] = count($this->atlnum["tot"]);
		foreach ($this->atlnum as $tipo => $att) {
			if ($tipo === "tot") continue;
			$this->atlnum[$tipo]["tot"] = 0;
			foreach ($att as $ag => $atlist) {
				$c = count($atlist);
				$this->atlnum[$tipo][$ag] = $c;
				$this->atlnum[$tipo]["tot"] += $c;
			}
		}
		
		//calcola medaglie
		foreach (array(self::ORO, self::ARGENTO, self::BRONZO, self::QUARTO) as $v) {
			$this->medaglie[$v]["tot"] = 0;
			$this->medaglie[$v][self::COLORATE]["tot"] = 0;
			$this->medaglie[$v][self::NERE]["tot"] = 0;
			
			$this->medaglie_pre[$v]["tot"] = 0;
			$this->medaglie_pre[$v][self::COLORATE]["tot"] = 0;
			$this->medaglie_pre[$v][self::NERE]["tot"] = 0;
		}
		
		foreach ($totcat->getCategorie() as $c) {
			/* @var $c Categoria */
			
 			if($c->isAgonista())
 			{
				foreach ($totcat->getPool($c) as $pool) {
					$num = $totcat->getNum($c, $pool);
					$comp = $totcat->getComponenti($c, $pool);
					$this->addMedaglia(self::ORO, $c, $comp);
					if ($num < 2) continue;
					$this->addMedaglia(self::ARGENTO, $c, $comp);
					if ($num < 3) continue;
					$this->addMedaglia(self::BRONZO, $c, $comp);
					if ($num < 4) continue;
					$this->addMedaglia(self::QUARTO, $c, $comp);
 				}
			}
			else 
			{
				foreach ($totcat->getPool($c) as $pool) {
					$num = $totcat->getNum($c, $pool);
					$comp = $totcat->getComponenti($c, $pool);
					$this->addMedagliaNA(self::ORO, $c, $comp);
					if ($num < 2) continue;
					$this->addMedagliaNA(self::ARGENTO, $c, $comp);
					if ($num < 3) continue;
					$this->addMedagliaNA(self::BRONZO, $c, $comp);
					if ($num < 4) continue;
					$this->addMedagliaNA(self::QUARTO, $c, $comp);
				}
			}
		}
	}
	
	/**
	 * @param int $val medaglia
	 * @param Categoria $c
	 * @param int $comp numero componenti
	 */
	private function addMedaglia($val, $c, $comp) {
		$this->medaglie[$val]["tot"] += $comp;
		if (Cintura::contieneMarroniNere($c->getCinture()))
			$nere = self::NERE;
		else
			$nere = self::COLORATE;
		$tipo = $c->getTipo();
		if (isset($this->medaglie[$val][$nere][$tipo])) {
			$this->medaglie[$val][$nere]["tot"] += $comp;
			$this->medaglie[$val][$nere][$tipo] += $comp;
		} else {
			if (isset($this->medaglie[$val][$nere]))
				$this->medaglie[$val][$nere]["tot"] += $comp;
			else
				$this->medaglie[$val][$nere]["tot"] = $comp;
			$this->medaglie[$val][$nere][$tipo] = $comp;
		}
	}

	
	/**
	 * @param int $val medaglia
	 * @param Categoria $c
	 * @param int $comp numero componenti
	 */
	private function addMedagliaNA($val, $c, $comp) {
		$this->medaglie_pre[$val]["tot"] += $comp;
		if (Cintura::contieneMarroniNere($c->getCinture()))
			$nere = self::NERE;
		else
			$nere = self::COLORATE;
		$tipo = $c->getTipo();
		if (isset($this->medaglie_pre[$val][$nere][$tipo])) {
			$this->medaglie_pre[$val][$nere]["tot"] += $comp;
			$this->medaglie_pre[$val][$nere][$tipo] += $comp;
		} else {
			if (isset($this->medaglie_pre[$val][$nere]))
				$this->medaglie_pre[$val][$nere]["tot"] += $comp;
			else
				$this->medaglie_pre[$val][$nere]["tot"] = $comp;
			$this->medaglie_pre[$val][$nere][$tipo] = $comp;
		}
	}
	
	/**
	 * @param Iscritto[] $isc
	 * @param Categoria[] $cat
	 * @param StatConteggioMedaglie $totcat
	 * @return array
	 */
	private function contaIscr($isc, $cat, $indiv, $totcat) {
		$catisc = array();
		
		//CONTEGGIO ISCRITTI
		$num["tot"] = 0;
		$pool = array();
		foreach ($isc as $i) {
			/* @var $i Iscritto */
			//TODO accorpamenti non pubblicati? tanto � per org e resp
			$idc = $i->getCategoriaFinale(); 
			/* @var $c Categoria */
			$c = $cat[$idc];
			$catisc[$idc][$i->getPool()] = $c;
			if ($c->isAgonista())
				$ag = 1;
			else
				$ag = 0;
			
			//aumenta il totale
			$num["tot"]++;
			if (isset($num[$ag])) {
				//aumenta il gruppo eta'
				$num[$ag]["tot"]++;
				if (isset($num[$ag][$c->getTipo()])) {
					//aumenta il tipo gara
					$num[$ag][$c->getTipo()]++;
				} else {
					//nuovo tipo gara
					$num[$ag][$c->getTipo()] = 1;
				}
			} else {
				//nuovo gruppo eta'
				$num[$ag]["tot"] = 1;
				$num[$ag][$c->getTipo()] = 1;
			}
			
			//conteggio atleti
			if ($indiv) {
				/* @var $i IscrittoIndividuale */
				$this->atlnum["tot"][$i->getAtleta()] = 1;
				$this->atlnum[0][$ag][$i->getAtleta()] = 1;
			} else {
				/* @var $i Squadra */
				foreach ($i->getComponenti() as $cmp) {
					$this->atlnum["tot"][$cmp] = 1;
					$this->atlnum[1][$ag][$cmp] = 1;
				}
			}
			
			//aggiungi il partecipante per le medaglie
			$totcat->addIscritto($c, $i, $indiv);
		}
		
		//CONTEGGIO CATEGORIE
		if ($indiv)
			$ini = 1;
		else
			$ini = 0;
		foreach ($catisc as $pool) {
			//conta la categoria per ogni pool
			foreach ($pool as $c) { 
				if ($c->isAgonista())
					$ag = 1;
				else
					$ag = 0;
					
				//aumenta il totale
				if (isset($this->catnum[$ini]["tot"]))
					$this->catnum[$ini]["tot"]++;
				else
					$this->catnum[$ini]["tot"] = 1;
				if (isset($this->catnum[$ini][$ag])) {
					//aumenta il gruppo eta'
					$this->catnum[$ini][$ag]["tot"]++;
					if (isset($this->catnum[$ini][$ag][$c->getTipo()])) {
						//aumenta il tipo gara
						$this->catnum[$ini][$ag][$c->getTipo()]++;
					} else {
						//nuovo tipo gara
						$this->catnum[$ini][$ag][$c->getTipo()] = 1;
					}
				} else {
					//nuovo gruppo eta'
					$this->catnum[$ini][$ag]["tot"] = 1;
					$this->catnum[$ini][$ag][$c->getTipo()] = 1;
				}
			}
		}
		
		return $num;
	}
	
	/**
	 * @param array $num l'array da dove leggere i valori
	 * @param boolean $ag NULL per il totale, true per gli agonisti, false per i preagonisti
	 * @param int $tipo NULL per totale agonisti/preagonisti, oppure il tipo di gara da contare
	 * @return int
	 */
	private function getConteggioInner($num, $ag, $tipo) {
		if (is_null($ag)) { //totale
			return $num["tot"];
		} else {
			if ($ag) $agi = 1;
			else $agi = 0;
			//se il gruppo non c'�
			if (!isset($num[$agi])) return 0;
			if (is_null($tipo)) { //totale gruppo
				return $num[$agi]["tot"];
			} else {
				//se il tipo non c'�
				if (!isset($num[$agi][$tipo]))
					return 0;
				else //totale tipo
					return $num[$agi][$tipo];
			}
		}
	}
	
	public function getNomeGara() {
		return $this->gara->getNome();
	}
	
	public function isGaraIndividuale() {
		return $this->gara->isIndividuale();
	}
	
	public function isGaraSquadre() {
		return $this->gara->isSquadre();
	}
	
	/**
	 * @param int $tipo 0 = iscritti, 1 = categorie, 2 = atleti
	 * @param boolean $indiv NULL per il totale, true per gli individuali, false per le squadre
	 * @param boolean $ag NULL per il totale, true per gli agonisti, false per i preagonisti
	 * @param int $tipo NULL per totale agonisti/preagonisti, oppure il tipo di gara da contare
	 * @return int
	 */
	public function getConteggio($tn, $indiv=NULL, $ag=NULL, $tipo=NULL) {
		switch($tn) {
			case 0: //iscritti
				if ($indiv === NULL) //conteggio totale
					return $this->getConteggio($tn, true) + $this->getConteggio($tn, false);
				if ($indiv)
					return $this->getConteggioInner($this->indnum, $ag, $tipo);
				else
					return $this->getConteggioInner($this->sqnum, $ag, $tipo);
				break;
			case 1: //categorie
				if ($indiv === NULL) //conteggio totale
					return $this->getConteggio($tn, true) + $this->getConteggio($tn, false);
				if ($indiv) $i=1;
				else $i=0;
				if (isset($this->catnum[$i])) 
					return $this->getConteggioInner($this->catnum[$i], $ag, $tipo);
				else
					return 0;
				break;
			case 2: //atleti
				if ($indiv === NULL) $i=NULL;
				else if ($indiv) $i=0;
				else $i=1;
				return $this->getConteggioInner($this->atlnum, $i, $ag);
				break;
			default:
				return 0;
		}
	}
	
	/**
	 * @return int[] array medaglie con tutti 0
	 */
	private function medaglieZero() {
		return array(self::ORO => 0, self::ARGENTO => 0, self::BRONZO => 0);
	}
	
	/**
	 * @return int[] valori delle medaglie
	 */
	public function valoriMedaglie() {
		return array(self::ORO, self::ARGENTO, self::BRONZO);
	}
	
	/**
	 * @param int $val valore medaglia
	 * @param bool $nere true cinture nere, false cinture colorate, NULL totale
	 * @param int $tipo tipo gara o NULL per il totale
	 * @return int numero di medaglie
	 */
	public function getMedaglie($val, $nere=NULL, $tipo=NULL) {
		if ($nere === NULL)
			return $this->medaglie[$val]["tot"];
		
		if ($nere) $nere = self::NERE; 
		else $nere = self::COLORATE; 
		
		if ($tipo === NULL)
			return $this->medaglie[$val][$nere]["tot"];
		
		if (isset($this->medaglie[$val][$nere][$tipo]))
			return $this->medaglie[$val][$nere][$tipo];
		else
			return 0;
	}
	
	/**
	 * @param int $val valore medaglia
	 * @param bool $nere true cinture nere, false cinture colorate, NULL totale
	 * @param int $tipo tipo gara o NULL per il totale
	 * @return int numero di medaglie
	 */
	public function getMedaglieNA($val, $nere=NULL, $tipo=NULL) {
		if ($nere === NULL)
			return $this->medaglie_pre[$val]["tot"];
	
		if ($nere) $nere = self::NERE;
		else $nere = self::COLORATE;
	
		if ($tipo === NULL)
			return $this->medaglie_pre[$val][$nere]["tot"];
	
		if (isset($this->medaglie_pre[$val][$nere][$tipo]))
			return $this->medaglie_pre[$val][$nere][$tipo];
		else
			return 0;
	}
}

class StatConteggioMedaglie {
	/**
	 * Categorie
	 * @var Categoria[]
	 */
	private $cat;
	/**
	 * Numero iscritti
	 * @var int[][]
	 */
	private $num;
	/**
	 * Numero max componenti
	 * @var int[][]
	 */
	private $comp;
	
	/**
	 * @param Categoria $c
	 * @param Iscritto $i
	 * @param bool $indiv true se è una categoria individuale
	 */
	public function addIscritto($c, $i, $indiv) {
		if ($this->isNuova($c, $i))
			$this->nuovaCat($c, $i, $indiv);
		else
			$this->sommaCat($c, $i, $indiv);
	}
	
	private function isNuova($c, $i) {
		return !isset($this->cat[$c->getChiave()]);
	}
	
	private function nuovaCat($c, $i, $indiv) {
		$id = $c->getChiave();
		$pool = $i->getPool();
		$this->cat[$id] = $c;
		$this->num[$id][$pool] = 1;
		//segna il numero componenti
		if ($indiv)
			$this->comp[$id][$pool] = 1;
		else
			$this->comp[$id][$pool] = count($i->getComponenti());
	}
	
	private function sommaCat($c, $i, $indiv) {
		$tipo = $c->getTipo();
		$id = $c->getChiave();
		$pool = $i->getPool();
		//aggiunge l'iscritto
		$this->num[$id][$pool]++;
		if (!$indiv) {
			//se è una squadra aggiorna il max num componenti
			$nc = count($i->getComponenti());
			$oc = $this->comp[$id][$pool];
			if ($nc > $oc)
				$this->comp[$id][$pool] = $nc;
		}
	}
	
	public function getCategorie() {
		return $this->cat;
	}
	
	public function getPool($c) {
		return array_keys($this->num[$c->getChiave()]);
	}
	
	public function getNum($c, $pool) {
		return $this->num[$c->getChiave()][$pool];
	}
	
	public function getComponenti($c, $pool) {
		return $this->comp[$c->getChiave()][$pool];
	}
}
?>