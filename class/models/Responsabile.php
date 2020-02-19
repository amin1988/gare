<?php
if (!defined("_BASEDIR_")) exit();
include_model("UtGare");

/**
 * @access public
 * @package models
 */
class Responsabile extends UtGare {
	
	/**
	 * @access public
	 * @param int $id
	 * @return Responsabile
	 * @static
	 */
	public static function crea($id = NULL){
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		$ut = new Responsabile($id);
		if ($ut->isAttivo() && $ut->getTipo() == Utente::RESPONSABILE) 
			return $ut;
		else
			return NULL;
	}
	
	/**
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email
	 * @param int[] $zone
	 * @return Responsabile
	 */
	public static function nuovo($psw, $dati, $zone) {
		$u = new Responsabile();
		$dati["zone"] = $zone;
		$u->nuovoUtente(Utente::RESPONSABILE, $psw, $dati);
		return $u;
	}
	
	/**
	 * @access public
	 * @param int id
	 * 	 */
	public function __construct($id = NULL) {
		parent::__construct($id);
	}
	
	public function getNomeTipo() {
		return "Responsabile";
	}
}
?>