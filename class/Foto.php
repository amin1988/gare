<?php
if (!defined("_BASEDIR_")) exit();

define("_FOTO_SUBDIR_", "coach/");
define("_FOTO_DEFAULT_", _FOTO_SUBDIR_."default.jpg");

class Foto {
	/**
	 * id societa
	 * @var int
	 */
	private $soc;
	/**
	 * id persona
	 * @var int
	 */
	private $id;

	/**
	 * @param Coach $c
	 */
	public static function coach($c) {
		return new Foto($c->getSocieta(), $c->getPersona());
	}

	/**
	 * @param Persona $c
	 */
	public static function persona($p) {
		return new Foto($p->getSocieta(), $p->getChiave());
	}
	
	private function __construct($ids, $idp) {
		$this->soc = $ids;
		$this->id = $idp;
	}
	
	private function getFotoPathInner() {
		return _FOTO_SUBDIR_."{$this->soc}_{$this->id}.jpg";
	}
	

	public function esiste() {
		return file_exists(_BASEDIR_.$this->getFotoPathInner());
	}
	
	/**
	 * @param boolean $default true o niente per avere l'immagine
	 * di default se manca la foto, false per avere sempre il percorso personale
	 * @return string il percorso relativo dell'immagine
	 */
	public function getFoto($default=true) {
		if (!$default || $this->esiste())
			return $this->getFotoPathInner();
		else
			return _FOTO_DEFAULT_;
	}
}