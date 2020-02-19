<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
class Zona extends Modello {
	/**
	 * @var Zona[]
	 */
	private static $zone = NULL;
	
	/**
	 * @var Zona[]
	 */
	private $superiori = NULL;

	/**
	 * @param int[]
	 * @return Zona[]
	 */
	public static function listaZone($lista) {
		$vuoto = is_null(self::$zone);
		if ($vuoto) 
			$listanew = $lista;
		else
			$listanew = array_intersect($lista, array_keys(self::$zone));
		if (count($listanew) > 0){
			$conn = $GLOBALS["connint"];
			$conn->connetti();
			$mr = $conn->select("zone", "idzona IN ".$conn->flatArray($listanew));
			$zone = array();
			while($row = $mr->fetch_assoc()){
				$z = new Zona();
				$z->carica($row);
				self::$zone[$z->getChiave()] = $z;
			}
		}
		if ($vuoto) 
			return self::$zone;
		else
			return array_intersect_key(self::$zone, array_flip($lista));
	}
	
	/**
	 * Restituisce le sottozone dirette di una zona
	 * @param  int $id
	 * @return Zona[]
	 */
	public static function getSottozone($id) {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("zone","padre = '$id' ORDER BY nome");
		$ret = array();
		while($row = $mr->fetch_assoc()){
			$z = new Zona();
			$z->carica($row);
			$ret[$z->getChiave()] = $z;
		}
		return $ret;
	} 
	
	/**
	 * Restituisce le zone appartenenti ad un certo livello
	 * @param  int $idlivello
	 * @return Zona[]
	 */
	public static function listaLivello($idlivello) {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("zone","idlivello = '$idlivello' ORDER BY nome");
		$ret = array();
		while($row = $mr->fetch_assoc()){
			$z = new Zona();
			$z->carica($row);
			$ret[$z->getChiave()] = $z;
		}
		return $ret;
	}
	
	/**
	 * @param int $id
	 * @return Zona
	 */
	public static function getZona($id) {
		if (is_null($id)) return NULL;
		if (isset(self::$zone[$id]))
			return self::$zone[$id];
		$z = new Zona($id);
		self::$zone[$id] = $z;
		return $z;
	}

	/**
	 * @access private
	 * @param int $id
	 */
	public function __construct($id = NULL) {
		parent::__construct("zone","idzona",$id);
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
	 * @param string $nome
	 */
	public function setNome($nome) {
		$this->set("nome", $nome);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getLivello() {
		return $this->get("idlivello");
	}

	/**
	 * @access public
	 * @param int $idlivello
	 */
	public function setLivello($idlivello) {
		$this->set("idlivello", $idlivello);
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getPadre() {
		return $this->get("padre");
	}

	/**
	 * @access public
	 * @param int $idpadre
	 */
	public function setPadre($idpadre) {
		$this->set("padre", $idpadre);
	}
	
	/**
	 * @access public
	 * @return Zona[]
	 */
	public function getSuperiori() {
		//TODO fare ricorsivo
		if (!is_null($this->superiori)) return $this->superiori;
		$this->superiori = array();
		$idz = $this->get("padre");
		while($idz) {
			$z = self::getZona($idz);
			$this->superiori[$idz] = $z;
			$idz = $z->get("padre");
		}
		return $this->superiori;
	}
	
}
?>