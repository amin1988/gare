<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

class Prestito extends Modello {
	private $tipo = NULL;

	/**
	 * Restituisce gli atleti di una societa che sono stati prestati ad altre
	 * @param int $idgara
	 * @param int $idsoc
	 * @return Prestito[]
	 */
	public static function usciti($idgara, $idsoc) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("prestiti", "idgara = '$idgara' AND idorig='$idsoc'");
		$ret = array();
		while($row = $mr->fetch_assoc()) {
			$p = new Prestito();
			$p->carica($row);
			$ret[] = $p;  
		}
		return $ret;
	}
	
	/**
	 * Restituisce l'atleta prestato ad una squadra
	 * @param int $idsquadra
	 * @return NULL|Prestito
	 */
	public static function squadra($idsquadra) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$row = $conn->select("prestiti", "idsquadra = '$idsquadra'")->fetch_assoc();
		if (!$row) return NULL;
		$p = new Prestito();
		$p->carica($row);
		return $p;
	}
	
	/**
	 * @param Squadra $sq
	 * @param Atleta $a
	 * @return Prestito
	 */
	public static function crea($sq, $a) {
		$p = new Prestito();
		$p->set("idsquadra", $sq->getChiave());
		$p->set("idatleta", $a->getChiave());
		$p->set("idorig", $a->getSocieta());
		$p->set("iddest", $sq->getSocieta());
		$p->set("idgara", $sq->getGara());
		return $p;
	}
	
	public function __construct() {
		parent::__construct("prestiti", "idsquadra");
	}
	
	/**
	 * @return int
	 */
	public function getSquadra() {
		return $this->getChiave(); 
	}
	
	/**
	 * @return int
	 */
	public function getAtleta() {
		return $this->get("idatleta");
	}
	
	/**
	 * @return int
	 */
	public function getOrigine() {
		return $this->get("idorig");
	}
	
	/**
	 * @return int
	 */
	public function getDestinazione() {
		return $this->get("iddest");
	}
	
	/**
	 * @return int
	 */
	public function getGara() {
		return $this->get("idgara");
	}
	
	/**
	 * Restituisce il tipo di categoria a cui appartiene la squadra 
	 */
	public function getTipo() {
		if ($this->tipo === NULL) {
			$idsq = $this->getSquadra();
			$rs = $this->_connessione->select('squadre INNER JOIN categorie USING(idcategoria)',
					"idsquadra='$idsq'", 'tipogara');
			$row = $rs->fetch_row();
			$this->tipo = $row[0];
		}
		return $this->tipo;
	}
}

?>