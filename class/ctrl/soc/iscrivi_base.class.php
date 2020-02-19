<?php
if (!defined("_BASEDIR_")) exit();
include_model("Coach","CoachEsterno","Persona","Arbitro");

class IscriviBase {
	/**
	 *  @var UtSocieta
	 */
	protected $ut;
	/** 
	 * @var Gara
	 */
	protected $gara;
	
	/**
	 * @var Tecnico[]
	 */
	protected $tecnici;
	/**
	 * @var Atleta[] solo cinture nere
	 */
	protected $nere;
	
	/**
	 * @var Coach[]
	 */
	protected $coach;
	
	/**
	 * @var CoachEsterno[]
	 */
	protected $coachesterno;
	
	/**
	 * @var Arbitro[]
	 */
	protected $arb;
	
	/**
	 * @var VerificaCoach
	 */
	protected $errCoach;
	
	protected function caricaArbitri() {
		$soc = $this->ut->getSocieta();
		
		$this->arb = Arbitro::lista($this->gara->getChiave(), $soc->getChiave());
	}
	
	protected function caricaCoach() {
		$soc = $this->ut->getSocieta();
		
		//lettura tecnici
		$this->tecnici = $soc->getTecnici();
		
		//lettura coach
		$this->coach = Coach::lista($this->gara->getChiave(), $soc->getChiave());
		
		//lettura coach esterni
		if(!$soc->isAffiliata())
			$this->coachesterno = CoachEsterno::lista($this->gara->getChiave(), $soc->getChiave());//coachSocieta($soc->getChiave());
	}
	
	protected function pulisciCoach() {
		//pulizia dei tecnici dalle nere
		foreach ($this->tecnici as $t) {
			unset($this->nere[$t->getChiave()]);
		}
		
		//pulizia dei coach da tecnici e nere
		foreach ($this->coach as $c) {
			/* @var $c Coach */
			unset($this->tecnici[$c->getPersona()]);
			unset($this->nere[$c->getPersona()]);
// 			switch ($c->getTipo()) {
// 				case Persona::TIPO_TECNICO:
// 					unset($this->tecnici[$c->getPersona()]);
// 					break;
// 				case Persona::TIPO_ATLETA:
// 					unset($this->nere[$c->getPersona()]);
// 					break;
// 			}
		}
	}
	
	public function isFotoCoachObbligatoria() {
		return $this->gara->isFotoCoachObbligatoria();
	}
	
	
	
	/**
	 * @return Persona[]
	 */
	public function getCoach() {
		if($this->ut->getSocieta()->isAffiliata())
			return $this->ut->getSocieta()->getCoach($this->coach);
		else
			return $this->getCoachEsterno();
	}
	
	public function getCoachEsterno() {
		return CoachEsterno::lista($this->gara->getChiave(),$this->ut->getIdSocieta());
	}
	
	/**
	 * @return Persona[]
	 */
	public function getArbitri() {
		return $this->ut->getSocieta()->getArbitri($this->arb);
	}
	
	/**
	 * Restituisce i tecnici non segnati come coach
	 * @return Tecnico[]
	 */
	public function getTecnici() {
		return $this->tecnici;
	}
	
	/**
	 * Restituisce le nere non segnate come coach
	 * @return Atleta[]
	 */
	public function getNere() {
		return $this->nere;
	}

	/**
	 * @return VerificaCoach
	 */
	public function getErroriCoach() {
		return $this->errCoach;
	}
	
	public function getErroriArbitri() {
		return NULL;
	}
	
	public function getGara() {
		return $this->gara;
	}
	
	public function getUtenteSocieta() {
		return $this->ut;
	}
	
// 	/**
// 	 * @param Atleta[] $atleti
// 	 * @return boolean
// 	 */
// 	protected function salvaArb($atleti, $salva=true) {
// 		if (!isset($_POST["pageid"])) return false; //chiamata non effettuata
	
// 		$dela = array();
// 		$mapa = array();
// 		$modifiche = false;
// 		//ricerca coach eliminati
// 		foreach ($this->arb as $ida => $a) {
// 			/* @var $a Arbitro */
// 			if (!isset($_POST["arb"][3][$a->getPersona()]))
// 				$dela[$ida] = $a;
// 			else
// 				$mapa[3][$a->getPersona()] = $ida;
// 		}
// 		//elimina coach
// 		foreach ($dela as $ida => $a) {
// 			unset($this->arb[$ida]);
// 			if ($salva) $a->elimina();
// 		}
// 		//ricerca nuovi coach tecnici
// 		$modifiche |= $this->nuoviArb($mapa, Persona::TIPO_ARBITRO, $this->arb, $salva);
// 		//$modifiche |= $this->nuoviCoach($mapc, Persona::TIPO_ATLETA, NULL, $salva);//$atleti);
	
// 		return $salva && $modifiche;
// 	}

	protected function salvaArb($arb)
	{
		if (!isset($_POST["pageid"])) return false; //chiamata non effettuata
		
		foreach($this->arb as $ids=>$a)
		{
			$ida = $a->getPersona();
			if(isset($_POST["arb"][3][$ida]))
			{
				$a->conferma(1);
				$a->salva();
			}
			else 
			{
				$a->conferma(0);
				$a->salva();
			}
		}
	}
	
	/**
	 * @param int[][] $mapc formato: tipo => idpersona => idcoach
	 * @param int $tipo
	 * @param Persona[] $lista
	 * @return boolean
	 */
	private function nuoviArb($mapa, $tipo, $lista, $salva) {
		if (!isset($_POST["arb"][$tipo])) return false;
		$modifiche = false;
		$idg = $this->gara->getChiave();
		if (is_null($lista)) {
			$halista = false;
			$val["ids"] = $this->ut->getSocieta()->getChiave();
			$val["tipo"] = $tipo;
			$listaid = array();
			foreach ($_POST["arb"][$tipo] as $idp) {
				if (!isset($mapa[$tipo][$idp]))
					$listaid[$idp] = $idp;
			}
			$lista = $this->ut->getSocieta()->getArbitri($listaid);
		} else {
			$halista = true;
			$listaid = $_POST["arb"][$tipo];
		}
		foreach ($listaid as $idp) {
			if (!$halista || !isset($mapa[$tipo][$idp])) {
				$modifiche = true;
				if ($halista)
					$val["persona"] = $lista[$idp];
				else
					$val["idp"] = $idp;
				$a = Arbitro::crea($idg, $val);
				if ($salva) {
					$a->salva();
					$a->conferma();
					$ida = $a->getChiave();
				} else {
					$ida = -$idp;
				}
				$this->arb[$ida] = $a;
			}
		}
		return $modifiche;
	}
	
	/**
	 * @param Atleta[] $atleti
	 * @return boolean
	 */
	protected function salvaCoach($atleti, $salva=true) {
		if (!isset($_POST["pageid"])) return false; //chiamata non effettuata
		
		$delc = array();
		$mapc = array();
		$modifiche = false;
		//ricerca coach eliminati
		foreach ($this->coach as $idc => $c) {
			/* @var $c Coach */
			if (!isset($_POST["coach"][$c->getTipo()][$c->getPersona()]))
				$delc[$idc] = $c;
			else
				$mapc[$c->getTipo()][$c->getPersona()] = $idc;
		}
		//elimina coach
		foreach ($delc as $idc => $c) {
			unset($this->coach[$idc]);
			if ($salva) $c->elimina();
		}
		
		if($this->ut->getSocieta()->isAffiliata())
		{
			//ricerca nuovi coach tecnici
			$modifiche |= $this->nuoviCoach($mapc, Persona::TIPO_TECNICO, $this->tecnici, $salva);
			$modifiche |= $this->nuoviCoach($mapc, Persona::TIPO_ATLETA, NULL, $salva);//$atleti);
		}
		else 
		{
			$modifiche |= $this->nuoviCoachEsterno($mapc);
		}
	
		return $salva && $modifiche;
	}
	
	/**
	 * @param int[][] $mapc formato: tipo => idpersona => idcoach
	 * @param int $tipo
	 * @param Persona[] $lista
	 * @return boolean
	 */
	private function nuoviCoach($mapc, $tipo, $lista, $salva) {
		if (!isset($_POST["coach"][$tipo])) return false;
		$modifiche = false;
		$idg = $this->gara->getChiave();
		if (is_null($lista)) {
			$halista = false;
			$val["ids"] = $this->ut->getSocieta()->getChiave();
			$val["tipo"] = $tipo;
			$listaid = array();
			foreach ($_POST["coach"][$tipo] as $idp) {
				if (!isset($mapc[$tipo][$idp]))
					$listaid[$idp] = $idp;
			}
			$lista = $this->ut->getSocieta()->getAltriCoach($listaid);
		} else {
			$halista = true;
			$listaid = $_POST["coach"][$tipo];
		}
		foreach ($listaid as $idp) {
			if (!$halista || !isset($mapc[$tipo][$idp])) {
				$modifiche = true;
				if ($halista)
					$val["persona"] = $lista[$idp];
				else
					$val["idp"] = $idp;
				$c = Coach::crea($idg, $val);
				$fotoOk = $this->fotoCoach($c);
				if ($salva && $fotoOk) {
					$c->salva();
					$idc = $c->getChiave();
				} else {
					$idc = -$idp;
				}
				$this->coach[$idc] = $c;
			}
		}
		return $modifiche;
	}
	
	private function nuoviCoachEsterno($mapc) {
		
		foreach($_POST["coach"][1] as $id=>$idce)
		{
			if(!isset($this->coachesterno[$idce]) && !isset($this->coach[$idce]))
			{
				$val = array("ids"=>$this->ut->getIdSocieta(),"idp"=>$idce,"tipo"=>1);
				$c = CoachEsterno::crea($this->gara->getChiave(), $val);
				$this->coachesterno[$idce] = $c;
				$this->coach[$idce] = $c;
				$c->salva();
			}
		}
		
		return true;
	}
	
	/**
	 * @param Coach $c
	 */
	private function fotoCoach($c) {
		if (!$this->gara->isFotoCoachObbligatoria() || $c->haFoto())
			return true;
		
		if (!$this->errCoach->verificaFoto($c->getPersona()))
			return false;
		$move = $this->spostaFile($_FILES["foto"]["tmp_name"][$c->getPersona()], _BASEDIR_.$c->getFoto(false));
		if ($move && $c->haFoto()) 
			return true;
		
		$this->errCoach->setErroreFoto($c->getPersona(), VerificaCoach::FOTO_UNK);
		return false;
	}
	
	protected function spostaFile($src, $dest) {
		return move_uploaded_file($src, $dest);
	}
}