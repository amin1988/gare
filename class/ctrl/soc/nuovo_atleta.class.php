<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("AtletaEsterno","UtSocieta");
include_errori("VerificaGara");

class AtletaEsternoCtrl {
	
	const NOME = 'nome';
	const COGNOME = 'cognome';
	const SESSO = 'sesso';
	const DATA_NASCITA = 'nascita';
	const CINTURA = 'cintura';
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
			$a = new AtletaEsterno();
			
			$a->setSocieta($ut->getIdSocieta());
			$a->setNome($_POST["nome"]);
			$a->setCognome($_POST["cognome"]);
			$a->setSesso($_POST["sesso"]);
			$a->setDataNascita(Data::parseDMY($_POST["nascita"]));
			$a->setCintura($_POST["cintura"]);
			
			$a->salva();
			
			if($a->getChiave() != NULL)
				$this->redirect();
		}
	}
}