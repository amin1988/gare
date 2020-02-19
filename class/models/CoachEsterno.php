<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");
include_class("Foto");


class CoachEsterno extends Modello {
	private $foto = NULL;
	
	/**
	 * @param int $idgara
	 * @param int $idsocieta o NULL per leggere l'elenco di tutti i coach della gara
	 * @return Coach[][] formato: idsocieta => Coach[] se idsocieta != NULL, altrimenti Coach[]
	 */
	public static function lista($idgara, $idsocieta=NULL) {
		$allsoc = is_null($idsocieta);
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$where = "idgara = '$idgara'";
		if (!$allsoc)
			$where .= " AND idsocieta = '$idsocieta'";
		$mr = $conn->select("coach", $where);
		if (is_null($mr)) return array();
		$ret = array();
		while ($row = $mr->fetch_assoc()) {
			$c = new Coach();
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
	 * @param int $idgara
	 * @param Persona $p
	 */
	public static function creaCompleto($idgara, $ids, $idp, $tipo) {
		$c = new Coach();
		$c->set("idgara", $idgara);
		$c->set("idsocieta", $ids);
		$c->set("idpersona", $idp);
		$c->set("tipo", $tipo);
		return $c;
	}
	
	public static function coachSocieta($idsoc,$lista=NULL) {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($lista) || count($lista) == 0) {
			$where = "";
		} else {
			$where = "idatleta IN ".$conn->flatArray($lista)." AND ";
		}
		$where .= "idsocieta = '$idsoc' ORDER BY cognome,nome";
	
		$mr = $conn->select("coachesterni",$where);
		$coach = array();
		if (is_null($mr)) return $atl;
		while($row = $mr->fetch_assoc()) {
			$c = new CoachEsterno();
			$c->carica($row);
			$coach[$c->getChiave()] = $c;
		}
		return $coach;
	}
	
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
	
	/**
	 * @param array $dati formato:
	 * 			"societa"=>int,
	 * 			"nome"=>string,
	 * 			"cognome"=>string,
	 * 			"sesso"=>int,
	 * 			"nascita"=>Data
	 * @return CoachEsterno
	 */
	public static function nuovo($dati) {
		$c = new CoachEsterno();
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
		$mr = $conn->select("coachesterni","idcoach='$id'");
		$row = $mr->fetch_assoc();
	
		if($row !== NULL)
		{
			$c = new CoachEsterno();
			$c->carica($row);
			return $c;
		}
		else
			return NULL;
	}
	
	public static function deleteCoach($id_coach)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$conn->query("DELETE FROM coach WHERE idpersona='$id_coach'");
		$conn->query("DELETE FROM coachesterni WHERE idcoach='$id_coach'");
	}
	
	/**
	 * @param int $id
	 */
	public function __construct($id=NULL) {
		parent::__construct("coachesterni", "idcoach", $id);
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
	
	private function getFotoObj() {
		if ($this->foto === NULL) $this->foto = Foto::coach($this);
		return $this->foto;
	}
	
	public function getTipo() {
		return 1;
	}
	
	
	public function haFoto() {
		return $this->getFotoObj()->esiste();
	}
	
	/**
	 * @param boolean $default true o niente per avere l'immagine
	 * di default se manca la foto, false per avere sempre il percorso personale
	 * @return string il percorso relativo dell'immagine
	 */
	public function getFoto($default=true) {
		return $this->getFotoObj()->getFoto($default);
	}
}
