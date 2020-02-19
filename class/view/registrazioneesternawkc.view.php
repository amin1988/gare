<?php
if (!defined("_BASEDIR_")) exit();
include_controller("registrazioneesterna");
include_view("SelectZone");
include_model("Nazione");

class RegistraEsternaWKCView
{
	private $ctrl;
	
	public function __construct($wkc=false)
	{
		$this->ctrl = new RegistraEsternaCtrl($wkc);
	}
	
	public function stampa()
	{
		if(isset($_SESSION["err_est"]))
				{
					switch($_SESSION["err_est"])
					{
						case RegistraEsternaCtrl::EMAIL_MIS : echo "<script type='text/javascript' charset=\"UTF-8\">alert(\"Email inserita non corretta\");</script>";
																unset($_SESSION["err_est"]);
																break;
						case RegistraEsternaCtrl::RES_MIS : echo "<script type='text/javascript' charset=\"UTF-8\">alert(\"Risultato non corretto.\");</script>";
																unset($_SESSION["err_est"]);
																break;
						case RegistraEsternaCtrl::SOC_PRE : echo "<script type='text/javascript' charset=\"UTF-8\">alert(\"La societ\u00E0 risulta gi\u00E0 registrata al sistema. Non \u00E8 necessario registrarla novamente per partecipare alla gare.\");</script>";
															unset($_SESSION["err_est"]);
															break;
						default: echo "<script type='text/javascript'>alert(\"Errore\");</script>"; unset($_SESSION["err_est"]);
					}
				}
		$lang = Lingua::getParole();
		
		//BODY
		?>
		
	<div align="center">
			<div id="form_login">
				<form method="post">
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["nome_fed"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='nome' value="" required>
					<div style='clear:both;'></div>

					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["abbrevia"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='nomebreve' value="" required>
					<div style='clear:both;'></div>
					
					<!-- 
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["stile_iscrizioni"]);?><span class='required'>*</span></label>
					<select name='idstile' required>
					<option></option>
					<option value='1'>Gojuryu</option>
					<option value='2'>Shitoryu</option>
					<option value='3'>Shotokan</option>
					<option value='4'>Wadoryu</option>
					<option value='5'>Shorinryu</option></select>
					<div style='clear:both;'></div>
					-->
					 
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["nazione"]);?><span class='required'>*</label>
					<select name="nazione" required>
					<?php
					echo '<option value="-1"></option>';
					echo $this->getElencoCode(Nazione::listaNazioni(), 106);
					?>
					</select>
					<div style='clear:both;'></div>
					
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["contatto"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='contatto' value="" required>
					<div style='clear:both;'></div>
					
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["email"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='email' value="" required>
					<div style='clear:both;'></div>
					
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["conf_email"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='conf_email' value="" required>
					<div style='clear:both;'></div>
					
					<!-- 
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["fed_est"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='fed_est' value="" required>
					<div style='clear:both;'></div>
					-->
					<br><br>
					
					<label style='width:200px' class='iconic '><?php echo $lang["ctrl65"];?><span class='required'>*</span></label>
					<input type='number' style='float:left' name='res_eq' value="" min="1" max="30" required>
					<div style='clear:both;'></div>
					
					<br>
					
					<input type='submit' name='invia' value='<?php echo ucfirst($lang["form_reg"]);?>' id='form_login-submit' style='left:100px;float:left'>
				</form>
			</div>
	</div>
		<?php
	}
	
	/**
	 * @param array $valori array di valori con metodo getNome()
	 * @param int $selid id da selezionare
	 */
	protected function getElencoCode($valori, $selid) {
		$r = "";
		foreach ($valori as $id => $v) {
			$r .= "<option value=\"$id\"";
			if ($selid == $id) $r .= ' selected="selected"';
			$r .= ">".$v->getNome()."</option>";
		}
		return $r;
	}
	
}