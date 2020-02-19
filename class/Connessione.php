<?php
if (!defined("_BASEDIR_")) exit();

class Connessione{
	/**
	 * @var mysqli
	 */
	private $_conn = NULL;
	
	public $host;
	public $user;
	public $psw;
	public $dbname;
	public $port;
	
	public function flatArray($arr) {
		$ins = false;
		foreach ($arr as $id){
			$id = $this->quote($id);
			if ($ins === false)
				$ins .= "('$id'";
			else
				$ins .= ", '$id'";
		}
		if ($ins === false) return "()";
		$ins .= ")";
		return $ins;
	}
	
	public function connetti(){
		if (is_null($this->_conn))
			$this->_conn = $this->getMysqli();
	}
	
	protected function getMysqli() {
		$m = new mysqli($this->host, $this->user, $this->psw, $this->dbname, $this->port);
		$m->set_charset("utf8");
		return $m;
	}
	
	/**
	 * @access public
	 * @return mysqli
	 */
	public function conn() {
		return $this->_conn;
	}
	
	/**
	 * @param string $query
	 * @return mysqli_result
	 */
	public function query($query) {
		return $this->_conn->query($query);
	}
	
	/**
	 * restituisce un insieme di record
	 * @access public
	 * @param string $tabella
	 * @param string $chiaveCol
	 * @param string $where
	 * @return mysqli_result
	 * @static
	 */
	public function select($tabella, $where = "1", $colonne = "*") {
		
		return $this->_conn->query("SELECT $colonne FROM $tabella WHERE $where;");
	}
	
	public function lastId() {
		return $this->_conn->insert_id;
	}
	
	public function quote($string) {
		return $this->_conn->real_escape_string($string);
	}
}
?>