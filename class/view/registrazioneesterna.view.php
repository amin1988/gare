<?php
if (!defined("_BASEDIR_")) exit();
include_controller("registrazioneesterna");
include_view("SelectZone");
include_model("Nazione");

class RegistraEsternaView
{
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new RegistraEsternaCtrl();
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
		
		$zone = new SelectZone();
		$zone->setZona(7, 1);
		//BODY
		$zone->stampaJavascript();
		?>
		<script type="text/javascript">
		function mostraZone(nome, sel, el, val, liv) {
		var span = document.createElement("span");
		var lbl = document.createElement("label");
		lbl.style.cssText = "width:200px";
		lbl.className = 'iconic ';
		lbl.innerHTML = nome + "<span class='required'>*</span>";
		span.appendChild(lbl);
		span.appendChild(sel);
		var clear = document.createElement("div");
		clear.style.cssText="clear:both;";
		span.appendChild(clear);
		//if (lastreq != val) return;
		el.parentNode.insertBefore(span,el.nextSibling);
		return span;
		}
		</script>
		
	<div align="center">
			<div id="form_login">
				<form method="post">
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["societa"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='nome' value="" required>
					<div style='clear:both;'></div>

					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["abbrevia"]);?><span class='required'>*</span></label>
					<input type='text' style='float:left' name='nomebreve' value="" required>
					<div style='clear:both;'></div>
					
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["stile_iscrizioni"]);?><span class='required'>*</span></label>
					<select name='idstile' required>
					<option></option>
					<option value='1'>Gojuryu</option>
					<option value='2'>Shitoryu</option>
					<option value='3'>Shotokan</option>
					<option value='4'>Wadoryu</option></select>
					<div style='clear:both;'></div>
					
					<span id="zonabase">
					<label style='width:200px' class='iconic '><?php echo $zone->getNomeLivelloTop(); //echo $livzonatop->getNome(); ?><span class='required'>*</span></label>
					<?php $zone->stampaSelectTop("idzona"); ?>
					<!-- <select onchange="cambiaZona(0, this)">
					<option value=""></option>
					<?php 
					/*
					foreach ($zonetop as $id => $z) {
						echo "<option value=\"$id\">".$z->getNome()."</option>\n";
					}
					*/
					?>
					</select> -->
					</span>
					<div style='clear:both;'></div>
					
					<?php 
					$sub = $zone->getNumSubzone();
					for($i=1; $i<=$sub; $i++) {
						echo "<span><label style='width:200px' class='iconic '>";
						echo $zone->getNomeLivelloSub($i);
						echo "<span class='required'>*</span></label>";
						$zone->stampaSelectSub($i,"idzonasub");
						echo "<div style='clear:both;'></div></span>";
					}
					?>
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
					
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["fed_est"]);?><span class='required'>*</span></label>
					<!--<input type='text' style='float:left' name='fed_est' value="" required> -->
                                        
                                        <label style='width:50px'>
                                        <select  name='fed_est' required>
					<option></option>
					<option value='fki'>FKI</option>
					<option value='tka'>TKA</option>
					<option value='wuka'>WUKA</option>
                                        </select>
                                        </label>
                                        
                                        
					<div style='clear:both;'></div>
					
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
}