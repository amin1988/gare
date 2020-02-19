<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");
include_class("Foto");


class Coach extends Modello {
	private $foto = NULL;
	/**
	 * @param int $idgara
	 * @param int $idsocieta o NULL per leggere l'elenco di tutti i coach della gara
	 * @return Coach[][] formato: idsocieta => Coach[] se idsocieta != NULL, altrimenti Coach[]
	 */
	public static function lista($idgara, $idsocieta=NULL) {
		$allsoc = is_null($idsocieta);
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$where = "idgara = '$idgara'";
		if (!$allsoc)
			$where .= " AND idsocieta = '$idsocieta'";
		$mr = $conn->select("coach", $where);
		if (is_null($mr)) return array();
		$ret = array();
		while ($row = $mr->fetch_assoc()) {
			$c = new Coach();
			$c->carica($row);
			if ($allsoc) 
				$ret[$c->getSocieta()][$c->getChiave()] = $c;
			else
				$ret[$c->getChiave()] = $c;
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
	public static function creaCompleto($idgara, $ids, $idp, $tipo) {
		$c = new Coach();
		$c->set("idgara", $idgara);
		$c->set("idsocieta", $ids);
		$c->set("idpersona", $idp);
		$c->set("tipo", $tipo);
		return $c;
	}
	
	/**
	 * @param int $id
	 */
	public function __construct($id=NULL) {
		parent::__construct("coach", "idcoach", $id);
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
		return $this->get("idpersona");
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
		return $this->get("tipo");
	}
	
	private function getFotoObj() {
		if ($this->foto === NULL) $this->foto = Foto::coach($this);
		return $this->foto;
	}
	
	
	public function haFoto() {
		return $this->getFotoObj()->esiste();
	}
	
	/**
	 * @param boolean $default true o niente per avere l'immagine
	 * di default se manca la foto, false per avere sempre il percorso personale
	 * @return string il percorso relativo dell'immagine
	 */
	public function getFoto($default=true) {
		return $this->getFotoObj()->getFoto($default);
	}
}
