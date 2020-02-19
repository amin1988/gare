<?php
if (!defined("_BASEDIR_")) exit();
include_controller("registrazioneesterna");
include_model("Societa");

class RegistraEstResView
{
	private $ids;
	public function __construct($ids)
	{
		$this->ids = $ids;
	}
	
	public function stampa()
	{
		
		$result = $_SESSION["err_est"];
		unset($_SESSION["err_est"]);
		
		$lang = Lingua::getParole();
		
		switch ($result)
		{
			case RegistraEsternaCtrl::EMAIL_MIS : $this->emailMis(); break;
			
			case RegistraEsternaCtrl::RES_MIS : $this->resMis(); break;
			
			case RegistraEsternaCtrl::SOC_PRE : $this->socPre(); break;
			
			case RegistraEsternaCtrl::SOC_OK : $this->socOk($this->ids); break;
		}
	}
	
	private function emailMis()
	{
		echo "email mismatch";
	}
	
	private function resMis()
	{
		echo "result mismatch";
	}
	
	private function socPre()
	{
		echo "La societ&agrave risulta gi&agrave registrata al sistema. Non &egrave necessario registrarla novamente per partecipare alla gare.";
	}
	
	private function socOk()
	{
		$soc = Societa::fromId($this->ids);
		$nome = $soc->getNome();
		
		if(_WKC_MODE_)
			echo "
				<div align=\"center\">
					<p><h3>$nome signed up correctly.</h3><br><h4>Please check the email provided to retrieve username and password.</h4><br><h4>Please check also in your spam folder!</h4></p>
				</div>";
		else
			echo "
				<div align=\"center\">
					<p><h3>Societ&agrave $nome registrata correttamente.</h3><br><h4>Controlli l'email fornita per le credenziali d'accesso.</h4><br><h4>Controlli anche nella cartella dello spam in caso fosse erroneamente cestinata.</h4></p>
				</div>";
	}
}