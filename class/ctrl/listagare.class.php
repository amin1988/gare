<?php
if (!defined("_BASEDIR_")) exit();
include_model("Gara");

abstract class ListaGare {
	/**
	 * @var Utente
	 */
	protected $ut;

	public function __construct($tipout=NULL) {
		$this->ut = $this->creaUtente($tipout);
		if (is_null($this->ut)) nologin();
	}
	
	/**
	 * @return Utente
	 */
	protected abstract function creaUtente($tipout=NULL);
	
	/**
	 * return int[] id zone
	 */
	protected abstract function getZone();
	
	/**
	 * @access public
	 * @return Gara[]
	 */
	public function getGareAttive() {
		return Gara::getGareAttive($this->getZone());
	}
	
	/**
	 * @access public
	 * @return Gara[]
	 */
	public function getGareChiuse() {
		return Gara::getGareChiuse($this->getZone());	
	}
}
?>