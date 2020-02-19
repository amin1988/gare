<?php
if (!defined("_BASEDIR_")) exit();

class Menu {
	private static $verifica;
	/**
	 * @var PaginaMenu[] formato: nome => pagina
	 */
	private static $pag = NULL;
	/**
	 * @var PaginaMenu[][] formato: gruppo => PaginaMenu[]
	 */
	private static $schede;
		
	/**
	 * Indica se è stato caricato un menu
	 * @return boolean 
	 */
	public static function caricato() {
		return self::$pag != NULL;
	}
	
	/**
	 * @param string $pagina o NULL per la pagina attuale
	 * @return PaginaMenu o NULL se la pagina non è nel menu
	 */
	public static function getPagina($pagina=NULL) {
		if ($pagina == NULL)
			$pagina = self::getPaginaAttiva();
		if (isset(self::$pag[$pagina]))
			return self::$pag[$pagina];
		else
			return NULL;
	}
	
	/**
	 * @param string $pagina il nome della pagina
	 * @return string il titolo della pagina o NULL se la pagina non ha padre
	 */
	public static function getTitolo($pagina=NULL) {
		$p = self::getPagina($pagina);
		if ($p == NULL)
			return NULL;
		else
			return $p->getTitolo();
	}
	
	/**
	 * @param string $pagina
	 * @return PaginaMenu[] o NULL se la pagina non è in un gruppo
	 */
	public static function getSchede($pagina=NULL) {
		$p = self::getPagina($pagina);
		if ($p == NULL || $p->getGruppo() == NULL)
			return NULL;
		$ret = array();
		foreach (self::$schede[$p->getGruppo()] as $sp) {
			/* @var $sp PaginaMenu */
			if (!$sp->isOpzionale() || self::$verifica == NULL
					|| self::$verifica->verificaPagina($sp)) 
			{
				$ret[] = $sp;
			}
		}
		return $ret;
	}
	
	public static function addPagina($pagina, $nome, $padre, $query) {
		self::addInner($pagina, $nome, $padre, NULL, $query, NULL);
	}
	
	public static function addScheda($pagina, $nome, $padre, $gruppo, $info=NULL) {
		self::addInner($pagina, $nome, $padre, $gruppo, true, $info);
	}
	
	/**
	 * @param string $pagina
	 * @param string $nome
	 * @param string $padre
	 * @param string $gruppo
	 * @param boolean $opt
	 */
	private static function addInner($pagina, $nome, $padre, $gruppo, $query, $info) {
		$p = new PaginaMenu($pagina, $nome, $padre, $gruppo, $query, $info);
		self::$pag[$pagina] = $p;
		if ($gruppo !== NULL)
			self::$schede[$gruppo][] = $p;
	}
	
	/**
	 * @param unknown_type $verifica la classe per verificare le pagine opzionali
	 */
	public static function setVerificaOpzionale($verifica) {
		self::$verifica = $verifica;
	}
	
	public static function getPaginaAttiva() {
		return basename($_SERVER['SCRIPT_FILENAME']);
	}
}

class PaginaMenu {
	private $titolo;
	private $url;
	private $padre;
	private $gruppo;
	private $info;
	private $query;
	
	public function __construct($url, $titolo, $padre, $gruppo, $query, $info) {
		$this->url = $url;
		$this->titolo = $titolo;
		$this->padre = $padre;
		$this->gruppo = $gruppo;
		$this->query = $query;
		$this->info = $info;
	}
	
	public function isAttiva() {
		return $this->url == Menu::getPaginaAttiva();
	}
	
	public function getUrl() {
		return $this->url;
	}
	public function getTitolo() {
		return $this->titolo;
	}
	public function getPadre() {
		return $this->padre;
	}
	public function getGruppo() {
		return $this->gruppo;
	}
	public function isOpzionale() {
		return $this->info !== NULL;
	}
	public function usaQuery() {
		return $this->query;
	}
	public function getInfo() {
		return $this->info;
	}
}