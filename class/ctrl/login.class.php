<?php
if (!defined("_BASEDIR_")) exit();
include_model("Utente");

class Login {
	const NON_EFFETTUATO = 0;
	const CAMPO_VUOTO = 1;
	const ERRORE = 2;
	
	private $risultato = self::NON_EFFETTUATO;
	private $username = "";
	
	public function __construct() {
// 		$ut = Utente::crea();
// 		if (!is_null($ut)){
// 			homeutente($ut);
// 			exit(); 
// 		}
		if (isset($_POST["username"])) $this->username = trim($_POST["username"]);
		else return;

		if (!isset($_POST["username"]) || !isset($_POST["password"]))
			return;
		if (strlen($this->username) == 0 || strlen($_POST["password"]) == 0) {
			$this->risultato = self::CAMPO_VUOTO;
			return;
		}
		$ut = Utente::login($this->username, $_POST["password"]);
		if (is_null($ut)) {
			$this->risultato = self::ERRORE;
			return;
		}
		$ref = "";
		if (isset($_GET["red"])) {
			$ref = $_GET["red"];
// 			if (parse_url($ref, PHP_URL_HOST) != $_SERVER["HTTP_HOST"]) {
// 				$ref = "";
// 			}
		}
		
		if($ut->getTipo() == 1)
		{
		
		$s = Societa::fromId($ut->getIdSoc());
		if(!is_null($s))
		{
			$wkc = $s->getWkc();
			
			if(_WKC_MODE_)
			{
				if($wkc != 1)
					{
						$this->risultato = self::ERRORE;
						return;
					}
			}
			else 
			{
				if($wkc != 0)
					{
						$this->risultato = self::ERRORE;
						return;
					}
			}
		}

		}
		
		$ut->salvaAccesso();
		if ($ref == "")
			homeutente($ut);
		else
			header("Location: http://{$_SERVER["HTTP_HOST"]}{$ref}");
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function campoVuoto() {
		return $this->risultato == self::CAMPO_VUOTO;
	}
	
	public function loginErrato() {
		return $this->risultato == self::ERRORE;
	}
}
?>