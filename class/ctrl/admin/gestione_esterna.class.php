<?php
if (!defined("_BASEDIR_")) exit();
include_model("Amministratore","Stile");
include_errori("VerificaEsterna");

abstract class GestioneEsterna {
	/**
	 * Lunghezza massima del nome breve
	 */
	const LEN_BREVE = 50;
	
	/** @var Amministratore */
	protected $ut;
	protected $errori;
	
	public function __construct($nuovo) {
		$this->ut = Amministratore::crea();
		if (is_null($this->ut)) nologin();
		
		if (!isset($_GET["id"])) {
			homeutente($this->ut);
			exit();
		}
		if (!$this->controlli()) {
			homeutente($this->ut);
			exit();
		}
		
		$this->errori = new VerificaEsterna($nuovo);
		if (isset($_POST["pageid"]) && !$this->errori->haErrori())
	 		$this->salvaSocieta();
	}
	
	protected abstract function controlli();
	
	public function getErrori() {
		return $this->errori;
	}
	
	protected abstract function salvaSocieta();
	
	public function getUtente() {
		return $this->ut;
	}
	
	/**
	 * @return Stile[]
	 */
	public function getListaStili() {
		return Stile::listaStili();
	}
	
	protected function getValue($campo, $default="") {
		if (isset($_POST[$campo])) return $_POST[$campo];
		else return $default;
	}
	
	public abstract function getNome();
	
	public abstract function getNomeBreve();
	
	public abstract function getStile();
	
	public function getUtenteAuto() {
		if (!isset($_POST["pageid"])) return true;
		return isset($_POST["user"]);
	}
	
	/**
	 * @var int
	 */
	public abstract function getZona();
	
	public static function getZonaPost() {
		$max = max(array_keys($_POST["zona"]));
		return $_POST["zona"][$max];
	}
	
	public function getCreaUtenteAuto() {
		return $this->getValue("creaut", true);
	}
	
}