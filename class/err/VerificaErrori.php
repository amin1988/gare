<?php
if (!defined("_BASEDIR_")) exit();
include_class("Data");

abstract class VerificaErrori {

	public abstract function haErrori();
// 	/**
// 	 * @param int $id
// 	 * @param boolean $new
// 	 */
// 	private function checkTipo($id, $new) {
// 		$c = Iscrivi::nomeCampo("tipo", $new);
// 		if (!isset($_POST[$c]) ||!isset($_POST[$c][$id])
// 				|| count($_POST[$c][$id]) == 0)
// 		{
// 			// 			echo count($_POST[$c][$id])." - ";
// 			$this->addErr($id, $c, $new);
// 		}
// 	}

// 	/**
// 	 * @param int $id
// 	 * @param boolean $new
// 	 */
// 	private function checkPeso($id, $new) {
// 		$c = Iscrivi::nomeCampo("peso", $new);
// 		$ct = Iscrivi::nomeCampo("tipo", $new);
// 		//se non fa sanbon esci
// 		if (!isset($_POST[$ct]) || !isset($_POST[$ct][$id])
// 				|| !in_array(1, $_POST[$ct][$id])) return;
// 		//se il testo è vuoto esci (errore già memorizzato)
// 		if (!$this->checkTesto($id, $c, $new)) return;
// 		//se il testo non è un numero salva errore
// 		if (!is_numeric($_POST[$c][$id]))
// 			$this->addErr($id, $c, $new);
// 	}

	/**
	 * @param string $valore
	 * @return boolean true se il valore è valido
	 */
	protected function isTestoValido($valore) {
		if (trim($valore) == "")
			return false;
		else
			return true;
	}
	
	/**
	 * @param string $valore
	 * @return boolean true se il valore è valido
	 */
	protected function isDataPassata($valore,$richiesto=true,&$data=NULL) {
		if ($richiesto && !$this->isTestoValido($valore))
			return false;
		$d = Data::parseDMY($valore);
		if (is_null($d) || !$d->valida() || $d->futura())
			return false;
		$data = $d;
		return true;
	}
	
	/**
	 * @param string $valore
	 * @return boolean true se il valore è valido
	 */
	protected function isDataFutura($valore,$richiesto=true,&$data=NULL) {
		if ($richiesto && !$this->isTestoValido($valore))
			return false;
		$d = Data::parseDMY($valore);
		if (is_null($d) || !$d->valida() || !$d->futura())
			return false;
		$data = $d;
		return true;
	}

	/**
	 * @param string[] $le lista errori
	 * @param string $prefisso il prefisso della chiave in Lingua
	 * @return string
	 */
	protected function toStringInner($le,$prefisso) {
		if (is_array($le)) {
			$str = false;
			foreach ($le as $e) {
				if ($str !== false) $str .= ", ";
				$p = $prefisso . str_replace("new", "", $e);
				$str .= Lingua::getParola($p);
			}
		} else {
			$str = $le;
		}
		return ucfirst(Lingua::getParola("errore")).': '.$str;
	}

}
?>