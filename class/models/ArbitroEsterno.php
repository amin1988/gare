<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");
include_class("Foto");


class ArbitroEsterno extends Modello {
	
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
			$ar = new Arbitro();//Coach();
			$ar->carica($row);
			if ($allsoc)
				$ret[$ar->getSocieta()][$ar->getPersona()] = $ar;
			else
				$ret[$ar->getPersona()] = $ar;
		}
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
	}
	
	/**
	 * @param int $idgara
	 * @param Persona $p
	 */
	public static function creaCompleto($idgara, $ids, $idp, $tipo) {
		$c = new Coach();
		$c->set("idgara", $idgara);
		$c->set("idsocieta", $ids);
		$c->set("idpersona", $idp);
		$c->set("tipo", $tipo);
		return $c;
	}
	
/**
	 * Restituisce gli id degli arbitri convocati per una gara e non ancora confermati
	 * @param int $idgara
	 * @param int $idsoc
	 * @return int[]
	 */
	public static function getConvocatiGara($idgara,$idsoc=NULL,$conf=NULL)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$where = '';
		
		if($idsoc !== NULL)
			$where .=  " AND idsocieta = $idsoc ";
		
		if($conf !== NULL)
			$where .= " AND confermato = $conf";
		
		$ar_arb = array();
		
		$mr = $conn->select("arbitro","idgara='$idgara' $where","idarbitro, idtesserato_aff");
		
		while($row = $mr->fetch_assoc())
		{
			$ar_arb[$row['idarbitro']] = $row['idtesserato_aff'];
		}
		
		return $ar_arb;
	}
	
	/**
	 * Valido solo per elenco arbitri
	 * @param array $dati formato:
	 * 			"societa"=>int,
	 * 			"nome"=>string,
	 * 			"cognome"=>string,
	 * 			"sesso"=>int,
	 * 			"nascita"=>Data
	 * @return ArbitroEsterno
	 */
	public static function nuovo($dati) {
		$c = new ArbitroEsterno();
		$c->set("idsocieta", $dati["idsocieta"]);
		$c->set("nome", $dati["nome"]);
		$c->set("cognome", $dati["cognome"]);
		$c->set("sesso", $dati["sesso"]);
		$c->set("nascita", $dati["nascita"]);
		return $c;
	}
	
	public static function fromId($id) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("arbitriesterni","idarbitro='$id'");
		$row = $mr->fetch_assoc();
	
		if($row !== NULL)
		{
			$ar = new ArbitroEsterno();
			$ar->carica($row);
			return $ar;
		}
		else
			return NULL;
	}
	
	public static function rowFromId($id)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
	
		$mr = $conn->select('arbitriesterni',"idarbitro='$id'");
	
		return $mr->fetch_assoc();
	}
	
	public static function getListaArbEst($id_soc=NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		if($id_soc !== NULL)
			$where = "idsocieta=$id_soc ";
		else
			$where = "1";
		
		$mr = $conn->select('arbitriesterni',$where);
		
		$arb = array();
		while($row = $mr->fetch_assoc())
			$arb[$row["idarbitro"]] = $row;
		
		return $arb;
	}
	
	public static function deleteArbitro($id_arb)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$conn->query("DELETE FROM arbitro WHERE idtesserato_aff='$id_arb'");
		$conn->query("DELETE FROM arbitriesterni WHERE idarbitro='$id_arb'");
	}
	
	/**
	 * @param int $id
	 */
	public function __construct($id=NULL) {
		parent::__construct("arbitriesterni", "idarbitro", $id);
	} 
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	public function setNome($val) {
		$this->set("nome", $val);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCognome() {
		return $this->get("cognome");
	}
	
	public function setCognome($val) {
		$this->set("cognome", $val);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getSesso() {
		return $this->get("sesso");
	}
	
	public function setSesso($val) {
		$this->set("sesso", $val);
	}

	/**
	 * @access public
	 * @return DateTime
	 */
	public function getDataNascita() {
		return $this->getData("nascita");
	}
	
	public function setDataNascita($data) {
		$this->setData("nascita", $data);
	}
	
	/**
	 * @return int
	 */
	public function getIDSocieta() {
		return $this->get("idsocieta");
	}
	
	public function setIDSocieta($val) {
		$this->set("idsocieta", $val);
	}
	
	public function getTipo() {
		return 1;
	}
}
