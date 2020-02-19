<?php
if (!defined("_BASEDIR_")) exit();
include_model("Arbitro_Abs");

class ArbitroAffiliato extends Arbitro_Abs {
	
	/**
	 * Restituisce i tesserati non tecnici e non cinture nere
	 * @return Persona[]
	 */
	public static function getAltriTesserati($idsoc, $idaff) {
		$where = " idsocieta='$idaff' AND idtesserato NOT IN (SELECT idtesserato FROM pagamenti_correnti WHERE idsocieta='$idaff'";
		$where .= " AND idtesserato IS NOT NULL AND idtipo NOT IN (1,2)) ORDER BY cognome, nome";
		return self::innerLista($idsoc, $where);
	}
	
	
	/**
	 * Restituisce tutti gli arbitri di una società da l'id della società affiliata
	 * se l'id è NULL allora li restituisce tutti
	 * @param int $id_soc
	 * @return (idtesserato, cognome, nome, sesso, data_nascita, idsocieta)[]
	 */
	public static function getListaArb($id_soc = NULL)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		if($id_soc !== NULL)
			$where = " AND idsocieta=$id_soc ";
		else
			$where = '';
		
		$ar_arb = array();
		
		$mr = $conn->select("pagamenti_correnti p INNER JOIN assicurazioni_correnti a ON p.idtesserato=a.idtesserato","p.idtipo=3 AND p.data_pagamento IS NOT NULL AND a.tessera IS NOT NULL","p.idtesserato");//prende tutti gli id dei tesserati arbitri pagati e assicurati
		
		$i = 0;
		$str_id = "(";
		while($row = $mr->fetch_assoc())
		{
			if($i != 0)
				$str_id .=',';
			$str_id .= $row['idtesserato'];
			$i++;
		}
		$str_id .= ')';
		
		//$campi = "idtesserato, cognome, nome, sesso, data_nascita, '$id_soc' AS idsocieta"; //perchè facevo così?
		$campi = "idtesserato, cognome, nome, sesso, data_nascita, idsocieta";
		$mr = $conn->select("tesserati","idtesserato IN $str_id $where ORDER BY cognome, nome ASC",$campi);
		
		if($mr !== NULL)
		{
			while($row = $mr->fetch_assoc())
			{
				$ar_arb[$row['idtesserato']] = $row;
			}
		}
		
		return $ar_arb;
		
		foreach($ar_arb as $id=>$row)
		{
			echo $row['idtesserato'].' - '.$row['nome'].' '.$row['cognome']."<br>";
		}
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
			$where .=  " AND idsocieta_aff = $idsoc ";
		
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
	 * 
	 * @param int $idgara
	 * @return array[int] = [int][int]
	 */
	public static function getTurni($idgara)
	{
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		
		$mr = $conn->select("arbitro","idgara='$idgara'","idtesserato_aff,turni");
		
		$ar_tur = array();
		while($row = $mr->fetch_assoc())
		{
			$ar_tur[$row['idtesserato_aff']] = $row['turni'];
		}
		
		return $ar_tur;
	}
	
	/**
	 * @param int[] $lista
	 * @return Persona[]
	 */
	public static function lista($idsoc, $lista) {
		if (is_null($lista) || count($lista) == 0)
			return array();
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		return self::innerLista($idsoc, "idtesserato IN ".$conn->flatArray($lista));
	}
	
	public static function extFromId($id)
	{
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$mr = $conn->select('tesserati',"idtesserato='$id'");
		
		return $mr->fetch_assoc();
	}
	
	private static function innerLista($idsoc, $where) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		
		$tes = array();
		
		$campi = "idtesserato, cognome, nome, sesso, data_nascita, '$idsoc' AS idsocieta";
		$mr = $conn->select("tesserati", $where, $campi);
		if (!is_null($mr)) {
			while($row = $mr->fetch_assoc()) {
				$t = new TesseratoFiam();
				$t->carica($row);
				$tes[$t->getChiave()] = $t;
			}
		}
		return $tes;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("", "idtesserato", $id, $GLOBALS["connest"]);
	}
	
	public function carica($row=NULL) {
		if (!is_null($row)) {
			parent::carica($row);
			return;
		}
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
	public function getSocieta() {
		return $this->get("idsocieta");
	}
	
	public function isVerificato() {
		return true; //TODO ci interessa che sia verificato?
	}
	
	public function getTipo() {
		return Persona::TIPO_ARBITRO;
	}
}
?>