<?php
if (!defined("_BASEDIR_")) exit();
include_model("Persona");

/**
 * @access public
 * @package models
 */
abstract class Tecnico extends Persona {
	
	public function __construct($tabella, $chiaveCol, $chiaveVal = NULL, $conn = NULL){
		parent::__construct($tabella, $chiaveCol, $chiaveVal, $conn);
	}
	
	public function getTipo() {
		return Persona::TIPO_TECNICO;
	}

	/**
	 * @return boolean
	 */
	public abstract function isVerificato();

}
?>