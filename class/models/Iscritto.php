<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
abstract class Iscritto extends Modello {

	/**
	 * @return int
	 */
	public function getGara() {
		return $this->get("idgara");
	}
	
	/**
	 * @return int
	 */
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	/**
	 * @return int
	 */
	public function getStile() {
		return $this->get("idstile");
	}

	/**
	 * @param int $stile
	 */
	public function setStile($idstile) {
		$this->set("idstile", $idstile);
	}
	
	/**
	 * @return boolean
	 */
	public function isHandicap() {
		return false;
	}

	/**
	 * @return int
	 */
	public function getCategoria() {
		return $this->get("idcategoria");
	}

	/**
	 * @return int
	 */
	public function getAccorpamento() {
		return $this->get("idaccorpamento");
	}
	
	/**
	 * @param int $idaccorpamento
	 */
	public function setAccorpamento($idaccorpamento) {
		$this->set("idaccorpamento", $idaccorpamento);
	}
	
	/**
	 * @return int
	 */
	public function getCategoriaFinale() {
		$idc = $this->get("idaccorpamento");
		if (is_null($idc)) return $this->get("idcategoria");
		else return $idc; 
	}
	
	/**
	 * @return boolean
	 */
	public function isAccorpato() {
		return !is_null($this->get("idaccorpamento"));
	}
	
	/**
	 * @return boolean
	 */
	public function isSeparato() {
		return $this->get("pool") != 0;
	}
	
	/**
	 * @return int
	 */
	public function getPool() {
		return $this->get("pool");
	}
	
	/**
	 * @param int $valore
	 */
	public function setPool($valore) {
		$this->set("pool",$valore);
	}
}
?>