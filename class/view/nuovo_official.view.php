<?php
if (!defined("_BASEDIR_")) exit();
include_controller("soc/nuovo_official");

class NuovoOfficialView
{
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new OfficialCtrl();
	}
	
	public function stampa()
	{
		$lang = Lingua::getParole();
		
		?>
	<div align="center">
		<div id="new_coach">
			<form method="post">
			
				<div class="Gare_soc_right"><h1><?php echo $lang["nuovo_official"]; ?></h1></div>
			
				<table>
				<tr>
				<td>
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["nome_iscrizioni"]);?><span class='required'>*</span></label>
				</td>
				<td class="nome">
					<input type='text' style='float:left' name='nome' value="" required>
				</td>
				</tr>
				
				<tr>
				<td>
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["cognome_iscrizioni"]);?><span class='required'>*</span></label>
				</td>
				<td class="cognome">
					<input type='text' style='float:left' name='cognome' value="" required>
				</td>
				</tr>
				
				<tr>
				<td>
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["nascita_iscrizioni"]);?><span class='required'>*</span></label>
				</td>
				<td class="nascita">
					<input type="text" placeholder="<?php echo $lang["formato_data"]; ?>" pattern="\d{1,2}/\d{1,2}/\d{4}" title="<?php echo $lang["formato_data"]; ?>" id="nascita" name="nascita" value="" required>
				</td>
				</tr>
				
				<tr>
				<td>
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["sesso_iscrizioni"]);?><span class='required'>*</span></label>
				</td>
				<td class="sesso">
					<select name="sesso" required>
						<option value="0"></option>
						<option value="1">M</option>
						<option value="2">F</option>
					</select>
				</td>
				</tr>
				
				</table>
				
				<div class="pulsante tr" style="text-align:center">
				<input type="submit" name="submit" value="<?php echo $lang["salva_iscrizioni"]; ?>" />
				</div>
				
			</form>
		</div>
	</div>	
		
		
		<?php
	}
}