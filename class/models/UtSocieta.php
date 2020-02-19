<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente", "Societa");

/**
 * @access public
 * @package models
 */
class UtSocieta extends Utente {
	/**
	 * @var Societa
	 */
	private $societa = NULL;

	/**
	 * @access public
	 * @param int $id
	 * @return UtSocieta
	 * @static
	 */
	public static function crea($id = NULL){
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		$ut = new UtSocieta($id);
		if ($ut->isAttivo() && $ut->getTipo() == Utente::SOCIETA
				 && !is_null($ut->get("idsocieta"))) 
			return $ut;
		else
			return NULL;
	}
	
	/**
	 * @param int $idsoc
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email
	 * @return UtSocieta
	 */
	public static function nuovo($idsoc, $psw, $dati) {
		$u = new UtSocieta();
		$u->nuovoUtente(Utente::SOCIETA, $psw, $dati);
		$u->set("idsocieta", $idsoc);
		return $u;
	}
	
	/**
	 * @access public
	 */
	public function __construct($id = NULL) {
		parent::__construct($id);
	}

	/**
	 * @access public
	 * @return Societa
	 */
	public function getSocieta() {
		if (is_null($this->societa))
			$this->societa = new Societa($this->get("idsocieta"));
		return $this->societa;
	}
	
	/**
	 * @return int
	 */
	public function getIdSocieta() {
		return $this->get("idsocieta");
	}
	
	public function setIdSocieta($valore) {
		if ($this->get("idsocieta") != $valore) {
			$this->societa == NULL;
			$this->set("idsocieta",$valore);
		}
	}
	
	/**
	 * @access public
	 * @return int[]
	 */
	public function getZone() {
		return array($this->getSocieta()->getZona());
	}
	
	/**
	 * Restituisce le zone associate all'utente comprese le zone superiori
	 * @access public
	 * @return Zona[]
	 */
	public function getZoneEstese() {
		$z = Zona::getZona($this->getSocieta()->getZona());
		$e = array($z->getChiave() => $z) + $z->getSuperiori();
		return $e;
	}
	
	/**
	 * @access public
	 * @param int $idzona
	 * @return boolean
	 */
	public function haZona($idzona) {
		return $idzona == $this->getSocieta()->getZona();
	}
	
	public function getNomeTipo() {
		return "Societa";
	}
}
?>