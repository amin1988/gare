<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

class SocietaAffiliata extends Modello {
	private $_utente = NULL;
	private $idzona = NULL;
	
	/**
	 * @return array formato idaffiliata => nome
	 */
	public static function getNomi($escludi=NULL) {
		$conn = $GLOBALS["connest"];
		$conn->connetti();
		if (!is_null($escludi) && count($escludi)>0) {
			$where = "idsocieta NOT IN " . $conn->flatArray($escludi);
		} else $where = "1";
		$where .= " ORDER BY nome";
		
		$mr = $conn->select("societa",$where,"idsocieta, nome");
		$res = array();
		while($row = $mr->fetch_assoc()) {
			$res[$row["idsocieta"]] = $row["nome"];
		}
		return $res;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("societa", "idsocieta", $id, $GLOBALS["connest"]);
	}
	
	public function getNome() {
		return $this->get("nome");
	}
	
	public function getEmail() {
		return $this->get("email");
	}
	
	public function getUsername() {
		return $this->get("codice");
	}
	
	public function getPassword() {
		return 'PASSWORD NON E PIU IN CHIARO'; //TODO
	}
	
	public function getContatto() {
		return ''; //TODO
	}
	
	/**
	 * @return int 
	 */
	public function getZona() {
		if (!is_null($this->idzona)) return $this->idzona;
		/* @var $conn Connessione */
		$conn = $GLOBALS['connest'];
		$conn->connetti();
		$idcom = $this->get('idcomune');
		$mr = $conn->select('comuni',"idcomune='$idcom'",'idprovincia');
		$val = $mr->fetch_array();
		if ($val === NULL) {
			$this->idzona = 0;
			return 0;
		} else
			$prov = $val[0];
		
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("province_affiliazione","idprovincia = '$prov'","idzona");
		$val = $mr->fetch_array();
		if (is_null($val))
			$this->idzona = 0;
		else
			$this->idzona = $val[0];
		return $this->idzona;
	} 
}

?>