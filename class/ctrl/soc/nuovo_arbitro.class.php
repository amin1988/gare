<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("ArbitroEsterno","UtSocieta");
include_errori("VerificaGara");

class ArbitroEsternoCtrl {
	
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
			$ar = new ArbitroEsterno();
			
			$ar->setIDSocieta($ut->getIdSocieta());
			$ar->setNome($_POST["nome"]);
			$ar->setCognome($_POST["cognome"]);
			$ar->setSesso($_POST["sesso"]);
			$ar->setDataNascita(Data::parseDMY($_POST["nascita"]));
			
			$ar->salva();
			
			if($ar->getChiave() != NULL)
				$this->redirect();
		}
	}
}