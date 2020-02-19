<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");
include_class("Foto");


class Official extends Modello {
	
	/**
	 * @param int $idgara
	 * @param int $idsocieta o NULL per leggere l'elenco di tutti i coach della gara
	 * @return Official[][] formato: idsocieta => Official[] se idsocieta != NULL, altrimenti Official[]
	 */
	public static function lista($idgara, $idsocieta=NULL) {
		$allsoc = is_null($idsocieta);
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$where = "idgara = '$idgara'";
		if (!$allsoc)
			$where .= " AND idsocieta = '$idsocieta'";
		$mr = $conn->select("official", $where);
		if (is_null($mr)) return array();
		$ret = array();
		while ($row = $mr->fetch_assoc()) {
			$c = new Official();
			$c->carica($row);
			if ($allsoc)
				$ret[$c->getSocieta()][$c->getChiave()] = $c;
			else
				$ret[$c->getChiave()] = $c;
		}
		return $ret;
	}
	
	/**
	 * @param int $idgara
	 * @param Persona $p
	 */
	public static function crea($idgara, $val) {
		if (isset($val["persona"])) {
			$p = $val["persona"];
			return self::creaCompleto($idgara, $p->getSocieta(), $p->getChiave(), $p->getTipo());
		} else
			return self::creaCompleto($idgara, $val["ids"], $val["idp"], $val["tipo"]);
	}
	
	/**
	 * 
	 * @param unknown $idsoc
	 * @param string $lista
	 * @return Official[]
	 */
	public static function officialSocieta($idsoc,$lista=NULL) {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($lista) || count($lista) == 0) {
			$where = "";
		} else {
			$where = "idofficial IN ".$conn->flatArray($lista)." AND ";
		}
		$where .= "idsocieta = '$idsoc' ORDER BY cognome,nome";
	
		$mr = $conn->select("official",$where);
		$coach = array();
		if (is_null($mr)) return $atl;
		while($row = $mr->fetch_assoc()) {
			$c = new Official();
			$c->carica($row);
			$coach[$c->getChiave()] = $c;
		}
		return $coach;
	}
	
	
	/*
	public static function coachDisponibili($idsoc, $idgara)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$ar_in = "(";
		$i = 0;
		$mr = $conn->select("coach","idsocieta='$idsoc' AND idgara='$idgara'");
		while($row = $mr->fetch_assoc()) {
			if($i > 0)
				$ar_in .= ",";
			$ar_in .= $row["idpersona"];
			$i++;
		}
		$ar_in .= ")";
		
		if($i > 0)
			$mr = $conn->select("coachesterni","idsocieta='$idsoc' AND idcoach NOT IN $ar_in ORDER BY cognome,nome");
		else 
			$mr = $conn->select("coachesterni","idsocieta='$idsoc' ORDER BY cognome,nome");
		
		$coach = array();
		if (is_null($mr)) return $coach;
		while($row = $mr->fetch_assoc()) {
			$c = new CoachEsterno();
			$c->carica($row);
			$coach[$c->getChiave()] = $c;
		}
		return $coach;
	}
	*/
	
	/**
	 * @param array $dati formato:
	 * 			"societa"=>int,
	 * 			"nome"=>string,
	 * 			"cognome"=>string,
	 * 			"sesso"=>int,
	 * 			"nascita"=>Data
	 * @return Official
	 */
	public static function nuovo($dati) {
		$c = new Official();
		$c->set("idsocieta", $dati["societa"]);
		$c->set("nome", $dati["nome"]);
		$c->set("cognome", $dati["cognome"]);
		$c->set("sesso", $dati["sesso"]);
		$c->setData("nascita", $dati["nascita"]);
		return $c;
	}
	
	public static function fromId($id) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("official","idofficial='$id'");
		$row = $mr->fetch_assoc();
	
		if($row !== NULL)
		{
			$o = new Official();
			$o->carica($row);
			return $o;
		}
		else
			return NULL;
	}
	
	public static function deleteOfficial($idoff,$idsoc)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$conn->query("DELETE FROM official WHERE idofficial='$idoff' AND idsocieta='$idsoc'");
	}
	
	/**
	 * @param int $id
	 */
	public function __construct($id=NULL) {
		parent::__construct("official", "idofficial", $id);
	} 
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	public function setNome($val) {
		$this->set("nome", $val);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCognome() {
		return $this->get("cognome");
	}
	
	public function setCognome($val) {
		$this->set("cognome", $val);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSesso() {
		return $this->get("sesso");
	}
	
	public function setSesso($val) {
		$this->set("sesso", $val);
	}

	/**
	 * @access public
	 * @return DateTime
	 */
	public function getDataNascita() {
		return $this->getData("nascita");
	}
	
	public function setDataNascita($data) {
		$this->setData("nascita", $data);
	}
	
	/**
	 * @return int
	 */
	public function getIDSocieta() {
		return $this->get("idsocieta");
	}
	
	public function setIDSocieta($val) {
		$this->set("idsocieta", $val);
	}
	
	/**
	 * @return int
	 */
	public function getIDSocietaAff() {
		return $this->get("idsocieta_aff");
	}
	
	public function setIDSocietaAff($val) {
		$this->set("idsocieta_aff", $val);
	}
	
	public function getTipo() {
		return 1;
	}
}
