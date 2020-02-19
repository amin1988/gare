<?php
if (!defined("_BASEDIR_")) exit();
include_model("Squadra");
include_errori("VerificaErrori");

class VerificaSquadra extends VerificaErrori {
	const POCHI_COMP = "min";
	const TROPPI_COMP = "max";
	const PREST = "prestiti";
	const PREST_USATO = "prestiti"; //TODO cambiare
	const NO_CAT = "nocat";
	const MULTI_CAT = "multicat";
	const COMP = "nocat"; //TODO cambiare
		
	private $err;
	private $min,$max;
	
	public function __construct($idgara, $idsquadra) {
		$this->err = array();
		if (!isset($_POST["pageid"])) return;
		
		if (!$this->checkEsiste("tipo")) return;
		
		Squadra::getLimiti($_POST["tipo"], $this->min, $this->max);
		
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		if (isset($_POST["comp"])) {
			$num = count($_POST["comp"]);
			//controllo componenti validi
			foreach ($_POST["comp"] as $idc) {
				if (!$this->checkComp($conn, $idc, $idgara, $idsquadra))
					break;
			}
		} else {
			$num = 0;
		}
		
		if (isset($_POST["pres"])) {
			if (count($_POST["pres"]) > 1) {
				$this->err[] = self::PREST;
			} else {
				$this->checkPrestito($conn, $idgara, $idsquadra);
			}
			$num++;
		}
		
		if ($num < $this->min) {
			$this->err[] = self::POCHI_COMP;
		} else if ($num > $this->max) {
			$this->err[] = self::TROPPI_COMP;
		}
	}
	
	public function haErrori() {
		return count($this->err) > 0;
	}
	
	public function isErrato($campo) {
		return in_array($campo, $this->err);
	}
	
	private function checkEsiste($campo) {
		$val = isset($_POST[$campo]);
		if (!$val)
			$this->err[] = $campo;
		return $val;
	}
	
	/**
	 * @param Squadra $squadra
	 */
	public function checkCategoria($squadra) {
		if ($squadra->inCategoria()) return;
		
		if (is_null($squadra->getMultiCategorie()))
			$this->err[] = self::NO_CAT;
		else
			$this->err[] = self::MULTI_CAT;
	}
	
	private function checkComp($conn, $idc, $idgara, $idsq) {
		//verifica che non sia iscritto ad altre squadre dello stesso tipo
		$tipo = $conn->quote($_POST['tipo']);
		$where = "idgara='$idgara' AND idatleta='$idc' AND tipogara='$tipo'";
		if ($idsq !== NULL) $where .= " AND s.idsquadra != '$idsq'";
		$mr = $conn->select('componentisquadre c INNER JOIN squadre s USING(idsquadra) INNER JOIN categorie k USING(idcategoria)',
				$where);
		if ($mr->fetch_row()) {
			//iscritto ad altre squadre
			$this->err[] = self::COMP;
			return false;
		}
		return true;
	}
	
	/**
	 * Verifica che l'atleta non sia in un'altra squadra
	 * @param Connessione $conn
	 */
	private function checkPrestito($conn, $idgara, $idsquadra) {
		//TODO eliminare duplicazione con ajax/cercaatl.php
		
		$ida = current($_POST["pres"]);
		$tipo = intval($_POST['tipo']);
		
		$where = "idgara = '$idgara' AND idatleta = '$ida' AND tipogara = '$tipo'";
		if ($idsquadra !== NULL)
			$where .= " AND s.idsquadra != '$idsquadra'";
		
		//TODO ottimizzare mettendo tipo gara in squadre?
		$mr = $conn->select("squadre s INNER JOIN componentisquadre c USING(idsquadra)".
				"INNER JOIN categorie USING(idcategoria)", 
				"$where LIMIT 1",
				 "idsocieta");
		if ($mr->fetch_row()) {
			//già iscritto ad una squadra dello stesso tipo
			$this->err[] = self::PREST_USATO; 
		}
	}
	
	public function toString() {
		$err = $this->toStringInner($this->err, "#errsq_");
		if (in_array(self::POCHI_COMP, $this->err) || in_array(self::TROPPI_COMP, $this->err))
			$err = str_replace(array("<MINTEAM>","<MAXTEAM>"), array($this->min, $this->max), $err);
		return $err;
	}
	
}