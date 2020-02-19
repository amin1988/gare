<?php
if (!defined("_BASEDIR_")) exit();
include_model("Iscritto", "Categoria");
include_class("Sesso");

class Squadra extends Iscritto {	
	/**
	 * @var int[] formato: idatleta => idcintura
	 */
	private $cinture = NULL;
	/**
	 * @var boolean
	 */
	private $modcomp = false;
	
	/**
	 * @var boolean
	 */
	private $inCat = true;
	/**
	 * @var Categoria[] se la squadra apaprtiene a pi� categorie
	 */
	private $multiCat = NULL;

	/**
	 * Scrive in $min e $max i limiti per il numero di componenti di una squadra
	 * @param int $tipo tipo gara
	 * @param int $min componenti minimi
	 * @param int $max componenti massimi
	 */
	public static function getLimiti($tipo, &$min, &$max) {
		if(_WKC_MODE_)
		{
			if ($tipo == 0) {
				//kata
				$min = 3;
				$max = 3;
			} else {
				//sanbon e ippon
				$min = 2;
				$max = 4;
			}
		}
		else 
		{
			if ($tipo == 0) {
				//kata
				$min = 3;
				$max = 4;
			} else {
				//sanbon e ippon
				$min = 2;
				$max = 5;
			}
		}
	}
	
	public static function listaSocieta($idsoc, $idgara)  {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("squadre", "idsocieta = '$idsoc' AND idgara = '$idgara' ORDER BY numero");
		$res = array();
		while ($row = $mr->fetch_assoc()) {
			$s = new Squadra();
			$s->carica($row);
			$res[$s->getChiave()] = $s;
		}
		return $res;
	}

	public static function accorpa($idgara, $idcat, $idacc) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($idacc))
			$conn->query("UPDATE squadre SET idaccorpamento = NULL WHERE idgara = '$idgara' AND idcategoria = '$idcat'");
		else
			$conn->query("UPDATE squadre SET idaccorpamento = '$idacc' WHERE idgara = '$idgara' AND idcategoria = '$idcat'");
	}
	
	/**
	 * @param int $idgara
	 * @param int $idcat se != NULL restituisce solo le squadre in una categoria
	 * @return Squadra[]
	 */
	public static function listaGara($idgara, $idcat = NULL)  {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		if (is_null($idcat))
			$where = "";
		else 
			$where = "idcategoria = '$idcat' AND ";
		$mr = $conn->select("squadre", "$where idgara = '$idgara'");
		$res = array();
		while ($row = $mr->fetch_assoc()) {
			$s = new Squadra();
			$s->carica($row);
			$res[$s->getChiave()] = $s;
		}
		return $res;
	}
	
	/**
	 * @param int $idgara
	 * @param int $idsocieta
	 * @param Squadra $squadra
	 * @return array formato idatleta => tipogara
	 */
	public static function altreIscrizioni($idgara, $idsocieta, $squadra = NULL) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$where = "idgara='$idgara' AND idsocieta='$idsocieta'";
		if ($squadra !== NULL)
			$where .= " AND s.idsquadra != '".$squadra->getChiave()."'";
		$mr = $conn->select("categorie k INNER JOIN squadre s ON k.idcategoria=s.idcategoria INNER JOIN componentisquadre c ON s.idsquadra = c.idsquadra",
				$where, "s.idsquadra, idatleta, tipogara");
		$res = array();
		while($row = $mr->fetch_assoc()) {
			$res[$row["idatleta"]][$row["tipogara"]] = $row["tipogara"];
		}
		return $res;	
	}
	
	/**
	 * @param Gara $gara
	 * @param array $dati formato: "comp"=>Atleta[], "tipo"=>int, "societa"=>int
	 * "cinture"=>array(idatleta=>idcintura), opzionale "categoria"=>int
	 * @return Squadra
	 */
	public static function nuovo($gara, $dati) {
		$sq = new Squadra();
		$sq->set("idgara", $gara->getChiave());
		$sq->set("idsocieta", $dati["societa"]);
		if (isset($dati["categoria"])) 
			$catpost = $dati["categoria"];
		else 
			$catpost = -1;
		$sq->calcolaCategoria($gara, $dati["comp"], $dati["tipo"], $catpost);
		foreach ($dati["comp"] as $a) {
			/* @var $a Atleta */
			$ida = $a->getChiave();
			$comp[] = $ida;
			if (!isset($dati["cinture"][$ida]))
				$dati["cinture"][$ida] = $a->getCintura();
		}
		$sq->setComponenti($comp, $dati["cinture"]);
		return $sq;
	}
	
	/**
	 * @param Gara $gara
	 * @param Atleta[] $comp
	 */
	public function calcolaCategoria($gara, $comp, $tipo, $post) {
		$this->set("idcategoria", NULL);
		//generazione caratteristiche squadra
		$etamin = 500;
		$etamax = 0;
		$sesso[Sesso::M] = 0;
		$sesso[Sesso::F] = 0;
		$data = $gara->getDataGara();
		$cinture = array();
		foreach ($comp as $a) {
			/* @var $a Atleta */
			$eta = $a->getEta($data);
			if ($eta > $etamax) $etamax = $eta;
			if ($eta < $etamin) $etamin = $eta;
			$cint = $a->getCintura();
			$cinture[$cint] = $cint;
			$sesso[$a->getSesso()]++;
		}
		/*if ($sesso[Sesso::M]) {
			if ($sesso[Sesso::F]) 
				$sesso = Sesso::MISTO;
			else
				$sesso = Sesso::M;
		} else {
			$sesso = Sesso::F;
		}*/
		if ($post != -1) {
			$c = new Categoria($post);
			if ($c->esiste() &&
					$c->squadraInCategoria($sesso, $etamin, $etamax, $tipo, $cinture)) {
				//la categoria suggerita va bene
				$this->set("idcategoria", $post);
				return;
			}
			//la categoria nno va bene, cercane un'altra
		}
		//ricerca delle categorie
		$cat = array();
		foreach ($gara->getCategorieSquadre() as $c) {
			/* @var $c Categoria */
			if ($c->squadraInCategoria($sesso, $etamin, $etamax, $tipo, $cinture))
				$cat[] = $c;
		}
		if (count($cat) == 0) {
			$this->inCat = false;
		} else if (count($cat) > 1) {
			$this->inCat = false;
			$this->multiCat = $cat;
		} else {
			$this->set("idcategoria", $cat[0]->getChiave());
		}
		
	}
	
	public function __construct($id=NULL) {
		parent::__construct("squadre", "idsquadra",$id);
	}
	
	public function inCategoria() {
		return $this->inCat;
	}
	
	public function getMultiCategorie() {
		return $this->multiCat;
	}
	
	public function getNumero() {
		return $this->get("numero");
	}
	
	/**
	 * @return int[]
	 */
	public function getComponenti() {
		//if ($this->getLista("componenti")) $this->caricaComponenti();
		return $this->getLista("componenti");
	}
	
	/**
	 * @param int $idatleta
	 * @return int
	 */
	public function getCinturaComponente($idatleta) {
		if (is_null($this->cinture)) $this->caricaLista("componenti");
		if (!isset($this->cinture[$idatleta])) return NULL;
		return $this->cinture[$idatleta];
	}
	
	/**
	 * @param int[] $atleti
	 * @param int[] $cinture formato idatleta => idcintura
	 */
	public function setComponenti($atleti, $cinture) {
		$this->setLista("componenti", $atleti);
		$this->cinture = $cinture;
	}
	
	protected function caricaLista($nome) {
		if ($nome != "componenti") parent::caricaLista($nome);
		$id = $this->getChiave();
		$mr = $this->_connessione->select("componentisquadre", "idsquadra = '$id'");
		$comp = array();
		$this->cinture = array();
		while($row = $mr->fetch_assoc()) {
			$comp[] = $row["idatleta"];
			$this->cinture[$row["idatleta"]] = $row["idcintura"];
		}
		return $comp;
	}
	
	public function salva() {
		$lock = !$this->hasChiave();
		if ($lock) {
			$ids = $this->get("idsocieta");
			$idg = $this->get("idgara");
			$this->_connessione->query("LOCK TABLES squadre;");
			$row = $this->_connessione->select("squadre", 
					"idsocieta='$ids' AND idgara='$idg'","MAX(numero)")->fetch_row();
			if (is_null($row[0]))
				$num = 1;
			else
				$num = intval($row[0])+1;
			$this->set("numero", $num);
		}
		parent::salva();
		if ($lock)
			$this->_connessione->query("UNLOCK TABLES;");
	}
	
	protected function insertLista($nome, $valori) {
		if ($nome != "componenti") return;
		$sql = false;
		$id = $this->getChiave();
		foreach ($valori as $v) {
			$v = $this->_connessione->quote($v);
			$c = $this->_connessione->quote($this->cinture[$v]);
			if ($sql === false)
				$sql = "('$id', '$v', '$c')";
			else
				$sql .= ",('$id', '$v', '$c')";
		}
		$sql = "INSERT INTO componentisquadre (idsquadra, idatleta, idcintura) VALUES $sql;";
		$this->_connessione->conn()->query($sql);
	}
	
	protected function updateLista($nome, $valori) {
		if ($nome != "componenti") return;
		$id = $this->getChiave();
		$this->_connessione->query("DELETE FROM componentisquadre WHERE idsquadra = '$id';");
		$this->insertLista($nome, $valori);
	}
	
	public function elimina() {
		$id = $this->getChiave();
		$this->_connessione->query("DELETE FROM componentisquadre WHERE idsquadra='$id'");
		$this->_connessione->query("DELETE FROM prestiti WHERE idsquadra='$id'");
		parent::elimina();
	}
}