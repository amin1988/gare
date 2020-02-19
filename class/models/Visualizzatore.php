<?php
if (!defined("_BASEDIR_")) exit();
include_model("UtGare");

/**
 * @access public
 * @package models
 */
class Visualizzatore extends UtGare {
	
	/**
	 * @access public
	 * @param int $id
	 * @return Visualizzatore
	 * @static
	 */
	public static function crea($id = NULL){
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		$ut = new Visualizzatore($id);
		if ($ut->isAttivo() && $ut->getTipo() == Utente::VISUALIZZA) 
			return $ut;
		else
			return NULL;
	}
	
	/**
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email
	 * @param int[] $zone
	 * @return Visualizzatore
	 */
	public static function nuovo($psw, $dati, $zone) {
		$u = new Visualizzatore();
		$dati["zone"] = $zone;
		$u->nuovoUtente(Utente::VISUALIZZA, $psw, $dati);
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
		return "Visualizzatore";
	}
}
?>