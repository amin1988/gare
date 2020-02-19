<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

class LivelloZona extends Modello {
	
	public static function getPrimoLivello() {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("livellizone","padre IS NULL LIMIT 1");
		$lz = new LivelloZona();
		$lz->carica($mr->fetch_assoc());
		return $lz;
	} 
	
	public function __construct($id=NULL) {
		parent::__construct("livellizone", "idlivello", $id);
	}
	
	/**
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	/**
	 * @return int
	 */
	public function getPadre() {
		return $this->get("padre");
	}
}