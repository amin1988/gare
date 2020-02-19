<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello","Categoria");

class GruppoCat extends Modello {
	/**
	 * @var Categoria[] formato: idcategoria => Categoria
	 */
	private $categ = NULL;
	private $catmod = false;
	
	/**
	 * 
	 * @param int $id
	 * @return GruppoCat
	 */
	public static function fromId($id) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("gruppicat","idgruppo='$id'");
		$row = $mr->fetch_assoc();
	
		if($row !== NULL)
		{
			$g = new GruppoCat();
			$g->carica($row);
			return $g;
		}
		else
			return NULL;
	}
	
	/**
	 * @param int[] $listaid
	 */
	public static function lista($listaid=NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($listaid)) {
			$where = "1";
		} else {
			$where = "idgruppo IN " . $conn->flatArray($listaid);
		}
		$mr = $conn->select("gruppicat",$where." ORDER BY nome");
		$res = array();
		while ($row = $mr->fetch_assoc()) {
			$g = new GruppoCat();
			$g->carica($row);
			$res[$g->getChiave()] = $g;
		}
		return $res;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("gruppicat", "idgruppo", $id);
	}
	
	/**
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	/**
	 * @param string $nome
	 */
	public function setNome($nome) {
		$this->set("nome", $nome);
	}
	
	/**
	 * @return boolean
	 */
	public function isIndividuale() {
		return $this->getBool("individuale");
	}
	
	/**
	 * @param boolean $valore
	 */
	public function setIndividuale($valore) {
		$this->setBool("individuale", $valore);
	}
	
	/**
	 * @return Categoria[]
	 */
	public function getCategorie() {
		if (is_null($this->categ)) {
			$this->categ = Categoria::listaGruppo($this->getChiave());
			$this->catmod = false;
		}
		return $this->categ;
	}
	
	/**
	 * @param Categoria[] $categ
	 */
	public function setCategorie($categ) {
		//TODO no, vanno modificate le categorie
		$this->categ = $categ;
		$this->catmod = true;
	}
}