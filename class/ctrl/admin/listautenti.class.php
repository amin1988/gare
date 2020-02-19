<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente", "Amministratore", "Societa");

class ListaUtenti {
	/**
	 * @var Amministratore
	 */
	private $ut;
	/**
	 * @var Utente[][] formato tipo => id => Utente
	 */
	private $listaut;
	
	/**
	 * @var Societa[]
	 */
	private $soc;
	
	/**
	 * @param boolean $resp true se la pagina è per responsabili,
	 * false se è per organizzatori
	 */
	public function __construct() {
		$this->ut = Amministratore::crea();
		if (is_null($this->ut)) nologin();
		
		$ut = Utente::lista();
		$this->listaut = array();
		foreach ($ut as $id => $u) {
			/* @var $u Utente */
			$this->listaut[$u->getTipo()][$id] = $u; 
		}
		$this->soc = Societa::lista();
	}
	
	private function getUtenti($tipo) {
		if (isset($this->listaut[$tipo]))
			return $this->listaut[$tipo];
		else
			return array();
	} 
	
	public function getNomeZona($idz) {
		return Zona::getZona($idz)->getNome();
	}
	
	/**
	 * @param UtSocieta $uts
	 */
	public function getNomeSocieta($uts) {
		return $this->soc[$uts->getIdSocieta()]->getNome();
	}
	
	/**
	 * @param UtSocieta $uts
	 */
	public function getUrlModSocieta($uts) {
		$ids = $uts->getIdSocieta();
		/* @var $s Societa */
		$s = $this->soc[$ids];
		if ($s->isAffiliata()) {
			return _PATH_ROOT_."admin/aff/modifica.php?id=$ids";
		} else {
			return "javascript:;"; //TODO implementare modifica esterne
		}
	}
	
	/**
	 * @param Utente $ut
	 * @return boolean
	 */
	public function utenteLoggato($ut) {
		return $this->ut->getChiave() == $ut->getChiave();
	}
	
	/**
	 * @return Amministratore[]
	 */
	public function getAdmin() {
		return $this->getUtenti(Utente::ADMIN);
	}

	/**
	 * @return Responsabile[]
	 */
	public function getResponsabili() {
		return $this->getUtenti(Utente::RESPONSABILE);
	}

	/**
	 * @return Responsabile[]
	 */
	public function getVisualizzatori() {
		return $this->getUtenti(Utente::VISUALIZZA);
	}
	
	/**
	 * @return Organizzatore[]
	 */
	public function getOrganizzatori() {
		return $this->getUtenti(Utente::ORGANIZZATORE);
	}
	
	/**
	 * @return UtSocieta[]
	 */
	public function getSocieta() {
		return $this->getUtenti(Utente::SOCIETA);
	}
}
?>