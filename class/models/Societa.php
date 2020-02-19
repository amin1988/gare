<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello", "Zona", "Gara","CoachEsterno","ArbitroEsterno");

/**
 * @access public
 * @package models
 */
class Societa extends Modello {
	/**
	 * @var Atleta[]
	 */
	private $atleti = array();
	private $atl_completi=false;
	
	private $includeAtl = false;

	public static function listaAffiliate($ordine=NULL) {
		$where = "idaffiliata IS NOT NULL";
		if (!is_null($ordine)) {
			$where .= " ORDER BY $ordine";
		}
		return self::listaInner($where);
	}
	
	public static function listaEsterne($ordine=NULL) {
		$where = "idaffiliata IS NULL";
		if (!is_null($ordine)) {
			$where .= " ORDER BY $ordine";
		}
		return self::listaInner($where);
	}
	
	/**
	 * Indica se la società affiliata è già presente nel database
	 * @param int $idaffiliata
	 * @return boolean 
	 */
	public static function isAffiliataInserita($idaffiliata) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr  = $conn->select("societa", "idaffiliata = '$idaffiliata'");
		
		return !is_null($mr->fetch_row());
	}
	
	/**
	 * @param int[] $lista
	 * @param string $ordine
	 * @return Societa[]
	 */
	public static function lista($lista=NULL,$ordine=NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($lista))
			$where = "1";
		else if (count($lista) == 0)
			return array();
		else 
			$where = "idsocieta IN ".$conn->flatArray($lista);
		
		if (!is_null($ordine)) {
			$where .= " ORDER BY $ordine";
		}
		
		return self::listaInner($where, $conn);
	}
	
	public static function elencoWKC() {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$where = "wkc = 1";
		
		return self::listaInner($where,$conn);
	} 
		
	/**
	 * @param string $where
	 * @param Connessione $conn
	 * @return Societa[] 
	 */
	private static function listaInner($where, $conn=NULL) {
		if (is_null($conn)) {
			$conn = $GLOBALS["connint"];
			$conn->connetti();
		}
		$mr = $conn->select("societa",$where);
		$soc = array();
		while($row = $mr->fetch_assoc()) {
			$s = new Societa();
			$s->carica($row);
			$soc[$s->getChiave()] = $s;
		}
		return $soc;
	}
	
	/**
	 * @param array $dati valori: nome, nomebreve, zona, stile, [affiliata]
	 * @return Societa
	 */
	public static function nuovo($dati) {
		$s = new Societa();
		$s->set("nome",$dati["nome"]);
		$s->set("nomebreve",$dati["nomebreve"]);
		$s->setZona($dati["zona"]);
		$s->setStile($dati["stile"]);
		if (isset($dati["affiliata"])) {
			$s->set("idaffiliata", $dati["affiliata"]);
		}
		return $s;
	}
	
	public static function idFromidAff($id_aff)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$mr = $conn->select("societa","idaffiliata=$id_aff","idsocieta");
		
		if(!is_null($mr))
		{
			$row = $mr->fetch_assoc();
			return $row['idsocieta'];
		}
		
		return NULL;
	}
	
	public static function idAffFromId($id_s)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$mr = $conn->select("societa","idsocieta=$id_s","idaffiliata");
		
		if(!is_null($mr))
		{
			$row = $mr->fetch_assoc();
			return $row['idaffiliata'];
		}
		
		return NULL;
	}
	
	public static function nomeFromidAff($id_aff)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
	
		$mr = $conn->select("societa","idaffiliata=$id_aff","nomebreve");
	
		if(!is_null($mr))
		{
			$row = $mr->fetch_assoc();
			return $row['nomebreve'];
		}
	
		return NULL;
	}
	
	public static function isPresenteNomeBreve($nome)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		if(_WKC_MODE_)
			$wkc = 1;
		else
			$wkc = 0;
		
		$mr = $conn->select("societa","nomebreve LIKE '%$nome%' AND wkc='$wkc'");
		$num = 0;
		
		while($row = $mr->fetch_assoc())
		{
			$num++;
		}
		
		return $num != 0;
	}
	
	/**
	 * Restituisce la società con l'id passato se esiste, altrimenti NULL
	 * @param int $id
	 * @return Societa
	 */
	public static function fromId($id)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$mr = $conn->select("societa","idsocieta='$id'");
		
		$row = $mr->fetch_assoc();
		if($row !== NULL)
		{
			$s = new Societa();
			$s->carica($row);
			
			return $s;
		}
		
		return NULL;
	}
	
	public function __construct($id = NULL) {
		parent::__construct("societa", "idsocieta", $id);
	}
	
	/**
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	public function setNome($valore) {
		$this->set("nome", $valore);
	}
	
	/**
	 * @return string
	 */
	public function getNomeBreve() {
		return $this->get("nomebreve");
	}
	
	public function setNomeBreve($valore) {
		$this->set("nomebreve", $valore);
	}
	
	public function getIdAffiliata() {
		return $this->get("idaffiliata");
	}
	
	private function includeAtleti() {
		if ($this->isAffiliata()) {
			include_esterni("AtletaAffiliato");
			include_esterni("TecnicoAffiliato");
			include_esterni("TesseratoFiam");
		} else {
			include_model("AtletaEsterno");
		}
		$this->includeAtl = true;
	}
	
	/**
	 * @param $lista int[] id degli atleti da caricare
	 * @param $nere true per caricare solo le cinture nere
	 * @return Atleta[] formato idatleta => Atleta
	 */
	public function getAtleti($lista=NULL, $nere=false) {
		if ($this->atl_completi && is_null($lista)) return $this->atleti;
	
		if (!$this->includeAtl) $this->includeAtleti();
	
		if ($this->isAffiliata()) {
			$newatl = AtletaAffiliato::atletiSocieta($this->getChiave(),
					$this->get("idaffiliata"),$lista,$nere);
		} else {
			$newatl = AtletaEsterno::atletiSocieta($this->getChiave(),$lista,$nere);
		}
	
		if (is_null($lista)) {
			$this->atl_completi = true;
			$this->atleti = $newatl;
		}
		return $newatl;
	}
	
	public function getTecnici($lista=NULL) {
		if (!$this->includeAtl) $this->includeAtleti();
		
		if ($this->isAffiliata()) {
			return TecnicoAffiliato::tecniciSocieta($this->getChiave(),
					$this->get("idaffiliata"), $lista);
		} else {
			return array(); //TODO tecnici per esterni
		}
	}
	
	public function getAltriCoach($lista = NULL) {		
		if (!$this->includeAtl) $this->includeAtleti();
		
		if ($this->isAffiliata()) {
			if (is_null($lista) || count($lista) == 0) 
				return TesseratoFiam::getAltriTesserati($this->getChiave(),
						$this->get("idaffiliata"));
			else
				return TesseratoFiam::lista($this->getChiave(), $lista);
		} else {
			return array(); //TODO coach per esterni
		}
	}
	
	/**
	 * @param Coach[] $coach
	 * @return Persona[]
	 */
	public function getCoach($coach) {
// 		$sep = array();
// 		foreach ($coach as $c) {
// 			/* @var $c Coach */
// 			$sep[$c->getTipo()][] = $c->getPersona();
// 		}
// 		$ret = array();
// 		if (isset($sep[Persona::TIPO_TECNICO])) {
// 			$ret = $this->getTecnici($sep[Persona::TIPO_TECNICO]);
// 		}
		
// 		if (isset($sep[Persona::TIPO_ATLETA])) {
// 			$ret += $this->getAtleti($sep[Persona::TIPO_ATLETA]);
// 		}
// 		return $ret;
		if (!$this->includeAtl) $this->includeAtleti();
		
		if ($this->isAffiliata()) {
			$lista = array(); 
			foreach ($coach as $c) {
				/* @var $c Coach */
				$lista[] = $c->getPersona();
			}
			return TesseratoFiam::lista($this->getChiave(), $lista);
		} else {
			$lista = array();
			foreach ($coach as $c) {
				$lista[$c->getPersona()] = CoachEsterno::fromId($c->getPersona());
			}
			return $lista;
		}
		
	}
	
	public function getOfficial($off) {
		$lista = array();
		foreach($off as $o)
		{
			/* @var $o Official*/
			$lista[$o->getChiave()] = Official::fromId($o->getChiave());
		}
		
		return $lista;
	}
	
	public function getArbitri($arb) {
		if (!$this->includeAtl) $this->includeAtleti();
	
		if ($this->isAffiliata()) {
			$lista = array();
			foreach ($arb as $a) {
				/* @var $a Arbitro */
				if($a->getPersona() !== NULL)
					$lista[] = $a->getPersona();
				else 
					$lista[] = $a;
			}
			return TesseratoFiam::lista($this->getChiave(), $lista);
		} else {
			$lista = array();
			foreach ($arb as $a) {
				$lista[$a->getPersona()] = ArbitroEsterno::fromId($a->getPersona());//CoachEsterno::fromId($c->getPersona());
			}
			return $lista;
		}
	
	}
	
	/**
	 * @return int[] formato idatleta => int
	 */
	public function getCintureAtleti() {
		if (!$this->includeAtl) $this->includeAtleti();
	
		if ($this->isAffiliata()) {
			$cin = AtletaAffiliato::getCinture($this->getChiave(),
					$this->get("idaffiliata"));
		} else {
			$newatl = AtletaEsterno::atletiSocieta($this->getChiave(),$lista);
			//TODO fare diretto?
			foreach ($newatl as $id=>$a) {
				$cin[$id] = $a->getCintura();
			}
		}
		return $cin;
	}
	
	/**
	 * @param int $id
	 * @return Atleta
	 */
	public function getAtleta($id) {
		if ($this->atl_completi) return $this->atleti[$id];
		if (!$this->includeAtl) $this->includeAtleti();
		
		if ($this->isAffiliata()) {
			//TODO rivedere, magari alleggerire
			foreach(self::getAtleti(array($id)) as $a)
				return $a;
		} else {
			return new AtletaEsterno($id);
		}
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getStile() {
		return $this->get("idstile");
	}

	/**
	 * @access public
	 * @param int $stile
	 */
	public function setStile($idstile) {
		$this->set("idstile",$idstile);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getZona() {
		return $this->get("idzona");
	}

	/**
	 * @access public
	 * @param int $zona
	 */
	public function setZona($idzona) {
		$this->set("idzona",$idzona);
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getFedEst() {
		return $this->get("federazione_est");
	}
	
	/**
	 * @access public
	 * @param int $zona
	 */
	public function setFedEst($fed_est) {
		$this->set("federazione_est",$fed_est);
	}
	
	public function getWkc() {
		return $this->get("wkc");
	}
	
	public function setWkc($wkc) {
		$this->set("wkc", $wkc);
	}
	
	public function getNazione() {
		return $this->get("nazione");
	}
	
	public function setNazione($idnazione) {
		$this->set("nazione", $idnazione);
	}

	/**
	 * le gare attive a cui è iscritta
	 * @param int[] $tipiisc se impostato conterrà i tipi nel formato
	 * idgara => [1 individuale, 2 squadre, 3 entrambi]
	 * @return Gara[]
	 */
	public function getGare(&$tipiisc=false) {
		$salvatipi = $tipiisc!==false;
		if ($salvatipi) $tipiisc = array();
		$conn = $this->_connessione;
		$id = $this->getChiave();
		//individuali
		$mr = $conn->select("individuali","idsocieta = '$id'", "DISTINCT idgara");
		$idgare = array();
		while($row = $mr->fetch_row()) {
			if ($salvatipi)
				$tipiisc[$row[0]] = 1;
			$idgare[$row[0]] = $row[0]; 
		}
		//squadre
		$mr = $conn->select("squadre","idsocieta = '$id'", "DISTINCT idgara");
		while($row = $mr->fetch_row()) {
			if ($salvatipi) {
				if (isset($idgare[$row[0]]))
					$tipiisc[$row[0]] = 3;
				else
					$tipiisc[$row[0]] = 2;
			} 
			$idgare[$row[0]] = $row[0]; 
		}
		if (count($idgare) == 0) return array();
		return Gara::insieme($idgare);
	}
	
	public function isAffiliata() {
		return !is_null($this->get("idaffiliata"));
	}
	
	public function haIndividuali($idgara) {
		return $this->haIscritti($idgara, "individuali");
	}
		
	public function haSquadre($idgara) {
		return $this->haIscritti($idgara, "squadre");
	}
			
	private function haIscritti($idgara, $tabella) {
		$id = $this->getChiave();
		$mr = $this->_connessione->select($tabella, "idsocieta = '$id' AND idgara = '$idgara' LIMIT 1");
		return !is_null($mr->fetch_row());
	}
}
?>