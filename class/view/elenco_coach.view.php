<?php
session_start();
if (!defined("_BASEDIR_")) exit();
include_model("UtSocieta");
include_class("Sesso");

class ElencoCoachView
{
	private $elenco;
	
	public function __construct()
	{
		$ut = UtSocieta::crea();
		$this->elenco = CoachEsterno::coachSocieta($ut->getIdSocieta());
	}
	
	public function stampa()
	{
		$lang = Lingua::getParole();
		if(count($this->elenco) > 0)
		{
		?>
			<div align="center">
				<div id="ele_coach">
						<div class="Gare_soc_right"><h1><?php echo $lang["elenco_coach"]; ?></h1></div>
					
						<table width="100%" class="atleti" id="atleti" >
						<tr  class="tr">
							<th><div class='thAtleti'><?php echo $lang["cognome_iscrizioni"].'/'.$lang["nome_iscrizioni"]; ?></div></th>
							<th><div class="thAtleti" ><?php echo $lang["sesso_iscrizioni"]; ?></div></th>
							<th><div class='thAtleti'><?php echo $lang["nascita_iscrizioni"]; ?></div></th>
							<th><div class='thAtleti'></div></th>
						</tr>
						<?php
						$c=0;
						foreach($this->elenco as $id=>$co)
						{
							if (($c % 2) == 0) $classe = "riga1";
							else $classe = "riga2";
							$c++;
							
							$cogn = $co->getCognome();
							$nome = $co->getNome();
							$sess = Sesso::toStringLungo($co->getSesso());
							$nasc = $co->getDataNascita()->format("d/m/Y");
							$link = "delcoach.php?id=$id";
							$str_eli = $lang["elimina_utente"];
							
							echo "<tr class=\"$classe\"><td class=\"riepilogo_center\">$cogn $nome</td><td class=\"riepilogo_center\">$sess</td><td class=\"riepilogo_center\">$nasc</td><td class=\"liButton\" style=\"text-align: center;\"><a href=\"$link\">$str_eli</a></td></tr>";
						} 
						?>
						</table>
				</div>
			</div>	
		<?php
		}
		else
		{?><div class="Gare_soc_right"><h1><?php echo $lang["no_elenco_coach"]; ?></h1></div><?php }
	}
}