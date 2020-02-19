<?php
if (!defined("_BASEDIR_")) exit();
session_start();
include_model("Organizzatore", "Gara", "Arbitro","Societa","Utente");
include_model("esterni/ArbitroAffiliato","esterni/TesseratoFiam","ArbitroEsterno");
include_errori("VerificaGara");

class ConvocazioniCtrl {
	
	/** @var Organizzatore */
	protected $ut;
	protected $gara;
	
	private $arb = array();
	private $arb_est = array();
	private $arb_conv = array();
	private $turni = array();
	
	public function __construct($id_gara) {
		
		$lang = Lingua::getParole();
		$gara = new Gara($id_gara);
		
		$id_u = Utente::getIdAccesso();
		$org = Organizzatore::crea($id_u);
		
		$zone_g = $gara->getZone();
		$zone_o = $org->getZone();
		
		if(!$this->checkZone($zone_g, $zone_o))//se la gara non è nelle zone dell'organizzatore, quest'ultimo non può fare le convocazioni
			homeutente($org);
		
		$this->arb = ArbitroAffiliato::getListaArb();
		$this->arb_est = ArbitroEsterno::getListaArbEst();
		$this->arb_conv = ArbitroAffiliato::getConvocatiGara($id_gara);
		$this->turni = ArbitroAffiliato::getTurni($id_gara);
		
		if(isset($_POST["pageid"]))//è stato premuto il pulsante salva
		{
			$check = $_POST["arb"];
			$turni = $_POST["turni"];
			
// 			var_dump($check,$turni);//DEBUG
			foreach($check as $id=>$arb)
			{
				if(!in_array($id, $this->arb_conv))//se l'arbitro non era già convocato
				{
					$t = TesseratoFiam::getTessFIAM($id);
					if(!is_null($t->getChiave()))
					{
						$ids = Societa::idFromidAff($t->getSocieta());
						if(!is_null($ids))
						{
							$a = new Arbitro();
								
							$a->setGara($id_gara);
							$a->setPersona($id);
							$a->setSocieta($ids);
							$a->setSocietaAff($t->getSocieta());
							$a->setTurni($turni[$id]);
								
							$a->conferma(1);
							$a->salva();
						}
					}
					else//arbitro non fiam
					{
						$arb_es = ArbitroEsterno::fromId($id);
						
						$a = new Arbitro();
						
						$a->setGara($id_gara);
						$a->setPersona($id);
						$a->setSocieta($arb_es->getIDSocieta());
						$a->setSocietaAff(NULL);
						$a->setTurni($turni[$id]);
						
						$a->conferma(1);
						$a->salva();
					}
				}
				else//arbitro già convocato, controllo numero turni
				{
					$t = TesseratoFiam::getTessFIAM($id);
					if($this->turni[$id] != $turni[$id])//se numero di turni diverso, update
					{
						Arbitro::eliminaConv($id_gara, $id);
						
						if($t->getChiave() !== NULL)
							$ids = Societa::idFromidAff($t->getSocieta());
						else 
							$ids = NULL;
						
						if(!is_null($ids))
						{
							$a = new Arbitro();
								
							$a->setGara($id_gara);
							$a->setPersona($id);
							$a->setSocieta($ids);
							$a->setSocietaAff($t->getSocieta());
							$a->setTurni($turni[$id]);
								
							$a->conferma(1);
							$a->salva();
						}
						else//arbitro non fiam
						{
							Arbitro::eliminaConv($id_gara, $id);
							
							$arb_es = ArbitroEsterno::fromId($id);
							
							$a = new Arbitro();
							
							$a->setGara($id_gara);
							$a->setPersona($id);
							$a->setSocieta($arb_es->getIDSocieta());
							$a->setSocietaAff(NULL);
							$a->setTurni($turni[$id]);
							
							$a->conferma(1);
							$a->salva();
						}
					}
				}
				
			}
			
			foreach($this->arb_conv as $id=>$arb)
			{
				if(!in_array($arb, $check))//se la convocazione è stata tolta
				{
					Arbitro::eliminaConv($id_gara, $arb);
				}
			}
			
			$page = $_SERVER["PHP_SELF"]."?id=$id_gara";
			header("Location: $page");
		}
	}
	
	public function getGara()
	{
		return $this->gara;
	}
	
	public function getArbitri()
	{
		return $this->arb;
	}
	
	public function getArbitriEsterni()
	{
		return $this->arb_est;
	}
	
	public function getConvocati()
	{
		return $this->arb_conv;
	}
	
	public function getTurni()
	{
		return $this->turni;
	}
	
	/**
	 * Restituisce true se organizzatore e gara hanno una zona in comune, altrimenti ritorna false
	 * @param int[] $zone_gara
	 * @param int[] $zone_org
	 * @return boolean
	 */
	public function checkZone($zone_gara, $zone_org)
	{
		$ret = false;
	
		foreach($zone_gara as $id_g=>$id_z_g)
		{
			if($ret == true)
				continue;
				
			foreach($zone_org as $id_o=>$id_z_o)
			{
				if($ret == true)
					continue;
	
				if($id_z_g == $id_z_o)
					$ret = true;
			}
		}
	
		return $ret;
	}
	
}