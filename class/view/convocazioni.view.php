<?php
if (!defined("_BASEDIR_")) exit();
require_once("../config.inc.php");
include_controller("org/convocazioni");

class ConvocazioniView
{
	private $ctrl;
	private $arb;
	private $arb_est;
	private $arb_conv;
	private $turni;
	private $gara;
	
	public function __construct($id_gara)
	{
		$this->ctrl = new ConvocazioniCtrl($id_gara);
		$this->arb = $this->ctrl->getArbitri();
		$this->arb_est = $this->ctrl->getArbitriEsterni();
		$this->arb_conv = $this->ctrl->getConvocati();
		$this->turni = $this->ctrl->getTurni();
		$this->gara = new Gara($id_gara);
	}
	
	public function stampa()
	{
		$lang = Lingua::getParole();
		
		//INFORMAZIONI GARA
		
		$nome = $this->gara->getNome();
		$data = $this->gara->getDataGara()->format('d/m/Y');
		$rimborso = $this->gara->getRimborsoArb();
		
		?>
	<div id="Right" style="width:95%;">
	<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["gara_dettagli"]; ?></h1></div>
	 
	<table class='tr' width='98%' >
		<tr>
		<th width='40%'><div class="thAtleti thAtletiDx"><?php echo $lang["nome_gara"]; ?>:</div>
		</th>
			<td><?php echo $nome; ?></td>
		</tr>
		<tr>
		<th width='40%'><div class="thAtleti thAtletiDx"><?php echo $lang["data_gara"]; ?>:</div>
		</th>
			<td><?php echo $data; ?></td>
		</tr>
		<tr>
		<th width='40%'><div class="thAtleti thAtletiDx"><?php echo $lang["rimb_arb"]; ?>:</div>
		</th>
			<td><?php echo $rimborso." euro"; ?></td>
		</tr>
	</table>
	</div>
	<br>
	
		<?php
		
		$count = 0;
		?>
		<form accept-charset="UTF-8"  method="post" enctype="multipart/form-data">
		<input type="hidden" name="pageid" value="<?php echo md5(time()); ?>" />
		
		<div id="Right" style="width:95%;">
		<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["arbitri"]; ?></h1></div>
		
		<table class="atleti">
		<?php
		foreach($this->arb as $id=>$arb)
		{
			$sel1 = $sel2 = $sel3 = $sel4 = '';
			
			if(in_array($id, $this->arb_conv))
			{
				$check = 'checked="checked"';
				switch($this->turni[$id])
				{
					case 1 : $sel1 = "selected"; break;
					case 2 : $sel2 = "selected"; break;
					case 3 : $sel3 = "selected"; break;
					case 4 : $sel4 = "selected"; break;
				}
			}
			else
				$check = '';
			
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			
			$gen_arb = ucwords(strtolower($arb['cognome'].' '.$arb['nome']));
			$nome_soc = ucwords(strtolower(Societa::nomeFromidAff($arb['idsocieta'])));
			
			echo "<tr class=\"$class\"><td class=\"tipo\">";
			echo "<input type=\"checkbox\" $check id=\"arb_$id\" name=\"arb[$id]\" value=\"$id\" class=\"styled\"></td>";
			echo "<td>
			<select id=\"turni[$id]\" name=\"turni[$id]\">
			<option value=1 $sel1>1 turno</option> 
			<option value=2 $sel2>2 turni</option>
			<option value=3 $sel3>3 turni</option>
			<option value=4 $sel4>4 turni</option>
			</select>
			</td>";
			echo "<td class=\"cognome\">$gen_arb";
			echo "</td>\n";
			echo "<td>$nome_soc</td>";
			echo "</tr>";
			
			$count ++;
		}
		?>
		</table></div>
		
		
		<div id="Right" style="width:95%;">
		<div class="Gare_soc_right"><h1 style="text-align: center;"><?php echo $lang["arbitri_est"]; ?></h1></div>
		
		<table class="atleti">
		<?php
		foreach($this->arb_est as $id=>$arb)
		{
			$sel1 = $sel2 = $sel3 = $sel4 = '';
			
			if(in_array($id, $this->arb_conv))
			{
				$check = 'checked="checked"';
				switch($this->turni[$id])
				{
					case 1 : $sel1 = "selected"; break;
					case 2 : $sel2 = "selected"; break;
					case 3 : $sel3 = "selected"; break;
					case 4 : $sel4 = "selected"; break;
				}
			}
			else
				$check = '';
			
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			
			$gen_arb = ucwords(strtolower($arb['cognome'].' '.$arb['nome']));
			$nome_soc = ucwords(strtolower(Societa::fromId($arb['idsocieta'])->getNome()));
// 			$nome_soc = ucwords(strtolower(Societa::nomeFromidAff($arb['idsocieta'])));
			
			echo "<tr class=\"$class\"><td class=\"tipo\">";
			echo "<input type=\"checkbox\" $check id=\"arb_$id\" name=\"arb[$id]\" value=\"$id\" class=\"styled\"></td>";
			echo "<td>
			<select id=\"turni[$id]\" name=\"turni[$id]\">
			<option value=1 $sel1>1 turno</option> 
			<option value=2 $sel2>2 turni</option>
			<option value=3 $sel3>3 turni</option>
			<option value=4 $sel4>4 turni</option>
			</select>
			</td>";
			echo "<td class=\"cognome\">$gen_arb";
			echo "</td>\n";
			echo "<td>$nome_soc</td>";
			echo "</tr>";
			
			$count ++;
		}
		?>
		</table></div>
		
		<div class="pulsante tr" style="text-align:center">
		<input type="submit" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
		</div>
		<?php
	}
}