<?php
if (!defined("_BASEDIR_")) exit();
include_model("Tecnico");

/**
 * @access public
 * @package models
 */
class TecnicoAffiliato extends Tecnico {
	const TIPO_TECNICO_FIAM = 2;
	
	public static function tecniciSocieta($idsoc,$idaff,$lista=NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$where = "a.idsocieta='$idaff' AND t.idtipo=".self::TIPO_TECNICO_FIAM;
		if ($lista !== NULL && count($lista) > 0) {
			$where .= " AND a.idtesserato IN ".$conn->flatArray($lista);
		}
		$tec = array();
		
// 		$mr = $conn->query("SELECT t.id, REPLACE(t.cognome, '\\\\', '' ) cognome, REPLACE(t.nome, '\\\\', '' ) nome, "
// 			."t.sesso, t.data_nascita, '$idsoc' AS idsocieta FROM t01_tecnici_$anno a "
// 			."INNER JOIN tesserati_$anno t ON a.idx=t.id "
// 			."WHERE $where ORDER BY cognome, nome;");
		$mr = $conn->query("SELECT a.idtesserato, cognome, nome, sesso, data_nascita, '$idsoc' AS idsocieta "
				." FROM tesserati a INNER JOIN tipi_tesserati t USING(idtesserato) "
				." INNER JOIN pagamenti_correnti p USING(idtesserato,idtipo) WHERE $where;");
		
		if (!is_null($mr)) {
			while($row = $mr->fetch_assoc()) {
				$t = new TecnicoAffiliato();
				$t->carica($row);
				$tec[$t->getChiave()] = $t;
			}
		}
		return $tec;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("", "idtesserato", $id, $GLOBALS["connest"]);
	}
	
	public function carica($row=NULL) {
		if (!is_null($row)) {
			parent::carica($row);
			return;
		}
		//TODO togliere sotto, o sistemare per tecnici
// 		if (is_null($this->getChiave())) return;
// 		$anno = AnnoSportivoFiam::get();
// 		$id = $this->getChiave();
// 		$mr = $this->_connessione->select("a01_atleti_$anno a INNER JOIN tesserati_$anno t ON a.idx=t.id ",
// 				"t.id='$id'",
// 				"t.id, REPLACE(t.cognome, '\\\\', '' ) cognome, REPLACE(t.nome, '\\\\', '' ) nome, "
// 				."t.sesso, t.data_nascita, a.cintura, a.ids");
// 		$row = $mr->fetch_assoc();
// 		if (!is_null($row)) parent::carica($row);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCognome() {
		return $this->get("cognome");
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSesso() {
		return $this->get("sesso");
	}

	/**
	 * @access public
	 * @return DateTime
	 */
	public function getDataNascita() {
		return $this->getData("data_nascita");
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	public function isVerificato() {
		return true; //TODO ci interessa che sia verificato?
	}
}
?>