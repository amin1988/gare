<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("CoachEsterno","UtSocieta");
include_errori("VerificaGara");

class CoachEsternoCtrl {
	
	const NOME = 'nome';
	const COGNOME = 'cognome';
	const SESSO = 'sesso';
	const DATA_NASCITA = 'nascita';
	const SUBMIT = 'submit';
	
	/**
	 * viene chiamato se non ci sono errori nella compilazione del modulo
	 */
	protected function redirect() {
		die("<script>location.href = 'index.php'</script>");
		exit();
	}
	
	public function __construct() {
		
		$lang = Lingua::getParole();
		
		$ut = UtSocieta::crea();
		
		if(isset($_POST["submit"]))//è stato premuto il pulsante salva
		{
			$c = new CoachEsterno();
			
			$c->setIDSocieta($ut->getIdSocieta());
			$c->setNome($_POST["nome"]);
			$c->setCognome($_POST["cognome"]);
			$c->setSesso($_POST["sesso"]);
			$c->setDataNascita(Data::parseDMY($_POST["nascita"]));
			
			$c->salva();
			
			if($c->getChiave() != NULL)
				$this->redirect();
		}
	}
}