<?php
require_once(realpath(dirname(__FILE__)) . '/../models/Modello.php');

/**
 * @access public
 * @package models
 */
class Stile extends Modello {
	/**
	 * @var Stile[]
	 */
	private static $stili;

	/**
	 * @return Stile[]
	 */
	public static function listaStili() {
		//TODO controllare incroci con getStile
		if (is_null(self::$stili)){
			$conn = $GLOBALS["connint"];
			$conn->connetti();
			$mr = $conn->select("stili");
			while($row = $mr->fetch_assoc()){
				$s = new Stile();
				$s->carica($row);
				self::$stili[$s->getChiave()] = $s;
			}
		}
		return self::$stili;
	}

	/**
	 * @access public
	 * @param int $id
	 * @return Stile
	 * @static
	 */
	public static function getStile($id) {
		if (is_null($id)) return NULL;
		if (isset(self::$stili[$id]))
			return self::$stili[$id];
		$s = new Stile($id);
		self::$stili[$id] = $s;
		return $s;
	}

	/**
	 * @access private
	 * @param int $id
	 */
	public function __construct($id = NULL) {
		parent::__construct("stili","idstile",$id);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
}
?>