<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente", "Gara", "Societa");
include_class("Sesso");
include_controller("VerificaPaginaIndividuale");

class RiepilogoSocietaSocEst {
	
	private $ut;
	/**
	 *  @var Gara
	 */
	private $gara;
	
	/**
	 *  @var int[][] formato: idsocieta => [individuale:0/squadre:1/componenti squadre:3] => int
	 */
	private $soccount;
	
	/**
	 * @var int[] formato idsocieta => int
	 */
	private $prezzo;

	/**
	 * @var IscrittoIndividuale[][][] formato idsocieta => idatleta => idiscritto => IscrittoIndividuale
	 */
	private $individuali = array();
	/**
	 * @var Squadra[][] formato idsocieta => idsquadra => Squadra
	 */
	private $squadre = array();
	
	/**
	 * @var Atleta[][] formato: idsocieta => idatleta => Atleta
	 */
	private $atleti;
	/**
	 * @var Persona[][] formato idsocieta => idpersona => Persona
	 */
	private $coach;
	/**
	 * 
	 * @var Persona[][] formato idsocieta => idpersona => Persona
	 */
	private $arbitri;
	/**
	 * @var Societa[] nomi delle societa
	 */
	private $soc;
	
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
		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));
		
		$idsoc = array();
		$atl = array();
		
		if ($this->gara->isIndividuale()) {
			include_model("IscrittoIndividuale");
			$isc = IscrittoIndividuale::listaGara($this->gara->getChiave());
			$pr = $this->gara->getPrezzoIndividuale();
			foreach ($isc as $idi => $i) {
				/* @var $i IscrittoIndividuale */
				$ids = $i->getSocieta();
				if (!isset($idsoc[$ids])) 
					$this->addSocieta($ids, $idsoc);
				$ida = $i->getAtleta();
				$atl[$ids][$ida] = $ida;
				$this->soccount[$ids][1]++;
				$this->prezzo[$ids] += $pr;
				$this->individuali[$ids][$ida][$idi] = $i;
			}
		}
		if ($this->gara->isSquadre()) {
			include_model("Squadra");
			$sqlist = Squadra::listaGara($this->gara->getChiave());
			$pr = $this->gara->getPrezzoSquadra();
			foreach ($sqlist as $idsq => $sq) {
				/* @var $sq Squadra */
				$ids = $sq->getSocieta();
				if (!isset($idsoc[$ids]))
					$this->addSocieta($ids, $idsoc);
				foreach ($sq->getComponenti() as $ida)
					$atl[$ids][$ida] = $ida;
				$this->soccount[$ids][0]++;
				$this->prezzo[$ids] += $pr;
				$this->squadre[$ids][$idsq] = $sq;
			}
			foreach ($this->squadre as $ids=>$sqsoc) {
				usort($this->squadre[$ids],array($this, "compareSq"));
			}
		}
		
		$coach = Coach::lista($this->gara->getChiave());
		
		$this->soc = Societa::lista($idsoc,"nome");
		if ($this->getPagamentoCoach())
			$prc = $this->gara->getPrezzoCoach();
		else
			$prc = 0;
		$contatto_nome = $this->ut->getContatto();
		foreach ($this->soc as $ids => $s) {
			/* @var $s Societa */
			$tec = array();
			$catl = array();
			$nome_fed = strtolower($s->getFedEst());
			if ( $contatto_nome != $nome_fed)
			{
			      
			      
			       
			        unset($this->soc[$ids]);
			        continue;
			}
			//tecnici
			if (isset($coach[$ids])) {
				foreach ($coach[$ids] as $c) {
					/* @var $c Coach */
					$idp = $c->getPersona();
					switch ($c->getTipo()) {
						case Persona::TIPO_TECNICO:
							$tec[$idp] = $idp;
							break;
						case Persona::TIPO_ATLETA:
							$atl[$ids][$idp] = $idp;
							$catl[] = $idp;
							break;
					}
				}
			}
			$this->atleti[$ids] = $s->getAtleti($atl[$ids]);
// 			if (count($tec) > 0)
// 				$this->coach[$ids] = $s->getTecnici($tec);
// 			foreach ($catl as $ida) {
// 				$this->coach[$ids][] = $this->atleti[$ids][$ida];
// 			}
			if (isset($coach[$ids])) {
				$this->coach[$ids] = $s->getCoach($coach[$ids]);
				if ($prc > 0) $this->prezzo[$ids] += $prc * count($this->coach[$ids]);
			} else
				$this->coach[$ids] = array();
		}
		
		$ar_arb = Arbitro::lista($this->gara->getChiave(),NULL,1);
		foreach($ar_arb as $arb_soc)
		{
			foreach($arb_soc as $arb)
			{
				$id_soc = $arb->getSocieta();
			}
			
			$s_arb = Societa::fromId($id_soc);
			
			$arb_soc = Arbitro::lista($this->gara->getChiave(),$id_soc,1);
			
			$rimb = $this->gara->getRimborsoArb();
			$n_a = count($arb_soc);
			
			
			if(!isset($idsoc[$id_soc]))
			{
 				//la societ� ha solo arbitri
				$this->soc[$id_soc] = $s_arb;
				$this->addSocieta($id_soc, $idsoc);
				$this->atleti[$id_soc] = array();
				$this->coach[$id_soc] = array();
				$this->prezzo[$id_soc] = 0;
			}
			
			$this->prezzo[$id_soc] -= $rimb*$n_a;
			$this->arbitri[$id_soc] = $s_arb->getArbitri($arb_soc);
		}
	}
	
	private function addSocieta($ids, &$idsoc) {
		$idsoc[$ids] = $ids;
		$this->soccount[$ids][0] = 0;
		$this->soccount[$ids][1] = 0;
		$this->soccount[$ids][3] = 0;
		$this->prezzo[$ids] = 0;
		$this->individuali[$ids] = array();
		$this->squadre[$ids] = array();
	}
	
	/**
	 * @param Squadra $a
	 * @param Squadra $b
	 */
	private function compareSq($a, $b) {
		$na = $a->getNumero();
		$nb = $b->getNumero();
		if ($na > $nb) return 1;
		if ($na < $nb) return -1;
		return 0;
	}
	
	public function haIscritti() {
		return count($this->soc) > 0;
	}
	
	/**
	 * @return Gara
	 */
	public function getGara() {
		return $this->gara;
	}
	
	public function getPagamentoCoach() {
		return $this->gara->getPagamentoCoach();
	}
	
	public function isIndividuale() {
		return $this->gara->isIndividuale();
	}
	
	public function isSquadre() {
		return $this->gara->isSquadre();
	}
	
	public function usaPeso() {
		$this->gara->usaPeso();
	}
	
	/**
	 * @return Societa[]
	 */
	public function getSocieta() {
		return $this->soc;
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function getNumIndividuali($id) {
		return $this->soccount[$id][1];
	}
	
	/**
	 * @param int $id
	 * @return int
	 */
	public function getNumAtletiIndividuali($id) {
		return count($this->individuali[$id]);
	}

	/**
	 * @param int $id
	 * @return int
	 */
	public function getNumSquadre($id) {
		return $this->soccount[$id][0];
	}

	/**
	 * @param int $id
	 * @return int
	 */
	public function getNumCoach($id) {
		if (isset($this->coach[$id]) && is_array($this->coach[$id]))
			return count($this->coach[$id]);
		else
			return 0;
	}
	
	public function getPrezzo($id) {
		return $this->prezzo[$id];
	} 
	
	/**
	 * @param int $ids
	 * @return Atleta[]
	 */
	public function getAtleti($ids) {
		return $this->atleti[$ids];
	}
	
	/**
	 * @param int $ids
	 * @return IscrittoIndividuale[][] formato idatleta => IscrittoIndividuale[]
	 */
	public function getIndividuali($ids) {
		return $this->individuali[$ids];
	}
	
	/**
	 * @param int $ids
	 * @return Squadra[]
	 */
	public function getSquadre($ids) {
		return $this->squadre[$ids];
	}
	
	/**
	 * @param int $ids
	 * @return boolean
	 */
	public function haCoach($ids) {
		return isset($this->coach[$ids]) && count($this->coach[$ids]) > 0;
	}
	
	/**
	 * @param int $ids
	 * @return Persona[]
	 */
	public function getCoach($ids) {
		return $this->coach[$ids];
	}
	
	public function haArbitri($ids) {
		return isset($this->arbitri[$ids]) && count($this->arbitri[$ids]) > 0;
	}
	
	public function getArbitri($ids) {
		return $this->arbitri[$ids];
	}
	
	public function getNumArbitri($ids) {
		if(isset($this->arbitri[$ids])  && is_array($this->arbitri[$ids]))
			return count($this->arbitri[$ids]);
		
		else return 0;
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
					if (!isset($this->catcount[$idc][$i->getPool()])) {
						$this->catcount[$idc][$i->getPool()] = 1;
					} else {
						$this->catcount[$idc][$i->getPool()]++;
					}
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
	
}