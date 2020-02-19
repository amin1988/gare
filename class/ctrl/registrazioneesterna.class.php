<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("Societa","Utente","UtSocieta");

class RegistraEsternaCtrl {
	
	const RES_MIS = 'result_mismatch';
	const EMAIL_MIS = 'email_mismatch';
	const SOC_PRE = 'societa_presente';
	const SOC_OK = 'registrazione_ok'; 
	
	private $ar_soc = array(
				1=>"nome",
				2=>"nomebreve",
				3=>"idstile",
				4=>"idzona",
				5=>"idaffiliata"
		);
	
	private $ar_ut = array();
	
	public function __construct($wkc=false)
	{
		$lang = Lingua::getParole();
		
		
		if(isset($_POST["invia"]))//è stato premuto il pulsante salva
		{
			if($_POST["res_eq"] != 11)
			{
				$_SESSION["err_est"] = self::RES_MIS;
				echo '<script type="text/javascript">location.href = "regest.php"</script>';
				die();
			}
			
			if(addslashes($_POST["conf_email"]) != addslashes($_POST["email"]))
			{
				$_SESSION["err_est"] = self::EMAIL_MIS;
				echo '<script type="text/javascript">location.href = "regest.php"</script>';
				die();
			}
			
			if(Societa::isPresenteNomeBreve($_POST["nomebreve"]))
			{
				$_SESSION["err_est"] = self::SOC_PRE;
				echo '<script type="text/javascript">location.href = "regest.php"</script>';
				die();
			}
			
			$soc = new Societa();
			
			$soc->setNome($_POST["nome"]);
			$soc->setNomeBreve($_POST["nomebreve"]);
			
			
			if(!$wkc)
			{
				$soc->setStile($_POST["idstile"]);
				$soc->setFedEst($_POST["fed_est"]);
				
				if(isset($_POST["idzonasub"]) && $_POST["idzonasub"] != '' &&$_POST["idzonasub"] != 0)
					$soc->setZona($_POST["idzonasub"]);
				else 
					$soc->setZona($_POST["idzona"]);
				
				$soc->setWkc(0);
				$soc->setNazione(106);
			}
			else
			{
				$soc->setStile(3);
				$soc->setFedEst(NULL);
				$soc->setZona(53);
				$soc->setWkc(1);
				$soc->setNazione($_POST["nazione"]);
			}
			
			$soc->salva();
			$ids = $soc->getChiave();
			
			$dati = array(
					"user"=>"se".$soc->getChiave(),
					"nome"=>$_POST["contatto"],
					"email"=>$_POST["email"]
			);
			
			$user = "se".$soc->getChiave();
			$psw = rand(10000,50000).$soc->getChiave();
			
			$ut = UtSocieta::nuovo($soc->getChiave(), $psw, $dati);
			
			$ut->salva();
			
			$_SESSION["err_est"] = self::SOC_OK;
			
			$to = $_POST["email"];
			
			if(_WKC_MODE_)
			{
				$subject = "11th WKC World Championships registration";
				$txt = $_POST["nome"]." has been inserted into WKC registration system.\n
You can access with the following credentials:\n \n
Username: $user \n
Password: $psw \n \n
Best regards \n
WKC Organizing Team";
			}
			else 
			{
				$subject = "Iscrizione gare FIAM";
				$txt = "La società ".$_POST["nome"]." è stata iscritta per partecipare alle gare FIAM.\n
Può accedere alle gare con le seguenti credenziali:\n
Username: $user\n
Password: $psw\n
Cordiali saluti\n
Segreteria FIAM";
			}
			$headers = "From: segreteria@fiamsport.it" . "\r\n" .
					"BCC: supporto@fiamsport.it";
			
			mail($to,$subject,$txt,$headers);
			
			echo "<script type=\"text/javascript\">location.href = \"regres.php?ids=$ids\"</script>";
			die();
		}
	}
	
	public function getArraySoc()
	{
		return $this->ar_soc;
	}
	
	public function getArrayUt()
	{
		return $this->ar_ut;
	}
}