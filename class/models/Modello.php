<?php
if (!defined("_BASEDIR_")) exit();
require_once(_BASEDIR_."connection.inc.php");
include_class('Data');

/**
 * @access public
 * @package models
 */

class Modello {
	/**
	 * @var Connessione
	 */
	protected $_connessione = NULL;
	protected $_backup = false;
	/**
	 * @var string
	 */
	private $tabella;
	/**
	 * @var string[] formato: nome lista => tabella
	 */
	private $listeTab = array();
	/**
	 * @var string[] formato: nome lista => colonna
	 */
	private $listeCol = array();
	/**
	 * @var string
	 */
	private $chiaveCol;
	private $chiaveVal = NULL;
	/**
	 * Indica se i dati sono stati letti dal database
	 * @var boolean
	 */
	private $letto = false;
	/**
	 * riepito da carica, accessibile con get e set
	 * @var array
	 */
	private $valori = NULL;
	/**
	 * riempito se $_backup=true e letto da ripristina
	 * @var array
	 */
	private $valoribk = NULL;
	/**
	 * @var array[]
	 */
	private $liste = NULL;
	/**
	 * true se campo modificato con set, usato da update per aggiornare i campi
	 * @var boolean[]
	 */
	private $modificato;
	/**
	 * true se campo modificato con set, usato da update per aggiornare i campi
	 * @var boolean[]
	 */
	private $modliste;
	
	/**
	 * @var string[]
	 */
	private $ignora = NULL;

	/**
	 * @access public
	 * @param string $tabella
	 * @param string $chiaveCol
	 * @param $chiaveVal
	 * @param Connessione $conn
	 */
	public function __construct($tabella, $chiaveCol, $chiaveVal = NULL, $conn = NULL) {
		//se la connessione non è impostata usa quella di default
		if (is_null($conn)) $this->_connessione = $GLOBALS["connint"];
		else $this->_connessione = $conn;
		
		$this->tabella = $tabella;
		$this->chiaveCol = $chiaveCol;
		$this->_connessione->connetti();
		if (!is_null($chiaveVal))
			$this->chiaveVal = $this->_connessione->quote($chiaveVal);
	}
	
	/**
	 * Indica se questo oggetto è presente sul database
	 * @return boolean
	 */
	public function esiste() {
		if (!$this->hasChiave()) return false;
		if (!$this->letto) $this->carica();
		return $this->letto;
	}

	/**
	 * @access public
	 */
	public function getChiave() {
		return $this->chiaveVal;
	}

	/**
	 * Indica se la chiave è stata impostata
	 * @access public
	 * @return boolean
	 */
	public function hasChiave() {
		return !is_null($this->chiaveVal);
	}

	/**
	 * Salva l'oggetto sul database
	 * @access public
	 */
	public function salva() {
		if (is_null($this->chiaveVal))
			return $this->insert();
		else
			return $this->update();
	}

	/**
	 * elimina l'oggetto dal database 
	 * @access public
	 */
	public function elimina() {
		//se chaiveVal non impostato non fa niente
		if (is_null($this->chiaveVal)) return;
		$this->_connessione->conn()->query("DELETE FROM $this->tabella WHERE $this->chiaveCol = '$this->chiaveVal';");
		//elimina il valore della chiave
		$this->chiaveVal = NULL;
	}
	
	public function ripristina() {
		if (!$this->_backup || is_null($this->valoribk)) return;
		foreach ($this->valoribk as $c => $v) {
			$this->valori[$c] = $v;
			$this->modificato[$c] = false;
		}
	}

	/**
	 * @access protected
	 * @param string $colonna
	 */
	protected function get($colonna) {
		$v = $this->valori;
		if (!$this->letto && !is_null($this->chiaveVal) && !isset($v[$colonna])) {
			$this->carica();
		}
		if (isset($this->valori[$colonna]))
			return $this->valori[$colonna];
		else return NULL;
	}
	
	/**
	 * @access protected
	 * @param string $nome
	 * @return array
	 */
	protected function getLista($nome){
		$v = $this->liste;
		if (!isset($v[$nome]) && !is_null($this->chiaveVal)) {
			$this->liste[$nome] = $this->caricaLista($nome);
			$this->modliste[$nome] = false;
		}
		if (isset($this->liste[$nome]))
			return $this->liste[$nome];
		else return NULL;
	}
	
	/**
	 * @access protected
	 * @param string $colonna
	 * @return Data
	 */
	protected function getData($colonna) {
		$v = $this->get($colonna);
		if (is_null($v)) return NULL;
		return new Data($v);
	}
	
	/**
	 * @access protected
	 * @param string $colonna
	 * @return boolean
	 */
	protected function getBool($colonna) {
		$v = $this->get($colonna);
		if (is_null($v)) return NULL;
		return ($v == 1);
	}

	/**
	 * @access protected
	 * @param string $colonna
	 * @param $valore
	 */
	protected function set($colonna, $valore) {
		if (!isset($this->valori[$colonna]) 
				|| $this->valori[$colonna] != $valore)
			$this->modificato[$colonna] = true;
		$this->valori[$colonna] = $valore;
	}
	
	/**
	 * @access protected
	 * @param string $nome
	 * @param array $valore
	 */
	protected function setLista($nome, $valore) {
		//TODO fare più furbo
		$this->liste[$nome] = $valore;
		$this->modliste[$nome] = true;
	}
	
	/**
	 * @param string $colonna
	 * @param Data $valore
	 */
	protected function setData($colonna, $valore){
		if (is_null($valore))
			$d = NULL;
		else 
			$d = $valore->toString();
		$this->set($colonna, $d);
	}
	
	/**
	 * @param string $colonna
	 * @param boolean $valore
	 */
	protected function setBool($colonna, $valore){
		if (is_null($valore)) $b = NULL;
		else if ($valore) $b = 1;
		else $b = 0;
		$this->set($colonna, $b);
	}
	
	protected function isMod($colonna) {
		if (isset($this->modificato[$colonna]))
			return $this->modificato[$colonna];
		else
			return false;
	}
	
	protected function ignoraCol($ignora) {
		$this->ignora = $ignora;
		$this->eliminaVal();
	}
	
	/**
	 * Abilita la lettura di una tabella con le colonne $chiaveCol e $colonna
	 * @param string $nome nome della lista
	 * @param string $tabella nome della tabella contenente la lista
	 * @param string $colonna nome della colonna da leggere
	 */
	protected function aggiungiLista($nome, $tabella, $colonna) {
		$this->listeTab[$nome] = $tabella;
		$this->listeCol[$nome] = $colonna;
	}
	
	private function eliminaVal() {
		if (is_null($this->valori) || is_null($this->ignora)) return;
		foreach ($this->ignora as $c) {
			unset($this->valori[$c]);
		}
	}

	/**
	 * @access protected
	 * @param array $row array associativo contenente colonna -> valore
	 */
	protected function carica($row = NULL) {
		if (is_null($row) && !is_null($this->chiaveVal)) {
			//se non è impostata una riga allora carica la riga in base alla chiave
			$record = $this->_connessione->select($this->tabella, "$this->chiaveCol = '$this->chiaveVal'");
			$row = $record->fetch_assoc();
		}
		if (is_null($row)) return;
		foreach ($row as $c => $v){
			//se questa colonna è la chiave
			if ($c == $this->chiaveCol) {
				//se la chiave non è impostata la salva
				if (is_null($this->chiaveVal)) 
					$this->chiaveVal = $v;
			} else if (!isset($this->modificato[$c]) || !$this->modificato[$c]) {
				//se questo valore non è stato modificato
				//salva il valore e lo imposta coem non modificato 
				$this->valori[$c] = $v;
				$this->modificato[$c]  = false;
				if ($this->_backup)
					$this->valoribk[$c] = $v;
			}
		}
		$this->eliminaVal();
		$this->letto = true;
	}
	
	/**
	 * Carica dal db e restituisce una lista
	 * @access public
	 * @param string $nome
	 * @return array
	 * @see Modello::caricaListaResult()
	 */
	protected function caricaLista($nome) {
		if (isset($this->listeTab[$nome]))
			$mr = $this->_connessione->select($this->listeTab[$nome],
					"$this->chiaveCol = '$this->chiaveVal'", $this->listeCol[$nome]);
		else
			$mr = $this->caricaListaResult($nome);
		if (is_null($mr)) return NULL;
		$lista = array();
		while ($row = $mr->fetch_row()) {
			$lista[] = $row[0];
		}
		return $lista;
	}
	
	/**
	 * restituisce il risultato della query contenente gli elementi della lista, FARE OVERRIDE
	 * @access public
	 * @param string $nome
	 * @return mysqli_result
	 */
	protected function caricaListaResult($nome) {}

	/**
	 * inserisce nel database e imposta il valore chiaveVal
	 * @access private
	 */
	private function insert() {
		$primo = true;
		foreach($this->valori as $c => $v){
			if (is_null($v)) $v = "NULL";
			else $v = "'".$this->_connessione->quote($v)."'";
			if ($primo) {
				$sc = "$c";
				$sv = $v;
				$primo = false;
			} else {
				$sc .= ", $c";
				$sv .= ", $v";
			}
		}
		$conn = $this->_connessione->conn();
		$ret = $conn->query("INSERT INTO $this->tabella($sc) VALUES($sv);");
		if ($ret) {
			$this->chiaveVal = $this->_connessione->lastId();
			//insert delle liste
			if (!is_null($this->liste)) {
				foreach ($this->liste as $k=>$l) {
					if (isset($this->listeTab[$k])) {
						$this->insertListaDefault($k,$l);
					} else {
						$this->insertLista($k,$l);
					}
				}
			}
		}
		return $ret;
	}
	
	private function insertListaDefault($nome, $valori) {
		$sql = false;
		$id = $this->chiaveVal;
		foreach ($valori as $v) {
			$v = $this->_connessione->quote($v);
			if ($sql === false)
				$sql = "('$id', '$v')";
			else
				$sql .= ",('$id', '$v')";
		}
		$sql = "INSERT INTO {$this->listeTab[$nome]}($this->chiaveCol, {$this->listeCol[$nome]}) VALUES $sql;";
		$this->_connessione->conn()->query($sql);
	}
	
	protected function insertLista($nome, $valori) {}

	/**
	 * modifica solo valori con modificato=true
	 * @access private
	 */
	private function update() {
		$sql = "UPDATE $this->tabella SET ";
		$primo = true;
		foreach($this->valori as $c => $v){
			if ($this->modificato[$c]){
				if (is_null($v)) $v = "NULL";
				else $v = "'".$this->_connessione->quote($v)."'";
				if ($primo) {
					$sql .= "$c = $v";
					$primo = false;
				} else {
					$sql .= ", $c = $v";
				}
			}
		}
		//se non ci sono modifiche non fare nulla
		if (!$primo) {
			$sql .= " WHERE $this->chiaveCol = '$this->chiaveVal';";
			$ret = $this->_connessione->conn()->query($sql);
		} else $ret = true;
		//update delle liste
		if (!is_null($this->modliste)) {
			foreach ($this->modliste as $k=>$mod) {
				if ($mod) {
					if (isset($this->listeTab[$k])) {
						$this->_connessione->conn()->query("DELETE FROM {$this->listeTab[$k]} WHERE $this->chiaveCol = '$this->chiaveVal';");
						$this->insertListaDefault($k, $this->liste[$k]);
					} else {
						$this->updateLista($k, $this->liste[$k]);
					}
				}
			}
		}
		return $ret;
	}
	
	protected function updateLista($nome, $valori) {}
	
	//TODO eliminare
// 	/**
// 	 * @param Modello $n
// 	 */
// 	protected function clona(&$n) {
// 		$n->_connessione = $this->_connessione;
// 		$n->_backup = $this->_backup;
// 		$n->tabella = $this->tabella;
// 		$n->listeTab = $this->_backup;
// 		$n->listeCol = $this->listeCol;
// 		$n->chiaveCol = $this->chiaveCol;
// 		$n->chiaveVal = NULL;
// 		$n->letto = false;
// 		$n->valori = $this->valori;
// 		$n->valoribk = $this->valoribk;
// 		$n->liste = $this->liste;
// 		$n->ignora = $this->ignora;
// 	}
}
?>