<?php
if (!defined("_BASEDIR_")) exit();
include_model("Modello", "Utente");

class Segnalazione extends Modello {
	
	public static function crea($pagina, $desc, $email) {
		$s = new Segnalazione();
		$conn = $s->_connessione;
		$s->set("idutente", Utente::getIdAccesso());
		$s->set("pagina", $conn->quote($pagina));
		$s->set("browser", $_SERVER["HTTP_USER_AGENT"]);
		$s->set("descrizione", $conn->quote($desc));
		$s->set("email", $conn->quote($email));
		return $s;
	}
	
	public function __construct($id = NULL) {
		parent::__construct("segnalazioni", "idsegnalazione", $id);
	}
	
	/**
	 * @return int
	 */
	public function getIdUtente() {
		return $this->get("idutente");
	}
	
	/**
	 * @return string
	 */
	public function getPagina() {
		return $this->get("pagina");
	}
	
	/**
	 * @return string
	 */
	public function getBrowser() {
		return $this->get("browser");
	}
	
	/**
	 * @return string
	 */
	public function getDescrizione() {
		return $this->get("descrizione");
	}
}

?>