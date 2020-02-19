<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
abstract class Persona extends Modello {
	const TIPO_ATLETA = 0;
	const TIPO_TECNICO = 1;
	const TIPO_TESSERATO = 2;
	const TIPO_ARBITRO = 3;
	
	public function __construct($tabella, $chiaveCol, $chiaveVal = NULL, $conn = NULL){
		parent::__construct($tabella, $chiaveCol, $chiaveVal, $conn);
	}
	/**
	 * @access public
	 * @return string
	 */
	public abstract function getNome();

	/**
	 * @access public
	 * @return string
	 */
	public abstract function getCognome();

	/**
	 * @access public
	 * @return int
	 */
	public abstract function getSesso();

	/**
	 * @access public
	 * @return Data
	 */
	public abstract function getDataNascita();

	/**
	 * @access public
	 * @return int
	 */
	public abstract function getSocieta();
	
	public abstract function getTipo();
}
?>