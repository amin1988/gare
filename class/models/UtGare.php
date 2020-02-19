<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente","Zona");

/**
 * @access public
 * @package models
 */
abstract class UtGare extends Utente {
	/**
	 * @var Zona[]
	 */
	private $zoneEstese = NULL;

	/**
	 * @access public
	 */
	public function __construct($id = NULL) {
		parent::__construct($id);
		$this->aggiungiLista("zone", "zoneutente", "idzona");
	}
	
	/**
	 * @param int $tipo
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email, zone
	 */
	protected function nuovoUtente($tipo, $psw, $dati) {
		parent::nuovoUtente($tipo, $psw, $dati);
		$this->setLista("zone", $dati["zone"]);
	}
	
// 	protected function caricaListaResult($nome) {
// 		if ($nome != "zone") return NULL;
// 		if (!$this->hasChiave()) return NULL;
// 		$id = $this->getChiave();
// 		return $this->_connessione->select("zoneutente", "idutente = '$id'", "idzona");
// 	}

	/**
	 * @access public
	 * @param int $idzona
	 * @return boolean
	 */
	public function haZona($idzona) {
		$zone = $this->getZone();
		if (in_array($idzona, $zone)) return true;
		foreach ($zone as $idz) {
			if (in_array($idzona, Zona::getZona($idz)->getSuperiori())) 
				return true;
		}
		return false;
	}
	
	/**
	 * @access public
	 * @return int[]
	 */
	public function getZone() {
		return $this->getLista("zone");
	}
	
	/**
	 * @access public
	 * @param int[] zone
	 */
	public function setZone($zone) {
		$this->setLista("zone",$zone);
	}
	
	/**
	 * Restituisce le zone associate all'utente comprese le zone superiori
	 * @access public
	 * @return Zona[]
	 */
	public function getZoneEstese() {
		if (!is_null($this->zoneEstese)) return $this->zoneEstese;
		//TODO potrebbe avere anche zone inferiori
		$e = array();
		foreach ($this->getZone() as $idz) {
			$z = Zona::getZona($idz);
			$e[$z->getChiave()] = $z;
			$e = array_merge($e, $z->getSuperiori());
		}
		$this->zoneEstese = $e;
		return $this->zoneEstese;
	}
	
	/*public function salva(){
		$mod = $this->hasChiave();
		$parent->salva();
		if (!$this->_zonemod) return;
		if ($mod) {
			//TODO elimina zone eliminate e inserisce nuove zone
		} else {
			//TODO inserisce tutte le zone
		}
	}*/
}
?>