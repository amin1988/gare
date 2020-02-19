<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("Official","UtSocieta");
include_errori("VerificaGara");

class OfficialCtrl {
	
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
			$o = new Official();
			
			$o->setIDSocieta($ut->getIdSocieta());
			if($ut->getSocieta()->isAffiliata())
				$o->setIDSocietaAff($ut->getSocieta()->getIdAffiliata());
			else
				$o->setIDSocietaAff(NULL);
			$o->setNome($_POST["nome"]);
			$o->setCognome($_POST["cognome"]);
			$o->setSesso($_POST["sesso"]);
			$o->setDataNascita(Data::parseDMY($_POST["nascita"]));
			
			$o->salva();
			
			if($o->getChiave() != NULL)
				$this->redirect();
		}
	}
}