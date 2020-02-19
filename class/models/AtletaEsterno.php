<?php
if (!defined("_BASEDIR_")) exit();
include_model("Atleta");

/**
 * @access public
 * @package models
 */
class AtletaEsterno extends Atleta {

	public static function atletiSocieta($idsoc,$lista=NULL,$nere=false) {
		//TODO gestire flag nere
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($lista) || count($lista) == 0) {
			$where = "";
		} else {
			$where = "idatleta IN ".$conn->flatArray($lista)." AND ";
		}
		$where .= "idsocieta = '$idsoc' ORDER BY cognome,nome";
		
		$mr = $conn->select("atleti",$where);
		$atl = array();
		if (is_null($mr)) return $atl;
		while($row = $mr->fetch_assoc()) {
			$a = new AtletaEsterno();
			$a->carica($row);
			$atl[$a->getChiave()] = $a;
		}
		return $atl;
	}
	
	/**
	 * @param array $dati formato:
	 * 			"societa"=>int,
	 * 			"nome"=>string,
	 * 			"cognome"=>string,
	 * 			"sesso"=>int,
	 * 			"nascita"=>Data
	 * @return AtletaEsterno
	 */
	public static function nuovo($dati) {
		$a = new AtletaEsterno();
		$a->set("idsocieta", $dati["societa"]);
		$a->set("nome", $dati["nome"]);
		$a->set("cognome", $dati["cognome"]);
		$a->set("sesso", $dati["sesso"]);
		$a->setData("nascita", $dati["nascita"]);
		return $a;
	}
	
	public static function fromId($id) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("atleti","idatleta='$id'");
		$row = $mr->fetch_assoc();
	
		if($row !== NULL)
		{
			$at = new AtletaEsterno();
			$at->carica($row);
			return $at;
		}
		else
			return NULL;
	}
	
	public static function deleteAtleta($id_atl,$idsoc)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
	
		$conn->query("DELETE FROM individuali WHERE idatleta='$id_atl' AND idsocieta='$idsoc'");
		$conn->query("DELETE FROM atleti WHERE idatleta='$id_atl' AND idsocieta='$idsoc'");
	}
	
	public function __construct($id = NULL) {
		parent::__construct("atleti", "idatleta", $id);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	public function setNome($valore) {
		$this->set("nome", $valore);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCognome() {
		return $this->get("cognome");
	}
	
	public function setCognome($valore) {
		$this->set("cognome", $valore);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSesso() {
		return $this->get("sesso");
	}
	
	public function setSesso($valore) {
		$this->set("sesso", $valore);
	}

	/**
	 * @access public
	 * @return DateTime
	 */
	public function getDataNascita() {
		return $this->getData("nascita");
	}
	
	public function setDataNascita($valore) {
		$this->setData("nascita", $valore);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getCintura() {
		return $this->get("idcintura");
	}
	
/**
	 * @param int $cintura
	 */
	public function setCintura($idcintura) {
		$this->set("idcintura",$idcintura);
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	public function setSocieta($valore) {
		$this->set("idsocieta", $valore);
	}
	
	public function isVerificato() {
		return true; //TODO deve dipendere dalla societa?
	}
	
	public function getUrlDettagli() {
		return "";
	}
}
?>