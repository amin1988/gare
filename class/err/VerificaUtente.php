<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente");
include_errori("VerificaErrori");

class VerificaUtente extends VerificaErrori {
	const USER_EXIST = "user_exist";
	const PSW_DIFF = "pswdiff";
	
	/**
	 * @var string[]
	 */
	private $err;
	
	public function __construct($nuova) {
		$this->err = array();
		if (!isset($_POST["pageid"])) return;
		
		if ($nuova) {
			if ($this->checkTesto("username")) {
				if (Utente::usernameEsiste($_POST["username"]))
					$this->err[] = self::USER_EXIST;
			}
		}
		$this->checkTesto("nome");
		if ($nuova || $_POST["psw"] != "" || $_POST["psw2"] != "") {
			if ($this->checkTesto("psw") & $this->checkTesto("psw2")) {
				if ($_POST["psw"] != $_POST["psw2"])
					$this->err[] = self::PSW_DIFF;
			}
		}
		
		if ($this->checkTesto("email")) {
			if (preg_match('/^'.GestioneUtente::EMAIL_REGEX.'$/', $_POST["email"]) == 0)
				$this->err[] = "email";
		}
		
		switch ($_POST["tipo"]) {
			case Utente::ADMIN:
				break;
			case Utente::ORGANIZZATORE:
			case Utente::RESPONSABILE:
			case Utente::VISUALIZZA:
				$this->checkZona();
				break;
			case Utente::SOCIETA:
				$this->checkSocieta();
				break;
			default:
				$this->err[] = "tipo";
				break;
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
	
	private function checkEsiste($campo) {
		$val = isset($_POST[$campo]);
		if (!$val)
			$this->err[] = $campo;
		return $val;
	} 
	
	private function checkSocieta() {
		if (!$this->checkEsiste("soc")) return;
		include_model("Societa");
		$soc = new Societa($_POST["soc"]);
		if (!$soc->esiste())
			$this->err[] = "soc";
	}
	
	private function checkZona() {
		if (!$this->checkEsiste("zona")) return;
		include_model("Zona");
		$l = Zona::listaZone($_POST["zona"]);
		if (count($l) != count($_POST["zona"]))
			$this->err[] = "zona";
	}
}