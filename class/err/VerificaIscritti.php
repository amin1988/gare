<?php
if (!defined("_BASEDIR_")) exit();
include_class("Data");

//TODO estendere VerificaErrori
class VerificaIscritti {
	/**
	 * @var string[][] formato: idatleta => campi errati
	 */
	private $err;
	/**
	 * @var string[][] formato: id nuovo atleta => campi errati
	 */
	private $errnew;
	
	public function __construct(){
		$this->err = array();
		$this->errnew = array();
		
		if (isset($_POST["check"])) {
			foreach ($_POST["check"] as $ida) {
				$this->checkModificabili($ida,false);
			}
		}
		
		if (isset($_POST["newcheck"])) {
			foreach ($_POST["newcheck"] as $ida) {
				$this->checkNuovi($ida);
				$this->checkModificabili($ida,true);
			}
		}
	}
	
	/**
	 * @return boolean
	 */
	public function haErrori() {
		return (count($this->err)+count($this->errnew) > 0);
	}
	
	public function setErroreCat($id, $tipo, $new) {
		$this->addErr($id, "cat$tipo", $new);
	}
	
	public function rimuoviErroriNuovo($idnuovo) {
		unset($this->errnew[$idnuovo]);
	}
	
	/**
	 * @param int $id
	 * @param string $campo
	 * @return boolean
	 */
	public function isErrato($id, $campo=NULL, $new = false) {
		if (!$new && !isset($this->err[$id])) return false;
		if ($new && !isset($this->errnew[$id])) return false;
		if (is_null($campo)) return true; //ha degli errori
		if ($new)
			return in_array($campo, $this->errnew[$id]);
		else
			return in_array($campo, $this->err[$id]);
	}

	/**
	 * @param int $id
	 * @return string[]
	 */
	public function getErrori($id) {
		return $this->err[$id];
	}
	
	/**
	 * @param int $id
	 * @param string $campo
	 * @return boolean
	 */
	public function isErratoNuovo($id, $campo=NULL) {
		return $this->isErrato($id, $campo, true);
	}
	
	/**
	 * @param int $id
	 * @return string[]
	 */
	public function getErroriNuovo($id) {
		return $this->errnew[$id];
	}
	
	/**
	 * @param int $id
	 * @param string $campo
	 * @param boolean $new
	 */
	private function addErr($id, $campo, $new) {
		if ($new) $this->errnew[$id][] = $campo;
		else $this->err[$id][] = $campo;
		
	}
	
	/**
	 * @param int $id
	 * @param boolean $new
	 */
	private function checkModificabili($id, $new) {
		$this->checkTipo($id, $new);
		$this->checkPeso($id, $new);
	}
	
	/**
	 * @param int $id
	 */
	private function checkNuovi($id) {
		$this->checkTesto($id, "cognome", true);
		$this->checkTesto($id, "nome", true);
		$this->checkElenco($id, "sesso");
		$this->checkNascita($id);
		$this->checkElenco($id, "newcintura");
	}
	
	/**
	 * @param int $id
	 * @param boolean $new
	 */
	private function checkTipo($id, $new) {
		$c = Iscrivi::nomeCampo("tipo", $new);
		if (!isset($_POST[$c]) ||!isset($_POST[$c][$id]) 
			|| count($_POST[$c][$id]) == 0) 
		{
// 			echo count($_POST[$c][$id])." - ";
			$this->addErr($id, $c, $new);
		}
	}
	
	/**
	 * @param int $id
	 * @param boolean $new
	 */
	private function checkPeso($id, $new) {
		$c = Iscrivi::nomeCampo("peso", $new);
		$ct = Iscrivi::nomeCampo("tipo", $new);
		//se non fa sanbon esci
		if (!isset($_POST[$ct]) || !isset($_POST[$ct][$id]) 
			|| !in_array(1, $_POST[$ct][$id])) return;
		//se il testo � vuoto esci (errore gi� memorizzato)
		if (!$this->checkTesto($id, $c, $new)) return;
		//se il testo non � un numero salva errore
		if (!is_numeric($_POST[$c][$id]))
			$this->addErr($id, $c, $new);
	}
	
	/**
	 * @param int $id
	 * @param string $campo
	 * @param boolean $new
	 */
	private function checkTesto($id,$campo,$new) {
		if (trim($_POST[$campo][$id]) == "") {
			$this->addErr($id, $campo, $new);
			return false;
		}
		return true;
	}
	
	/**
	 * @param int $id
	 */
	private function checkNascita($id) {
		if (!$this->checkTesto($id, "nascita", true)) return;
		$d = Data::parseDMY($_POST["nascita"][$id]);
		if (is_null($d) || !$d->valida() || $d->futura())
			$this->errnew[$id][] = "nascita";
	}
	
	/**
	 * @param int $id
	 * @param string $campo
	 * @param boolean $new
	 */
	private function checkElenco($id, $campo) {
		if (!isset($_POST[$campo]) ||!isset($_POST[$campo][$id]) 
			|| $_POST[$campo][$id] <= 0)
			$this->errnew[$id][] = $campo;
	}
	
	public function toString($ida, $new) {
		if ($new) $le = $this->errnew[$ida];
		else $le = $this->err[$ida];
		$str = false;
		foreach ($le as $e) {
			if ($str !== false) $str .= ", ";
			$p = "err_" . str_replace("new", "", $e);
			$str .= Lingua::getParola($p);
		}
		return ucfirst(Lingua::getParola("errore")).': '.$str;
	} 	
}
?>