<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");
include_class("Foto");


class Arbitro extends Modello {
	private $foto = NULL;
	/**
	 * @param int $idgara
	 * @param int $idsocieta o NULL per leggere l'elenco di tutti gli arbitri della gara
	 * @return Arbitro[][] formato: idsocieta => Arbitro[] se idsocieta != NULL, altrimenti Arbitro[]
	 */
	public static function lista($idgara, $idsocieta=NULL, $conf=NULL) {
		$allsoc = is_null($idsocieta);
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$where = "idgara = '$idgara'";
		if (!$allsoc)
			$where .= " AND idsocieta = '$idsocieta'";
		if($conf !== NULL)
			$where .= " AND confermato = '$conf'";
		$mr = $conn->select("arbitro", $where);
		if (is_null($mr)) return array();
		$ret = array();
		while ($row = $mr->fetch_assoc()) {
			$a = new Arbitro();
			$a->carica($row);
			if ($allsoc) 
				$ret[$a->getSocieta()][$a->getChiave()] = $a;
			else
				$ret[$a->getChiave()] = $a;
		}
		return $ret; 
	}
	
	/**
	 * Valido solo per arbitri esterni - WKC
	 * @param int $idsocieta
	 * @return Arbitro[]
	 */
	public static function elencoSoc($idsocieta,$idgara) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$where = "idsocieta = '$idsocieta'";
		$mr = $conn->select("arbitriesterni", $where);
		if (is_null($mr)) return array();
		$ret = array();
		$count = 0;
		while ($row = $mr->fetch_assoc()) {
			$a = new Arbitro();
			$a->setGara($idgara);
			$a->setPersona($row["idarbitro"]);
			$a->setSocieta($idsocieta);
			$a->setSocietaAff(NULL);
			$a->setTurni(1);
			$a->salva();
			$ret[$row["idarbitro"]] = $a;
// 			$ret[$a->getChiave()] = $a;
			$count++;
		}
		if($count == 0)
			return NULL;
		
		return $ret;
	}
	
	/**
	 * @param int $idgara
	 * @param Persona $p
	 */
	public static function crea($idgara, $val) {
		if (isset($val["persona"])) {
			$p = $val["persona"];
			return self::creaCompleto($idgara, $p->getSocieta(), $p->getChiave(), $p->getTipo());
		} else 
			return self::creaCompleto($idgara, $val["ids"], $val["idp"], $val["tipo"]);
// 		$c = new Coach();
// 		$c->set("idgara", $idgara);
// 		$c->set("idsocieta", $p->getSocieta());
// 		$c->set("idpersona", $p->getChiave());
// 		$c->set("tipo", $p->getTipo());
// 		return $c;
	}
	
	/**
	 * @param int $idgara
	 * @param Persona $p
	 */
	public static function creaCompleto($idgara, $ids, $idp) {
		$a = new Arbitro();
		$a->set("idgara", $idgara);
		$a->set("idsocieta", $ids);
		$a->set("idtesserato_aff", $idp);
		$a->set("tipo", 3);
		return $a;
	}
	
	public static function eliminaConv($id_gara,$idtess_aff)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$conn->query("DELETE FROM arbitro WHERE idgara=$id_gara AND idtesserato_aff=$idtess_aff");
	}
	
	/**
	 * @param int $id
	 */
	public function __construct($id=NULL) {
		parent::__construct("arbitro", "idarbitro", $id);
	} 
	
	/**
	 * @return int
	 */
	public function getGara() {
		return $this->get("idgara");
	}
	
	/**
	 * @return int
	 */
	public function getPersona() {
		return $this->get("idtesserato_aff");
	}
	
	/**
	 * @return int
	 */
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	/**
	 * @return int
	 */
	public function getTipo() {
		return 3;
	}
	
	public function getTurni() {
		return $this->get("turni");
	}
	
	public function conferma($val) {
		$this->set("confermato", $val);
	}
	
	public function setGara($val)
	{
		$this->set("idgara", $val);
	}
	
	public function setPersona($val)
	{
		$this->set("idtesserato_aff", $val);
	}
	
	public function setSocieta($val)
	{
		$this->set("idsocieta", $val);
	}
	
	public function setSocietaAff($val)
	{
		$this->set("idsocieta_aff", $val);
	}
	
	public function setTurni($val)
	{
		$this->set("turni", $val);
	}
	
}
