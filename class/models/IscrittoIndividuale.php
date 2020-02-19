<?php
if (!defined("_BASEDIR_")) exit();
include_model("Iscritto");

/**
 * @access public
 * @package models
 */
class IscrittoIndividuale extends Iscritto {
	private $inCat = true;
	/** @var Atleta */
	private $tmpAtl = NULL;
	
	/**
	 * Restituisce gli atleti iscritti ad una gara
	 * @param int $idgara
	 * @param int $idsoc se != NULL restituisce solo gli atleti di una societa
	 * @param int $idcat se != NULL e $idsco == NULL restituisce solo gli atleti in una categoria
	 * @return IscrittoIndividuale[]
	 */
	public static function listaGara($idgara, $idsoc = NULL, $idcat = NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (!is_null($idsoc))
			$mr = $conn->select("individuali","idgara = '$idgara' AND idsocieta = '$idsoc'");
		else if (!is_null($idcat))
			$mr = $conn->select("individuali","idgara = '$idgara' AND idcategoria = '$idcat'");
		else
			$mr = $conn->select("individuali","idgara = '$idgara'");
		$isc = array();
		if (is_null($mr)) return $isc;
		while($row = $mr->fetch_assoc()) {
			$i = new IscrittoIndividuale();
			$i->carica($row);
			$isc[$i->getChiave()] = $i;
		}
		return $isc;
	}
	
	/**
	 * @param int $idgara
	 * @param int $idcat categoria sorgente da eliminare
	 * @param int $idacc categoria destinazione o NULL per eliminare l'accorpamento
	 */
	public static function accorpa($idgara, $idcat, $idacc) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($idacc))
			$conn->query("UPDATE individuali SET idaccorpamento = NULL WHERE idgara = '$idgara' AND idcategoria = '$idcat'");
		else
			$conn->query("UPDATE individuali SET idaccorpamento = '$idacc' WHERE idgara = '$idgara' AND idcategoria = '$idcat'");
	}
	
	
	/**
	 * @param Gara $gara
	 * @param array $dati formato: "atleta"=>Atleta, "tipi"=>int[], "stile"=>int, "peso"=>int
	 * @return IscrittoIndividuale[] uno per ogni tipo gara
	 */
	public static function nuovoMulti($gara, $dati) { //TODO eliminare
		/* @var $a Atleta */
		$a = $dati["atleta"];
		$isc = array();
		foreach ($dati["tipi"] as $tipo) {
			$i = new IscrittoIndividuale();
			$i->set("idgara", $gara->getChiave());
			if ($a->hasChiave())
				$i->set("idatleta", $a->getChiave());
			else
				$i->tmpAtl = $a;
			$i->set("idsocieta", $a->getSocieta());
			$i->set("idcintura", $a->getCintura());
			$i->set("tipogara", $tipo);
			if ($tipo == 0) //kata //TODO generalizzare
				$i->set("idstile", $dati["stile"]);
			if ($tipo == 1) //sanbon //TODO generalizzare
				$i->set("peso", $dati["peso"]);
	
			//selezione della categoria
			$i->calcolaCategoria($gara, $a);
	
			$isc[] = $i;
		}
		return $isc;
	}
	
	/**
	 * @param Gara $gara
	 * @param array $dati formato: 
	 * 				"atleta"=>Atleta,
	 * 				"cintura"=>int,
	 * 				"tipo"=>int,
	 * 				"stile"=>int,
	 * 				"peso"=>int,
	 * 				"hp"=>boolean
	 * @return IscrittoIndividuale[] uno per ogni tipo gara
	 */
	public static function nuovo($gara, $dati) {
		/* @var $a Atleta */
		$a = $dati["atleta"];
		$i = new IscrittoIndividuale();
		$i->set("idgara", $gara->getChiave());
		if ($a->hasChiave())
			$i->set("idatleta", $a->getChiave());
		else
			$i->tmpAtl = $a;
		$i->set("idsocieta", $a->getSocieta());
		$i->set("idcintura", $dati["cintura"]);
		$i->set("tipogara", $dati["tipo"]);
		if ($dati["tipo"] == 0) //kata //TODO generalizzare
			$i->set("idstile", $dati["stile"]);
		//if ($dati["tipo"] == 1 || $gara->haCategorieIpponPeso()) //sanbon //TODO generalizzare
			$i->set("peso", $dati["peso"]);
		if (isset($dati["hp"])) {
			$i->setBool("hp", $dati["hp"]);
			$a->setHandicap($dati["hp"]);
		}

		//selezione della categoria
		$i->calcolaCategoria($gara, $a);
		
		return $i;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("individuali", "idiscritto", $id);
		$this->_backup = true;
	}
	
	/**
	 * @param Gara $gara
	 * @param Atleta $atleta
	 */
	public function calcolaCategoria($gara, $atleta) {
		$cat = array();
		foreach ($gara->getCategorieIndiv() as $c) {
			/* @var $c Categoria */
			if ($c->individualeInCategoria($atleta, $this, $gara->getDataGara()))
				$cat[] = $c;
		}
		if (count($cat) == 0) {
			$this->inCat = false;
		} else if (count($cat) > 1) {
			//TODO scegli categoria min
			$this->set("idcategoria", $cat[0]->getChiave());
		} else {
			$this->set("idcategoria", $cat[0]->getChiave());
		}
	}
	
	/**
	 * Indica se l'iscritto appartiene ad una categoria
	 * @return boolean
	 */
	public function inCategoria() {
		return $this->inCat;
	}
	
	/**
	 * @return int
	 */
	public function getAtleta() {
		if (is_null($this->tmpAtl))
			return $this->get("idatleta");
		else
			return $this->tmpAtl->getChiave();
	}
	
	/**
	 * @return int
	 */
	public function getCintura() {
		return $this->get("idcintura");
	}
	
	/**
	 * @param int $peso
	 */
	public function setCintura($idcintura) {
		$this->set("idcintura", $idcintura);
	}
	
	/**
	 * @return int
	 */
	public function getPeso() {
		return $this->get("peso");
	}

	/**
	 * @param int $peso
	 */
	public function setPeso($peso) {
		$this->set("peso", $peso);
	}
	
	public function getTipoGara() {
		return $this->get("tipogara");
	}
	
	public function isHandicap() {
		return $this->getBool("hp");
	}
	
	public function setHandicap($hp) {
		$this->setBool("hp", $hp);
	}
	
	public function salva() {
		if (!is_null($this->tmpAtl) && $this->tmpAtl->hasChiave()) {
			$this->set("idatleta",$this->tmpAtl->getChiave());
			$this->tmpAtl = NULL;
		}
		parent::salva();
	}
}
?>