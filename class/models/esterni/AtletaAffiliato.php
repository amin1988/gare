<?php
if (!defined("_BASEDIR_")) exit();
include_model("Atleta");
include_esterni("AnnoSportivoFiam", "AffiliazioneFiam");

/**
 * @access public
 * @package models
 */
class AtletaAffiliato extends Atleta {
	const CINTURA_NERA = 7;
	const TIPO_ATL_FIAM = 1;
	
	private $verificato = NULL;

	public static function convertiCintura($idfiam) {
		//TODO fare meglio? per il momento corrispondono
		return $idfiam;
	}
	
	public static function atletiSocietaRistretta($idsoc,$idaff,$lista=NULL,$nere=false) {
		return self::atletiSocieta($idsoc, $idaff, $lista, $nere, true);
	}
	
	public static function atletiSocietaAmpia($idsoc,$lista,$nere=false) {
		if (count($lista) == 0) return array();
		return self::atletiSocieta($idsoc, -1, $lista, $nere, false);
	}
	
	public static function atletiSocieta($idsoc,$idaff,$lista=NULL,$nere=false,$ristretta=false) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$where = 't.idtipo='.self::TIPO_ATL_FIAM;
		if ($lista === NULL || count($lista) == 0) {
			$where .= " AND a.idsocieta='$idaff'";
		} else {
			$where .= " AND a.idtesserato IN ".$conn->flatArray($lista);
			if ($ristretta) $where .= " AND a.idsocieta='$idaff'";
		}
		if ($nere) $where .= ' AND t.idgrado = '.self::CINTURA_NERA;
		
// 		$mr = $conn->query("SELECT t.id, REPLACE(t.cognome, '\\\\', '' ) cognome, REPLACE(t.nome, '\\\\', '' ) nome, "
// 			."t.sesso, t.data_nascita, t.assicurazione, '$idsoc' AS idsocieta, p.stato FROM pagamenti_$anno p "
// 			."INNER JOIN tesserati_$anno t ON p.idx=t.id "
// 			."WHERE $where ORDER BY cognome, nome");
		$mr = $conn->query("SELECT a.idtesserato, cognome, nome, sesso, data_nascita, t.idgrado, '$idsoc' AS idsocieta "
				." FROM tesserati a INNER JOIN tipi_tesserati t USING(idtesserato) "
				." INNER JOIN pagamenti_correnti p USING(idtesserato,idtipo) WHERE $where ORDER BY cognome, nome;");
		$atl = array();
		if (!$mr || is_null($mr)) return $atl;
		while($row = $mr->fetch_assoc()) {
			$a = new AtletaAffiliato();
			$a->carica($row);
			$atl[$a->getChiave()] = $a;
		}
		return $atl;
	}
	
	public static function getCinture($idsoc,$idaff) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$mr = $conn->select("tipi_tesserati t INNER JOIN tesserati a USING(idtesserato)", 
				"a.idsocieta='$idaff' AND idtipo=".self::TIPO_ATL_FIAM, "t.idtesserato, t.idgrado");
		$cin = array();
		if (is_null($mr)) return $cin;
		while($row = $mr->fetch_assoc()) {
			$cin[$row["idtesserato"]] = self::convertiCintura($row["idgrado"]);
		}
		return $cin;
	}
	
	/**
	 * @param string $testo
	 * @param int $escludi id societa da escludere
	 * @return array formato "atl" => AtletaAffiliato, "soc" => string (nome societa)
	 */
	public static function ricerca($testo, $escludi=NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		/* @var $connint Connessione */
		$connint = $GLOBALS["connint"];
		$connint->connetti();
		
		$tok = explode(' ', $testo);
		$where = '1';
		foreach ($tok as $t) {
			$where .= " AND ";
			$t = $conn->quote($t);
			$where .= " ricerca LIKE '% $t %'";
		}
		$anno = AnnoSportivoFiam::get();
		$mr = $conn->query("SELECT * FROM (SELECT a.idtesserato, cognome, nome, "
			."CONCAT(' ', nome, ' ', cognome, ' ') AS ricerca, a.idsocieta, "
			."sesso, data_nascita, t.idgrado FROM tesserati a INNER JOIN tipi_tesserati t USING(idtesserato) "
			." INNER JOIN pagamenti_correnti p USING(idtesserato,idtipo) WHERE idtipo=".self::TIPO_ATL_FIAM
			.") t WHERE $where ORDER BY cognome, nome");
		$atl = array();
		
		
		if (!is_object($mr)) return $atl;
		while($row = $mr->fetch_assoc()) {
			$mrs = $connint->select("societa", "idaffiliata = '$row[idsocieta]' LIMIT 1", "idsocieta, nome");
			$rs = $mrs->fetch_assoc();
			if (!$rs) continue;
			if ($escludi == $rs["idsocieta"]) continue;
			$row["idsocieta"] = $rs["idsocieta"];
			$nomesoc = $rs["nome"]; 
			
			unset($row["ricerca"]);
			
			$a = new AtletaAffiliato();
			$a->carica($row);
			$atl[$a->getChiave()]["atl"] = $a;
			$atl[$a->getChiave()]["soc"] = $nomesoc;
		}
		return $atl;
	} 
	
	public function __construct($id=NULL) {
		parent::__construct("", "idtesserato", $id, $GLOBALS["connest"]);
	}
	
	public function carica($row=NULL) {
		if (!is_null($row)) {
			parent::carica($row);
			return;
		}
		if (is_null($this->getChiave())) return;
		$id = $this->getChiave();
		$mr = $conn->query("SELECT a.idtesserato, cognome, nome, sesso, data_nascita, idgrado, '$idsoc' AS idsocieta "
				." FROM tesserati a INNER JOIN tipi_tesserati t USING(idtesserato) "
				." INNER JOIN pagamenti_correnti p USING(idtesserato,idtipo) WHERE a.idtesserato = '$id';");
		$row = $mr->fetch_assoc();
		if (!is_null($row)) parent::carica($row);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCognome() {
		return $this->get("cognome");
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSesso() {
		return $this->get("sesso");
	}

	/**
	 * @access public
	 * @return DateTime
	 */
	public function getDataNascita() {
		return $this->getData("data_nascita");
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getCintura() {
		return self::convertiCintura($this->get("idgrado"));
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	public function isVerificato() {
		//TODO prendere giorno gara
		if ($this->verificato === NULL) {
			$conn = $GLOBALS["connest"];
			$conn->connetti();
			$anno = AnnoSportivoFiam::get();
			
			//verifica pagamento
			$id = $this->getChiave();
			$mr = $conn->select('pagamenti_correnti',
					"idtesserato='$id' AND YEAR(scadenza) >= $anno AND idtipo=".self::TIPO_ATL_FIAM);
			if ($mr->fetch_row() === NULL) {
				$this->verificato = false;
			} else {
				//verifica assicurazione
				$mr = $conn->select('assicurazioni_correnti',
					"idtesserato='$id' AND YEAR(valido_da) <= $anno AND YEAR(valido_a) >= $anno");
				$this->verificato = ($mr->fetch_row() !== NULL);
			}
		}
		return $this->verificato;
	}
	
	public function getUrlCintura() {
		return AffiliazioneFiam::getUrlCambioCintura($this->getChiave());
	}
	
	public function getUrlDettagli() {
		return AffiliazioneFiam::getUrlAtleta($this->getChiave());
	}
	
}
?>