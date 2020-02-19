<?php
if (!defined("_BASEDIR_")) exit();
include_model("Amministratore", "Societa");
include_esterni("SocietaAffiliata");

class ListaAffiliate {
	/**
	 * @var Societa[]
	 */
	private $lista;
	/**
	 * @var array formato idaffiliata => nome
	 */
	private $nonins;
	
	public function __construct() {
		$this->ut = Amministratore::crea();
		if (is_null($this->ut)) nologin();
				
		$this->lista = Societa::listaAffiliate("nome");
		$escludi = array();
		foreach ($this->lista as $s) {
			/* @var $s Societa */
			$escludi[] = $s->getIdAffiliata();
		}
		$this->nonins = SocietaAffiliata::getNomi($escludi);
	}
	
	/**
	 * @return array formato idaffiliata => nome
	 */
	public function getNonInserite() {
		return $this->nonins;
	}
	
	/**
	 * @return Societa[]
	 */
	public function getInserite() {
		return $this->lista;
	}
}
