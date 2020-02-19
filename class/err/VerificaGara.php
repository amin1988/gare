<?php
if (!defined("_BASEDIR_")) exit();
include_errori("VerificaErrori");

class VerificaGara extends VerificaErrori {
	const LOC_TIPO = "locandina_tipo";
	const DIFF_FINE = "difffine";
	const DIFF_CHIUSURA = "diffchiusura";
	const DIFF_COACH = "diffcoach";
	
	/**
	 * @var string[]
	 */
	private $err;
	
	public function __construct($nuova) {
		$this->err = array();
		if (!isset($_POST["nome"])) return;
		
		$this->checkTesto("nome");
		if ($nuova)
			$data = $this->checkData("data");
		else
			$data = Data::parseDMY($_POST["data"]);
		if (isset($_POST["chkfine"])) {
			$fine = Data::parseDMY($_POST["datafine"]);
			if (is_null($fine) || !$fine->valida())
				$this->err[] = "datafine";
			else if (!is_null($data) && !is_null($fine) && $data->confronta($fine) >= 0)
				$this->err[] = self::DIFF_FINE;
		}
		$chius = Data::parseDMY($_POST["chiusura"]);
		if (is_null($chius) || !$chius->valida())
			$this->err[] = "chiusura";
		else if (!is_null($data) && !is_null($chius) && $chius->confronta($data) > 0)
			$this->err[] = self::DIFF_CHIUSURA;
		
		//controllo num coach 
		$coach = true;
		$coach &= $this->checkInt("mincoach");
		$coach &= $this->checkInt("maxcoach");
		if ($coach && $_POST["mincoach"] > $_POST["maxcoach"])
			$this->err[] = self::DIFF_COACH;
		
		//controllo prezzi
		$this->checkFloat('prezzo_indiv');
		$this->checkFloat('prezzo_sq');
		$this->checkFloat('prezzo_coach');
		
		//controllo tipo locandina
		if (isset($_POST["chkloc"])) {
			if (!isset($_FILES["locandina"])) {
				$this->err[] = "locandina";
			} else {
				$tipi = GestioneGara::getTipiLocandina();
				$est = pathinfo($_FILES["locandina"]["name"],PATHINFO_EXTENSION);
				if (!in_array($est, $tipi))
					$this->err[] = self::LOC_TIPO;
				//TODO controllo se vera immagine?
			}
		}
		
		if ($nuova && !isset($_POST["gruppo"]))
			$this->err[] = "gruppo";
		
		if (!isset($_POST["zona"]))
			$this->err[] = "zona";
		
		if (isset($_POST["doc"])) {
			$okdoc = true;
			foreach ($_POST["doc"] as $id) {
				if (!$this->isTestoValido($_POST["nomedoc"][$id])) {
					$okdoc = false;
					$this->err[] = "nomedoc_$id";
				}
			}
			if (!$okdoc) $this->err[] = "doc";
		}
	}
	
	public function haErrori() {
		return count($this->err) > 0;
	}
	
	public function isErrato($campo) {
		return in_array($campo, $this->err);
	}
	
	private function checkTesto($campo) {
		if (!$this->isTestoValido($_POST[$campo]))
			$this->err[] = $campo;
	}
	/**
	 * @param string $campo
	 * @return Data
	 */
	private function checkData($campo) {
		$d = NULL;
		if (!$this->isDataFutura($_POST[$campo],true,$d))
			$this->err[] = $campo;
		return $d;
	}
	
	/**
	 * @param string $campo
	 * @return true se il campo è corretto
	 */
	private function checkInt($campo) {
		$v = $_POST[$campo];
		$ok = $this->isTestoValido($v) && is_numeric($v);
		if (!$ok) $this->err[] = $campo;
		return $ok;
	}

	/**
	 * @param string $campo
	 * @return true se il campo è corretto
	 */
	private function checkFloat($campo) {
		$v = $_POST[$campo];
		$ok = $this->isTestoValido($v);
		if ($ok)
			$ok = preg_match('/^\d+([.,]\d+|)$/', trim($v), $m);
		if (!$ok) $this->err[] = $campo;
		return $ok;
	}
}