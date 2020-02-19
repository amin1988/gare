<?php
if (!defined("_BASEDIR_")) exit();
require_once(_BASEDIR_."connection.inc.php");

/**
 * @access public
 * @package models
 */
class Lingua {
	/**
	 * @var string
	 */
	private static $_linguaDefault = NULL;
	/**
	 * @var array
	 */
	private static $_parole = array();
	/**
	 * @var array
	 */
	private static $_idparole = NULL;
	
	/**
	 * @access public
	 * @return string[]
	 * @static
	 */
	public static function getLingue() {
		return array("it" => "Italiano", "en" => "English"); //TODO generalizzare
	}

	/**
	 * imposta l'insieme di parole da leggere dal database 
	 * @access public
	 * @param int[] $idparole
	 * @return void
	 * @static
	 */
	
	public static function setParole($idparole) {
		self::$_idparole = $idparole;
	}

	/**
	 * @access public
	 * @param string lingua
	 * @return hash
	 * @static
	 */
	public static function getParole($lingua = NULL) {
		if (is_null($lingua)) $lingua = self::getLinguaDefault();
		if (!isset(self::$_parole[$lingua])) self::caricaParole($lingua);
		return self::$_parole[$lingua];
	}
	
	public static function getParola($chiave, $lingua = NULL) {
		if (is_null($lingua)) $lingua = self::getLinguaDefault();
		if (_LOCALHOST_ && $lingua=="debug")
			return "[$chiave]";
		if (!isset(self::$_parole[$lingua])) self::caricaParole($lingua);
		return self::$_parole[$lingua][$chiave];
	}
	
	private static function caricaParole($lingua) {
		$lcol = $lingua;
		if (_LOCALHOST_ && $lingua=="debug")
			$lcol = "concat('[',chiave,']')";
		if (is_null(self::$_idparole)) $where = "1";
		else {
			$primo = true;
			$where = "idparola in (";
			foreach (self::$_idparole as $id) {
				if ($primo) {
					$where .= "'$id'";
					$primo = false;
				} else 
					$where .= ", '$id'";
			}
			$where .= ")";
		}
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("lingue", $where, "chiave, $lcol AS p");
		while ($row = $mr->fetch_assoc()) {
			self::$_parole[$lingua][$row["chiave"]] = $row["p"];
		}
	}

	/**
	 * @access public
	 * @return string
	 * @static
	 */
	public static function getLinguaDefault() {
		if (!is_null(self::$_linguaDefault)) return self::$_linguaDefault;
		
		if(_WKC_MODE_)
			$l = "en";
		else
			$l = "it";
		
		if (isset($_COOKIE["lang"])) {
			$ll = self::getLingue();
			if (isset($ll[$_COOKIE["lang"]]) 
					|| (_LOCALHOST_ && $_COOKIE["lang"]=="debug")) {
				$l = $_COOKIE["lang"];
			}
		}
		if (isset($_GET["lang"])) {
			$ll = self::getLingue();
			if (isset($ll[$_GET["lang"]])
					|| (_LOCALHOST_ && $_GET["lang"]=="debug")) {
				$l = $_GET["lang"];
			}
		}
		self::setLinguaDefault($l);
		return self::$_linguaDefault;
	}

	/**
	 * @access public
	 * @param string $lingua
	 * @return void
	 * @static
	 */
	public static function setLinguaDefault($lingua) {
		if ((!isset($_COOKIE["lang"]) || $lingua != $_COOKIE["lang"]) && !headers_sent())
			setcookie("lang", $lingua, 0, _PATH_ROOT_);		
		self::$_linguaDefault = $lingua;
	}
}
?>