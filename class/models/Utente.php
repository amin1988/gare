<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello");

/**
 * @access public
 * @package models
 */
class Utente extends Modello {
	const SOCIETA = 1;
	const ORGANIZZATORE = 2;
	const RESPONSABILE = 3;
	const ADMIN = 4;
	const VISUALIZZA = 5;
	
	public static function getTipiUtente() {
		return array(self::SOCIETA,
				self::ORGANIZZATORE,
				self::RESPONSABILE,
				self::VISUALIZZA,
				self::ADMIN
				);
	}
	
	/**
	 * @param int $id l'id dell'utente o NULL per creare l'utente loggato
	 * @param boolean $tipato true per restituire un oggetto del tipo specifico dell'utente
	 * @return Utente o la sottoclasse specifica se $tipato==true
	 */
	public static function crea($id=NULL, $tipato=false) {
		if (is_null($id)) $id = Utente::getIdAccesso();
		if (is_null($id)) return NULL;
		if ($tipato) {
			$GLOBALS["connint"]->connetti();
			$mr = $GLOBALS["connint"]->select("utenti","attivo = '1' AND idutente = '$id'");
			$ut = self::creaTipato($mr->fetch_assoc());
		} else {
			$ut = new Utente($id);
		}
		if ($ut->isAttivo()) 
			return $ut;
		else 
			return NULL;
	}
	
	private static function creaTipato($row) {
		switch ($row["tipo"]) {
			case self::SOCIETA:
				include_model("UtSocieta");
				$ut = new UtSocieta();
				break;
			case self::ORGANIZZATORE:
				include_model("Organizzatore");
				$ut = new Organizzatore();
				break;
			case self::RESPONSABILE:
				include_model("Responsabile");
				$ut = new Responsabile();
				break;
			case self::ADMIN:
				include_model("Amministratore");
				$ut = new Amministratore();
				break;
			case self::VISUALIZZA:
				include_model("Visualizzatore");
				$ut = new Visualizzatore();
				break;
			default:
				return NULL;
		}
		unset($row["password"]);
		$ut->carica($row);
		return $ut;
	}
	
	public static function lista() {
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$mr = $conn->select("utenti","attivo = '1' ORDER BY user");
		$res = array();
		while($row = $mr->fetch_assoc()) {
			$u = self::creaTipato($row);
			$res[$u->getChiave()] = $u;
		}
		return $res;
	}
	
	/**
	 * Indica se un certo username � gi� presente nel database
	 * @param string $username
	 * @return boolean
	 */
	public static function usernameEsiste($username) {
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		$username = $conn->quote($username);
		$mr = $conn->select("utenti","user='$username' AND attivo = '1'");
		return !is_null($mr->fetch_row()); 
	} 
	
	/**
	 * @access public
	 * @param string $user
	 * @param string $password
	 * @return Utente
	 * @static
	 */
	public static function login($user, $password) {
		$GLOBALS["connint"]->connetti();
		$u = $GLOBALS["connint"]->conn()->real_escape_string($user);
		$p = md5($password);
		$mr = $GLOBALS["connint"]->select("utenti","attivo = '1' AND password = '$p' AND user = '$u'");
		$row = $mr->fetch_assoc();
		//se non trova niente esce
		if (is_null($row)) return NULL;
		//se trova pi� utenti esce
		if (!is_null($mr->fetch_assoc())) return NULL;
		return self::creaTipato($row);
	}
	
	public static function logout() {
		$_SESSION["idutente"] = NULL;
		unset($_SESSION["idutente"]);
	}
	
	/**
	 * @return int
	 */
	public static function getIdAccesso() {
		//login gare
		if (isset($_SESSION["idutente"]))
			return $_SESSION["idutente"];
		
		//verifica login affiliazione
		include_esterni("LoginFiam");
		$ids = LoginFiam::getIdSocieta();
		if (is_null($ids)) return NULL;
		
		//cerca un utente per la societa
		/* @var $conn Connessione */
		$conn = $GLOBALS["connint"];
		$conn->connetti();
		//TODO cercare quello col codice/username uguale?
		$mr = $conn->select("utenti u INNER JOIN societa s ON u.idsocieta = s.idsocieta",
				"s.idaffiliata = '$ids' AND attivo=1 LIMIT 1","u.*");
		$row = $mr->fetch_assoc();
		//se non trova niente esce
		if (is_null($row)) return NULL;
		$ut = self::creaTipato($row);
		$ut->salvaAccesso();
		return $ut->getChiave();
	}
	
	/**
	 * @param Utente $u
	 * @param string $psw
	 * @param array $dati chiavi: user, nome, email
	 */
	protected function nuovoUtente($tipo, $psw, $dati) {
		$this->set("tipo",$tipo);
		$this->set("user",$dati["user"]);
		$this->setPassword($psw);
		$this->set("nome",$dati["nome"]);
		$this->set("email",$dati["email"]);
	}
	
	/**
	 * @param int $id
	 * @param Connessione $conn
	 */
	public function __construct($id = NULL, $conn = NULL){
		parent::__construct("utenti", "idutente", $id, $conn);
	}
	
	public function salvaAccesso() {
		$_SESSION["idutente"] = $this->getChiave();
		$_SESSION["email_segnala"] = $this->getEmail();
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getTipo() {
		return $this->get("tipo");
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getNome() {
		return $this->get("user");
	}
	
	public function setPassword($valore) {
		$this->set("password", md5($valore));
	} 
	
	public function getContatto() {
		return $this->get("nome");
	}
	
	public function setContatto($valore) {
		$this->set("nome", $valore);
	}
	
	public function getEmail() {
		return $this->get("email");
	}
	
	public function setEmail($valore) {
		$this->set("email", $valore);
	}
	
	public function isAttivo() {
		$v = $this->getBool("attivo");
		if (is_null($v)) return false;
		return $v;
	}
	
	public function disattiva($salva=true) {
		$this->setBool("attivo", false);
		if ($salva) $this->salva();
	}
	
	public function getNomeTipo() {
		switch ($this->getTipo()) {
			case self::SOCIETA:
				return "Societa";
			case self::RESPONSABILE:
				return "Responsabile";
			case self::ORGANIZZATORE:
				return "Organizzatore";
			case self::ADMIN:
				return "Amministratore";
			case self::VISUALIZZA:
				return "Visualizzatore";
			default:
				return "Utente";
		}
	}
	
	public function getIdSoc() {
		return $this->get("idsocieta");
	}
}


?>