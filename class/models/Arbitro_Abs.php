<?php
if (!defined("_BASEDIR_")) exit();
include_model("Persona");

/**
 * @access public
 * @package models
 */
abstract class Arbitro_Abs extends Persona {
	
	public function __construct($tabella, $chiaveCol, $chiaveVal = NULL, $conn = NULL){
		parent::__construct($tabella, $chiaveCol, $chiaveVal, $conn);
	}
	
	public function getTipo() {
		return Persona::TIPO_ARBITRO;
	}

	/**
	 * @return boolean
	 */
	public abstract function isVerificato();

}
?>