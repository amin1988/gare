<?php
if (!defined("_BASEDIR_")) exit();
require_once("../../config.inc.php");
include_controller("admin/ShowRanking");
include_model("esterni/TesseratoFiam");

class ShowRankingView
{
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new ShowRankingCtrl();
	}
	
	public function stampa()
	{
		$lang = Lingua::getParole();
		
		$at = $lang["atleta"];
		$pos = $lang["posizione"];
		$pun = $lang["punti"];
		
		$count = 0;
		$kata = $this->ctrl->getKata();
		echo "<h2>Ranking Kata</h2>";
		
		if(count($kata) > 0)
		{
		echo "<table class=\"atleti\" style=\"font-size: 15px; text-align: center;\">";
		echo "<thead><tr><td style=\"width: 150px;\"><b>$pos</b></td><td style=\"font-size: 15px;\"><b>$at</b></td><td style=\"width: 150px;\"><b>$pun</b></td></tr></thead>";
		foreach($kata as $ida=>$punti)
		{
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			
			if($count%50 == 0)
			{
				echo "<script type=\"text/javascript\">";
				echo "hidePoints();";
				echo "</script>";
			}
			
			$tess = TesseratoFiam::getTessFIAM($ida);
			$gen = strtolower($tess->getCognome()." ".$tess->getNome());
			$count++;
			
			echo "<tr class=\"$class\">";
			echo "<td>$count</td>";
			echo "<td class=\"cognome\" style=\"font-size: 15px; text-align: center;\"><a onclick=\"showKA($ida)\">$gen</a></td>";
			echo "<td>$punti</td></tr>";
			
			foreach($this->ctrl->getPunti($ida, 0) as $idr=>$r)
			{
				$strdata = $r->getDataGara()->format("d/m/Y");
				$g = Gara::fromId($r->getIDGara());
				$gn = strtolower($g->getNome());
				$p = $r->getPunti();
				$cr = $r->getCategoria();
				$c = Categoria::elenco("idcategoria='$cr'");
				$strc = $c[$cr]->getNome();
				echo "<tr class=\"puntiKA_$ida punti_ALL\"><td class=\"cognome\">$strdata</td><td class=\"cognome\">$gn - $strc</td><td class=\"cognome\">$p</td></tr>";
			}
		}
		echo "</table>";
		}
		else 
			echo "<h3>Nessun risultato inserito</h3>";
		
		echo "<script type=\"text/javascript\">";
		echo "hidePoints();";
		echo "</script>";
		
		$count = 0;
		$kumite = $this->ctrl->getKumite();
		echo "<h2>Ranking Kumite</h2>";
		
		if(count($kumite) > 0)
		{
		echo "<table class=\"atleti\" style=\"font-size: 15px; text-align: center;\">";
		echo "<thead><tr><td style=\"width: 150px;\"><b>$pos</b></td><td style=\"font-size: 15px;\"><b>$at</b></td><td style=\"width: 150px;\"><b>$pun</b></td></tr></thead>";
		foreach($kumite as $ida=>$punti)
		{
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			
			if($count%50 == 0)
			{
				echo "<script type=\"text/javascript\">";
				echo "hidePoints();";
				echo "</script>";
			}
				
			$tess = TesseratoFiam::getTessFIAM($ida);
			$gen = strtolower($tess->getCognome()." ".$tess->getNome());
			$count++;
				
			echo "<tr class=\"$class\">";
			echo "<td>$count</td>";
			echo "<td class=\"cognome\" style=\"font-size: 15px;\"><a onclick=\"showKU($ida)\">$gen</a></td>";
			echo "<td>$punti</td></tr>";
			
		foreach($this->ctrl->getPunti($ida, 1) as $idr=>$r)
			{
				$strdata = $r->getDataGara()->format("d/m/Y");
				$g = Gara::fromId($r->getIDGara());
				$gn = strtolower($g->getNome());
				$p = $r->getPunti();
				$cr = $r->getCategoria();
				$c = Categoria::elenco("idcategoria='$cr'");
				$strc = $c[$cr]->getNome();
				echo "<tr class=\"puntiKU_$ida punti_ALL\"><td class=\"cognome\">$strdata</td><td class=\"cognome\">$gn - $strc</td><td class=\"cognome\">$p</td></tr>";
			}
		}
		echo "</table>";
		}
		else 
			echo "<h3>Nessun risultato inserito</h3>";
		
		echo "<script type=\"text/javascript\">";
		echo "hidePoints();";
		echo "</script>";
	}
}