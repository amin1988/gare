<?php
if (!defined("_BASEDIR_")) exit();
include_model("UtSocieta", "Gara", "Cintura", "Stile", "Squadra", "Prestito");
include_errori("VerificaSquadra");

class ModificaSquadre {
	/** @var UtSocieta */
	private $ut;
	/** @var Gara */
	private $gara;
	
	/**
	 * Tipi di gara possibili
	 * @var int[]
	 */
	private $tipigara;
	
	/** 
	 * Tipo di gara della squadra
	 * @var int
	 */
	private $tipo;
	
	/**
	 * @var Squadra
	 */
	private $sq;
	/**
	 * Componenti della squadra
	 * @var Atleta[]
	 */
	private $comp;
	
	/**
	 * Atleti che possono partecipare 
	 * @var Atleta[] 
	 */
	protected $atok;
	
	/**
	 * @var Prestito
	 */
	protected $prestito = NULL;
	
	/**
	 * @var int[] formato idatleta => idsocieta
	 */
	protected $pres = NULL;
	
	/**
	 * Atleti prestati ad altre societ�
	 * @var int[]
	 */
	private $prestati;
	
	/**
	 * i tipi di gara a cui pu� partecipare l'atleta. formato idatleta => tipo[]
	 * @var int[][]
	 */
	protected $tipi;
	
	/**
	 * @var VerificaSquadra
	 */
	private $errori;
	
	private $multiCat;
	
	public function __construct() {
		$this->ut = UtSocieta::crea();
		if (is_null($this->ut)) nologin();
		
		if (isset($_GET["ids"])) {
			//modifica squadra
			$this->sq = new Squadra($_GET["ids"]);
			if (!$this->sq->esiste() || $this->sq->getSocieta() != $this->ut->getIdSocieta()) {
				homeutente($this->ut);
				exit();
			}
			$this->gara = new Gara($this->sq->getGara());
		} else if (isset($_GET["idg"])) {
			//nuova squadra
			$this->sq = NULL;
			$this->gara = new Gara($_GET["idg"]);
		} else { //nessun id in GET
			homeutente($this->ut);
			exit();
		}
		
		if (!$this->gara->esiste() || $this->gara->iscrizioniChiuse()) {
			if ($_SESSION["backdoor"] != "aprigara") {
				homeutente($this->ut);
				exit();
			}
		}
		
		if (!$this->gara->isSquadre()) {
			redirect("soc/iscrivi.php?id=".$this->gara->getChiave());
			exit();
		}
		
		//controllo zone
		$zonaut  = $this->ut->getSocieta()->getZona();
		$zonegara = $this->gara->getZone();
		$trovata = false;
		while(!is_null($zonaut)) {
			if (in_array($zonaut, $this->gara->getZone())) {
				$trovata = true;
				break;
			}
			$zonaut = Zona::getZona($zonaut)->getPadre();
		}
		if (!$trovata) {
			homeutente($this->ut);
			exit();
		}
		
		//lettura atleti
		$atl = $this->ut->getSocieta()->getAtleti();
		$this->atok = array();
		$altre = Squadra::altreIscrizioni($this->gara->getChiave(), $this->ut->getIdSocieta(), $this->sq);
		foreach ($atl as $a) {
			$tipi = $this->gara->puoPartecipareSquadre($a, true);
			if (isset($altre[$a->getChiave()])) {
				foreach ($altre[$a->getChiave()] as $tipo)
					unset($tipi[$tipo]);
			}
			if (count($tipi) > 0) {
				$this->atok[$a->getChiave()] = $a;
				$this->tipi[$a->getChiave()] = array_keys($tipi);
			}
		}
		
		$cat = $this->gara->getCategorieSquadre();
		if (count($cat) == 0) {
			//gara non a squadre
			redirect("soc/iscrivi.php?id=".$this->gara->getChiave());
			exit();
		}
		foreach ($cat as $c) {
			/* @var $c Categoria */
			$tipig[$c->getTipo()] = $c->getTipo();
		}
		//TODO generalizzare
		$this->tipigara = array_intersect_key(array(0=>"Kata", 1=>"Sanbon", 2=>"Ippon"), $tipig);
		
		if (isset($_POST["pres"])) {
			foreach ($_POST["pres"] as $idp) {
				$this->pres[$idp] = $_POST["pres_soc"][$idp];
			}
		}
		
		if (!is_null($this->sq)) {
			$this->tipo = $cat[$this->sq->getCategoria()]->getTipo();
			$_POST["tipo"] = $this->tipo;
			if (isset($_POST["comp"])) 
				$lista = $_POST["comp"];
			else 
				$lista = $this->sq->getComponenti();
			$this->prestito = Prestito::squadra($this->sq->getChiave());
			if (!is_null($this->prestito)) {
				$idap = $this->prestito->getAtleta();
				if (!isset($_POST["pageid"]) || isset($_POST["pres"][$idap])) {
					$this->pres[$idap] = $this->prestito->getOrigine();
					unset($lista[array_search($idap,$lista)]);
				}
			}
			foreach ($lista as $idc) {
				$this->comp[$idc] = $this->atok[$idc];
			}
		} else {
			if (isset($_POST["tipo"]))
				$this->tipo = $_POST["tipo"];
			else
				$this->tipo = NULL;
			$this->comp = array();
			if (isset($_POST["comp"])) {
				foreach ($_POST["comp"] as $idc)
					$this->comp[$idc] = $this->atok[$idc];
			}
		}
		if (!is_null($this->pres) && count($this->pres) > 0) {
			$ap = AtletaAffiliato::atletiSocietaAmpia(
					$this->ut->getIdSocieta(), array_keys($this->pres));
			foreach ($ap as $ida => $a) {
				$this->comp[$ida] = $a;
			}
		}
		
		$usciti = Prestito::usciti($this->gara->getChiave(), $this->ut->getIdSocieta());
		foreach ($usciti as $u) {
			/* @var $u Prestito */
			$ida = $u->getAtleta();
			$tipo = $u->getTipo();
			$this->prestati[$ida][$tipo] = $tipo;
		}
		
		if ($this->sq === NULL)
			$idsq = NULL;
		else
			$idsq = $this->sq->getChiave();
		$this->errori = new VerificaSquadra($this->gara->getChiave(), $idsq);
		if (!$this->errori->haErrori())
			$this->salvaSquadra();
	}
	
	/**
	 * @return VerificaSquadra
	 */
	public function getErrori() {
		return $this->errori;
	}
	
	/**
	 * @return Squadra
	 */
	public function  getSquadra() {
		return $this->sq;
	}
	
	/**
	 * @return Gara
	 */
	public function getGara() {
		return $this->gara;
	}
	
	public function getIdGara() {
		return $this->gara->getChiave();
	}
	
	public function getIdSocieta() {
		return $this->ut->getIdSocieta();
	}
		
	public function isNuova() {
		return is_null($this->sq);
	}
	
	public function haDati() {
		return !is_null($this->sq) || isset($_POST["tipo"]);
	}
	
	/**
	 * @return Atleta[]
	 */
	public function getComponenti() {
		return $this->comp;
	}
	
	/**
	 * @param int $ida
	 * @return boolean
	 */
	public function isComponente($ida) {
		return isset($this->comp[$ida]);
	}
	
	/**
	 * @param int $ida
	 * @return boolean
	 */
	public function isPrestito($ida) {
		return isset($this->pres[$ida]);
	}
	
	/**
	 * @param int $ida
	 * @return boolean
	 */
	public function isUscito($ida) {
		return isset($this->prestati[$ida]);
	}
	
	/**
	 * Restituisce i tipi per cui l'atleta è stato prestato
	 * @param int $ida
	 * @return int[] i tipi o NULL se non è stato prestato
	 */
	public function tipiUsciti($ida) {
		if (isset($this->prestati[$ida]))
			return $this->prestati[$ida];
		else
			return NULL;
	}
	
	/**
	 * @param int $ida
	 * @return int
	 */
	public function getSocietaPrestito($ida) {
		return $this->pres[$ida];
	}
	
	/**
	 * Restituisce gli atleti che possono partecipare alla gara
	 * @return Atleta[]
	 */
	public function getAtletiOk() {
		return $this->atok;
	}
	
	public function getStili() {
		return Stile::listaStili();
	}
	
	public function getTipiGara() {
		return $this->tipigara;
	}
	
	public function getTipo() {
		if (!is_null($this->sq)) return $this->tipo;
		if (isset($_POST["tipo"])) return $_POST["tipo"];
		return NULL;
	}
	
	/**
	 * Indica se un atleta pu� iscriversi ad un certo tipo di gara
	 * @param Atleta $atleta
	 * @param int $tipo
	 */
	public function tipoGaraOk($atleta, $tipo) {
		if (isset($this->tipi[$atleta->getChiave()]))
			return in_array($tipo, $this->tipi[$atleta->getChiave()]);
		return false;
	}
	
	/**
	 * @param Atleta $a
	 * @return string
	 */
	public function getCinturaComponente($a) {
		$idc = $a->getCintura();
		if (!is_null($this->sq)) {
			$idcsq = $this->sq->getCinturaComponente($a->getChiave());
			if (!is_null($idcsq))
				$idc = $idcsq;
		}
		return Cintura::getCintura($idc)->getNome();
	}
	
	public function getNomeCintura($id) {
		return Cintura::getCintura($id)->getNome();
	}
		
	public function cintureFisse() {
		return $this->ut->getSocieta()->isAffiliata();
	}
	
	private function salvaSquadra() {
		//TODO controllare se componenti verificati
		if (!isset($_POST["pageid"])) return; //chiamata non effettuata
		
		$nuovopres = false;
		$delpres = false;
		$pres = NULL;
		if (is_null($this->sq)) {
			//nuova squadra
			$dati["tipo"] = $_POST["tipo"];
			$dati["societa"] = $this->ut->getIdSocieta();
			if (isset($_POST["categoria"]))
				$dati["categoria"] = $_POST["categoria"];
			foreach ($_POST["comp"] as $idc) {
				$a = $this->atok[$idc];
				$dati["comp"][$idc] = $a;
				$dati["cinture"][$idc] = $a->getCintura();
			}
			$pres = $this->creaNuovoPrestito();
			$nuovopres = !is_null($pres);
			if ($nuovopres) {
				$dati["comp"][$pres->getChiave()] = $pres;
				$dati["cinture"][$pres->getChiave()] = $pres->getCintura();
			}
			$sq = Squadra::nuovo($this->gara, $dati);
		} else {
			//modifica squadra
			$sq = $this->sq;
			foreach ($_POST["comp"] as $idc) {
				$a = $this->atok[$idc];
				$comp[] = $a;
				$cin = $sq->getCinturaComponente($idc);
				if (is_null($cin)) $cin = $a->getCintura();
				$idcomp[] = $idc;
				$cinture[$idc] = $cin;
			}
// 			$idcomp = $_POST["comp"];
				
			//controllo eliminazione prestiti
			if (!is_null($this->prestito)) 
				$delpres = !isset($_POST["pres"][$this->prestito->getAtleta()]);
			//controllo nuovo prestito
			$pres = $this->creaNuovoPrestito();
			$nuovopres = !is_null($pres);
			if ($nuovopres) {
				$idcomp[] = $pres->getChiave();
				$cinture[$pres->getChiave()] = $pres->getCintura();
				$comp[] = $pres;
			}
			
			$sq->setComponenti($idcomp, $cinture);
			if (isset($_POST["categoria"]))
				$cat = $_POST["categoria"];
			else $cat = -1;
			$sq->calcolaCategoria($this->gara, $comp, $this->tipo, $cat);
		}
		$this->errori->checkCategoria($sq); 
		
		if (!$this->errori->haErrori()) {
			$sq->salva();
			if ($delpres) {
				$this->prestito->elimina();
				$this->prestito = NULL;
			}
			if ($nuovopres) {
				$this->prestito = Prestito::crea($sq, $pres);
				$this->prestito->salva();
			}
			redirect("soc/iscrivisq.php?id=".$this->gara->getChiave());
			exit();
		} else {
			$this->multiCat = $sq->getMultiCategorie();
			if ($delpres)
				unset($this->comp[$this->prestito->getChiave()]);
		}
		
	}
	
	/**
	 * @return NULL|Atleta
	 */
	private function creaNuovoPrestito() {
		if (!isset($_POST["pres"])) return NULL;
		
		reset($_POST["pres"]);
		$idpres = current($_POST["pres"]);
		$idsp = $_POST["pres_soc"][$idpres];
		$s = new Societa($idsp);
		return $s->getAtleta($idpres);
	}
	
	/**
	 * @return Categoria[]
	 */
	public function getMultiCategorie() {
		return $this->multiCat;
	}
}