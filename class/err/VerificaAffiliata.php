<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente");
include_errori("VerificaErrori");

class VerificaAffiliata extends VerificaErrori {
	const USER_EXIST = "user_exist";
	const BREVE_LEN = "brevelen";
	
	/**
	 * @var string[]
	 */
	private $err;
	
	public function __construct($nuova) {
		$this->err = array();
		if (!isset($_POST["pageid"])) return;
		
		$this->checkTesto("nome");
		if ($this->checkTesto("nomebreve")) {
			if (strlen($_POST["nomebreve"]) > GestioneAffiliata::LEN_BREVE)
				$this->err[] = self::BREVE_LEN;
		}
		//TODO controllo nome doppio
		$this->checkTesto("stile");
		$idz = GestioneAffiliata::getZonaPost();
		if ($idz == "") 
			$this->err[] = "zona";
		else {
			$sub = Zona::getSottozone($idz);
			if (count($sub) > 0) $this->err[] = "zona";
		}
	}
	
	public function haErrori() {
		return count($this->err) > 0;
	}
	
	public function isErrato($campo) {
		return in_array($campo, $this->err);
	}
	
	/**
	 * @param string $campo
	 * @return boolean false se il campo è errato
	 */
	private function checkTesto($campo) {
		$val = $this->isTestoValido($_POST[$campo]);
		if (!$val) $this->err[] = $campo;
		return $val;
	}
}