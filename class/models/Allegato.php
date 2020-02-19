<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

define("_ALLEGATO_SUBDIR_", "garedoc/");

class Allegato extends Modello {
	/**
	 * @param $idgara
	 * @return Allegato[]
	 */
	public static function listaAllegati($idgara) {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("allegati", "idgara = '$idgara'");
		$all = array();
		while($row = $mr->fetch_assoc()) {
			$a = new Allegato();
			$a->carica($row);
			$all[$a->getChiave()] = $a;
		}
		return $all;
	}
	
	/**
	 * @param int $idgara
	 * @param string $nome
	 * @param string $file il nome del file senza percorso
	 */
	public static function nuovo($idgara, $nome, $file) {
		$a = new Allegato();
		$a->set("idgara", $idgara);
		$a->set("nome", $nome);
		$a->set("url", $file);
		return $a;
	}
	
	public function __construct($id=NULL) {
		parent::__construct("allegati", "idallegato", $id);
	}
	
	/**
	 * @return string
	 */
	public function getNome() {
		return $this->get("nome");
	}
	
	public function setNome($nome) {
		$this->set("nome", $nome);
	}
	
	public function getNomeFile() {
		return $this->get("url");
	}
	
	/**
	 * @return string
	 */
	public function getUrl() {
		return _ALLEGATO_SUBDIR_.$this->get("idgara")."_".$this->get("url").".file";
	}
	
	public function getTipo() {
		$est = pathinfo($this->getNomeFile(),PATHINFO_EXTENSION);
		switch ($est) {
			case "pdf":
				return "PDF.gif";
			case "zip":
				return "zip.gif";
			case "jpg":
			case "gif":
			case "png":
				return "images.gif";
			case "ppt":
			case "pptx":
			case "pps":
				return "PP.gif";
			case "xls":
			case "xlsx":
				return "excel.gif";
			case "doc":
			case "docx":
				return "Word.gif";
			default:
				return "default.png";
		}
	}
	
	public function getMime() {
		$est = pathinfo($this->getNomeFile(),PATHINFO_EXTENSION);
		switch ($est) {
			case "pdf":
				return "application/pdf";
			case "zip":
				return "application/zip";
			case "jpg":
				return "image/jpeg";
			case "gif":
				return "image/gif";
			case "png":
				return "image/png";
			case "pps":
			case "ppt":
				return "application/vnd.ms-powerpoint";
			case "pptx":
				return "application/vnd.openxmlformats-officedocument.presentationml.presentation";
			case "xls":
				return "application/vnd.ms-excel";
			case "xlsx":
				return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
			case "doc":
				return "application/vnd.msword";
			case "docx":
				return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
			default:
				return NULL;
		}
	}
}
?>