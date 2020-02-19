<?php
if (!defined("_BASEDIR_")) exit();
include_model("Persona");

/**
 * @access public
 * @package models
 */
abstract class Atleta extends Persona {
	/**
	 * @var boolean
	 */
	private $hp = false;
	
	public function __construct($tabella, $chiaveCol, $chiaveVal = NULL, $conn = NULL){
		parent::__construct($tabella, $chiaveCol, $chiaveVal, $conn);
	}
	
	public function getTipo() {
		return Persona::TIPO_ATLETA;
	}

	/**
	 * @access public
	 * @return int
	 */
	public abstract function getCintura();
	
	/**
	 * @return boolean
	 */
	public abstract function isVerificato();
	
	/**
	 * @return string
	 */
	public abstract function getUrlDettagli();
		
	/**
	 * @return string o NULL
	 */
	public function getUrlCintura() {
		return NULL;
	}
	
	/**
	 * @return boolean
	 */
	public function isHandicap() {
		return $this->hp;
	}
	
	/**
	 * @param boolean $hp
	 */
	public function setHandicap($hp) {
		$this->hp = $hp;
	}
	
	/**
	 * @param Data $data
	 * @return int
	 */
	public function getEta($data) {
		return $this->getDataNascita()->anniDa($data, false);
	}
}
?>