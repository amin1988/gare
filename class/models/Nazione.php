<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
class Nazione extends Modello {
	/**
	 * @var Nazione[]
	 */
	private static $nazioni;

	/**
	 * @access public
	 * @return Nazione[] formato idnaz => Nazione
	 * @static
	 */
	public static function listaNazioni() {
		if (is_null(self::$nazioni)){
			$conn = $GLOBALS["connint"];
			$conn->connetti();
			$mr = $conn->select("nation","1 ORDER BY bezeichnung");
			while($row = $mr->fetch_assoc()){
				$n = new Nazione();
				$n->carica($row);
				self::$nazioni[$n->getChiave()] = $n;
			}
		}
		return self::$nazioni;
	}

	/**
	 * @access public
	 * @param int $id
	 * @return Cintura
	 * @static
	 */
	public static function getCintura($id) {
		if (is_null($id)) return NULL;
		if (isset(self::$nazioni[$id]))
			return self::$nazioni[$id];
		$n = new Nazione($id);
		self::$nazioni[$id] = $n;
		return $n;
	}

	/**
	 * @access private
	 * @param int $id
	 */
	public function __construct($id = NULL) {
		parent::__construct("nation","id",$id);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("bezeichnung");
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getIso() {
		return $this->get("iso");
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getKurz() {
		return $this->get("kurz");
	}
}
?>