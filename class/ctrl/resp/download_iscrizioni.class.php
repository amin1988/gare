<?php
if (!defined("_BASEDIR_")) exit();
include_model("Responsabile", "Gara");

define("_MOD_DIR_",_CLASSDIR_."mod_dl/");

class DownloadIscrizioni {
	/**
	 * @var Responsabile
	 */
	private $ut;
	/**
	 * @var Gara
	 */
	private $gara;
	
	/**
	 * @var Info[]
	 */
	private $info = NULL;
	
	public function __construct() {
		$this->ut = Responsabile::crea();
		if (is_null($this->ut)) nologin();
	
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste()) {
			homeutente($this->ut);
			exit();
		}
		
		include_class("Menu");
		include_controller("VerificaPaginaIndividuale");
		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));
	}
	
	public function datiInviati() {
		return isset($_GET["mod"]);
	}
	
	public function eseguiModulo() {
		if (!isset($_GET["mod"])) return;
		$mod = $_GET["mod"];
		if (preg_match('/^[a-z1-9\-_]+$/i', $mod)) {
			include _MOD_DIR_ . $mod.".inc.php";
		}
	}
	
	private function caricaInfo() {
		require_once _MOD_DIR_.'Info.class.php';
		$this->info = array();
		
		$gara = $this->gara;
		foreach (scandir(_MOD_DIR_) as $f) {
			if (preg_match('/^([a-z1-9\-_]+)\.info\.php$/i', $f, $m)) {
				include _MOD_DIR_ . $m[0];
				if (isset($info))
					$this->info[$m[1]] = $info;
			}
		}
	}

	public function getIdGara() {
		return $this->gara->getChiave();
	}

	public function getNomeGara() {
		return $this->gara->getNome();
	}
	
	/**
	 * @return Info[]
	 */
	public function getInfoModuli() {
		if ($this->info === NULL) $this->caricaInfo();
		return $this->info;
	}
}