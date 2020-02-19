<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente","Gara");

class Storico {
	/**
	 * @var Gara[]
	 */
	private $gare;
	
	/**
	 *  @var Utente 
	 */
	private $ut;
	
	public function __construct() {
		$this->ut = Utente::crea();
		$this->gare = Gara::getGarePassate();
	}
	
	public function loginEffettuato() {
		return !is_null($this->ut);
	}
	
	/**
	 * @return Utente
	 */
	public function getUtente() {
		return $this->ut;
	}
	
	public function getTipoUtente() {
		if (is_null($this->ut))
			return -1;
		return $this->ut->getTipo();
	}
	
	/**
	 * @return Gara[]
	 */
	public function getGare() {
		return $this->gare;
	}
	
	/**
	 * @param Gara $gara
	 * @return string la dimensione da limitare
	 */
	public function getLocandinaSize($gara) {
		$file = _BASEDIR_.$gara->getLocandina();
		$size = getimagesize($file);
		$w = $size[0];
		$h = $size[1];
		if ($h > $w)
			return "height";
		else
			return "width";
	}
}
?>
