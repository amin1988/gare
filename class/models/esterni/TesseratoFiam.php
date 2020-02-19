<?php
if (!defined("_BASEDIR_")) exit();
include_model("Persona");

class TesseratoFiam extends Persona {
	
	/**
	 * Restituisce i tesserati non tecnici e non cinture nere
	 * @return Persona[]
	 */
	public static function getAltriTesserati($idsoc, $idaff) {
		$where = " idsocieta='$idaff' AND idtesserato NOT IN (SELECT idtesserato FROM pagamenti_correnti WHERE idsocieta='$idaff'";
		$where .= " AND idtesserato IS NOT NULL AND idtipo NOT IN (1,2)) ORDER BY cognome, nome";
		return self::innerLista($idsoc, $where);
	}
	
	/**
	 * @param int[] $lista
	 * @return Persona[]
	 */
	public static function lista($idsoc, $lista) {
		if (is_null($lista) || count($lista) == 0)
			return array();
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		return self::innerLista($idsoc, "idtesserato IN ".$conn->flatArray($lista));
	}
	
	private static function innerLista($idsoc, $where) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$tes = array();
		
		$campi = "idtesserato, cognome, nome, sesso, data_nascita, '$idsoc' AS idsocieta";
		$mr = $conn->select("tesserati", $where, $campi);
		if (!is_null($mr)) {
			while($row = $mr->fetch_assoc()) {
				$t = new TesseratoFiam();
				$t->carica($row);
				$tes[$t->getChiave()] = $t;
			}
		}
		return $tes;
	}
	
	public static function getTessFIAM($id)
	{
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$campi = "idtesserato, cognome, nome, sesso, data_nascita,idsocieta";
		$mr = $conn->select("tesserati","idtesserato=$id",$campi);
		
		if(!is_null($mr))
		{
			$row = $mr->fetch_assoc();
			$t = new TesseratoFiam();
			$t->carica($row);
			
			return $t;
		}
		
		return NULL;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("", "idtesserato", $id, $GLOBALS["connest"]);
	}
	
	public function carica($row=NULL) {
		if (!is_null($row)) {
			parent::carica($row);
			return;
		}
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
	
	public function getTipo() {
		return Persona::TIPO_ATLETA;
	}
}
?>