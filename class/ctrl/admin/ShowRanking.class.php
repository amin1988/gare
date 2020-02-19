<?php
if (!defined("_BASEDIR_")) exit();
session_start();

include_model("Categoria","Gara","Ranking");

class ShowRankingCtrl {
	
	private $kata = array();
	private $kumite = array();
	
	public function __construct()
	{
// 		$ranking = Ranking::getPunteggi();
		$idatleti = Ranking::getAtleti();
		
		foreach($idatleti as $ida)
		{
			$pka = Ranking::getPuntiAtleta($ida, 0);
			if($pka > 0)
				$this->kata[$ida] = $pka; 
			
			$pku = Ranking::getPuntiAtleta($ida, 1);
			if($pku > 0)
				$this->kumite[$ida] = $pku;
		}
		
		arsort($this->kata);
		arsort($this->kumite);
	}
	
	public function getKata()
	{
		return $this->kata;
	}
	
	public function getKumite()
	{
		return $this->kumite;
	}
	
	public function getPunti($idatleta,$tipo)
	{
		return Ranking::getPunteggiAtlGara($idatleta, $tipo);
	}
}
