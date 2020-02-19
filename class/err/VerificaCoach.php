<?php
if (!defined("_BASEDIR_")) exit();
include_errori("VerificaErrori");

class VerificaCoach extends VerificaErrori {
	const NUM_OK = 0;
	const NUM_POCHI = 1;
	const NUM_TROPPI = 2;
	
	/**
	 * Foto non caricata
	 */
	const FOTO_NO_UP = 1;
	/**
	 * File non foto
	 */
	const FOTO_NO_FOTO = 2;
	/**
	 * Tipo non valido
	 */
	const FOTO_TIPO = 3;
	/**
	 * Errore sconosciuto
	 */
	const FOTO_UNK = 4;
	
	private $coach = self::NUM_OK;
	private $numCoach;
	private $fotoCoach = array();
	
	public function __construct($gara) {
		$this->numCoach[self::NUM_POCHI] = $gara->getMinCoach();
		$this->numCoach[self::NUM_TROPPI] = $gara->getMaxCoach();
		if (isset($_POST["pageid"])) {
			//verifica numero
			$totcoach = 0;
			if (isset($_POST["coach"])) {
				foreach ($_POST["coach"] as $cl) {
					$totcoach += count($cl);
				}
			}
			if ($totcoach < $gara->getMinCoach())
				$this->coach = self::NUM_POCHI;
			else if ($totcoach > $gara->getMaxCoach())
				$this->coach = self::NUM_TROPPI;
		}
	}
	
	public function verificaFoto($idp) {
		//verifica upload foto
		if (!isset($_FILES["foto"]["error"][$idp]) || $_FILES["foto"]["error"][$idp] != UPLOAD_ERR_OK) {
			$this->fotoCoach[$idp] = self::FOTO_NO_UP;
			return false;
		}
		
		//verifica immagine
		try {
			$info = @getimagesize($_FILES["foto"]["tmp_name"][$idp]);
		} catch (Exception $e) {
			$this->fotoCoach[$idp] = self::FOTO_UNK;
			return false;
		}
		if(empty($info)) {
			$this->fotoCoach[$idp] = self::FOTO_NO_FOTO;
			return false;
		}
		//verifica tipo
		if ($info[2] != IMAGETYPE_JPEG) {
			$this->fotoCoach[$idp] = self::FOTO_TIPO;
			return false;
		}
		return true;
	}
	
	public function setErroreFoto($id, $tipo) {
		$this->fotoCoach[$id] = $tipo;
	}
	
	public function haErrori() {
		if ($this->coach != 0) return true;
		return !empty($this->fotoCoach);
	}

	public function haErroreNum() {
		return $this->coach != self::NUM_OK;
	}
	
	public function getErroreNum() {
		return $this->coach;
	}

	public function haErroreFoto($id) {
		return isset($this->fotoCoach[$id]);
	}
	
	public function getErroreFoto($id) {
		return $this->fotoCoach[$id];
	}
	
	public function toStringNum() {
		$lk = "#err_coach_";
		switch ($this->coach) {
			case self::NUM_POCHI:
				$lk .= "min";
				break;
			case self::NUM_TROPPI:
				$lk .= "max";
				break;
			default:
				return "";
		}
		$n = $this->numCoach[$this->coach];
		if ($n == 1) {
			$str = Lingua::getParola($lk."1");
		} else {
			$str = str_replace("<NUM>", $n, Lingua::getParola($lk));
		}
		return ucfirst(Lingua::getParola("errore")).': '.$str;
	}
	
	public function toStringFoto($id) {
		if (!isset($this->fotoCoach[$id])) 
			return "";
		$lk = "#err_coach_foto{$this->fotoCoach[$id]}";
		return ucfirst(Lingua::getParola("errore")).': '.Lingua::getParola($lk);
	}
	
}