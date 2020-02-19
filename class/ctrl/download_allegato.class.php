<?php
if (!defined("_BASEDIR_")) exit();
include_model("Allegato");

class DownloadAllegato {
	private $file;
	private $mime;
	private $nome;
	
	/**
	 * @var Allegato
	 */
	private $allegato;
	
	public function __construct() {
		if (!isset($_GET["id"])) {
			homeutente(NULL);
			exit();
		}
		$this->allegato = new Allegato($_GET["id"]);
		if (!$this->allegato->esiste()) {
			homeutente(NULL);
			exit();
		}
		
		$this->nome = $this->allegato->getNomeFile();
		$this->mime = $this->allegato->getMime();
		$this->file = _BASEDIR_.$this->allegato->getUrl();
	}
	
	public function setHeader() {
		header('Content-Description: File Transfer');
		if (is_null($this->mime))
			header('Content-Type: application/octet-stream');
		else
			header("Content-Type: $this->mime");
		header('Content-Disposition: attachment; filename="'.$this->nome.'"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($this->file));
	}
	
	public function stampaContenuto() {
		ob_clean();
    	flush();
    	readfile($this->file);
	}
}