<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente");

/**
 * @access public
 * @package models
 */
class Amministratore extends Utente {

	/**
	 * @access public
	 * @param int id
	 * @return Amministratore
	 * @static
	 */
	public static function crea($id=NULL){
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		$ut = new Amministratore($id);
		if ($ut->isAttivo() && $ut->getTipo() == Utente::ADMIN) 
			return $ut;
		else
			return NULL;
	}
	
	public static function nuovo($psw, $dati) {
		$u = new Amministratore();
		$u->nuovoUtente(Utente::ADMIN, $psw, $dati);
		return $u;
	}
	
	/**
	 * @access public
	 * @param int id
	 */
	public function __construct($id = NULL) {
		parent::__construct($id);
	}
	
	public function getNomeTipo() {
		return "Amministratore";
	}
}
?>