<?php
if (!defined("_BASEDIR_")) exit();
include_model("UtGare");

/**
 * @access public
 * @package models
 */
class Organizzatore extends UtGare {

	/**
	 * @access public
	 * @param int $id
	 * @return Organizzatore
	 * @static
	 */
	public static function crea($id=NULL){
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		$ut = new Organizzatore($id);
		if ($ut->isAttivo() && $ut->getTipo() == Utente::ORGANIZZATORE) 
			return $ut;
		else
			return NULL;
	}
	
	/**
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email
	 * @param int[] $zone
	 * @return Organizzatore
	 */
	public static function nuovo($psw, $dati, $zone) {
		$u = new Organizzatore();
		$dati["zone"] = $zone;
		$u->nuovoUtente(Utente::ORGANIZZATORE, $psw, $dati);
		return $u;
	}
	
	/**
	 * @access public
	 * @param int $id
	 */
	public function __construct($id = NULL) {
		parent::__construct($id);
	}
	
	public function getNomeTipo() {
		return "Organizzatore";
	}
}
?>