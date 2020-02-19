<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
class Cintura extends Modello {
	/**
	 * @var Cintura[]
	 */
	private static $cinture;

	public static function cinturaNera() {
		return 7; //TODO fare meglio?
	}

	public static function cinturaMarrone() {
		return 6; //TODO fare meglio?
	}
	
	/**
	 * Indica se un insieme di cinture contiene cinture marroni o nere
	 * @param int[] $cinture
	 */
	public static function contieneMarroniNere($cinture) {
		if (in_array(self::cinturaMarrone(), $cinture))
			return true;
		if (in_array(self::cinturaNera(), $cinture))
			return true;
		return false;
	}
	
	/**
	 * @access public
	 * @return Cintura[] formato idcintura => Cintura
	 * @static
	 */
	public static function listaCinture() {
		if (is_null(self::$cinture)){
			$conn = $GLOBALS["connint"];
			$conn->connetti();
			$mr = $conn->select("cinture");
			while($row = $mr->fetch_assoc()){
				$c = new Cintura();
				$c->carica($row);
				self::$cinture[$c->getChiave()] = $c;
			}
		}
		return self::$cinture;
	}

	/**
	 * @access public
	 * @param int $id
	 * @return Cintura
	 * @static
	 */
	public static function getCintura($id) {
		if (is_null($id)) return NULL;
		if (isset(self::$cinture[$id]))
			return self::$cinture[$id];
		$c = new Cintura($id);
		self::$cinture[$id] = $c;
		return $c;
	}

	/**
	 * @access private
	 * @param int $id
	 */
	public function __construct($id = NULL) {
		parent::__construct("cinture","idcintura",$id);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get(Lingua::getLinguaDefault());
	}
}
?>