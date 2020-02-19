<?php
if (!defined("_BASEDIR_")) exit();
include_controller("soc/nuovo_atleta");

class NuovoAtletaView
{
	private $ctrl;
	
	public function __construct()
	{
		$this->ctrl = new AtletaEsternoCtrl();
	}
	
	public function stampa()
	{
		$lang = Lingua::getParole();
		
		?>
	<div align="center">
		<div id="new_coach">
			<form method="post">
			
				<div class="Gare_soc_right"><h1><?php echo $lang["nuovo_atleta"]; ?></h1></div>
			
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
				
				<tr>
				<td>
					<label style='width:200px' class='iconic '><?php echo ucfirst($lang["cintura_iscrizioni"]);?><span class='required'>*</span></label>
				</td>
				<td class="sesso">
					<select name="cintura" required>
					<?php
					echo '<option value="-1"></option>';
					echo $this->getElencoCode(Cintura::listaCinture(), NULL);
					?>
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
	
	/**
	 * @param int $ida id atleta
	 * @param int $pre prefisso del nome del select
	 * @param string $prima in uscita contiene il valore da stampare prima del contenuto
	 * @param string $dopo in uscita contiene il valore da stampare dopo il contenuto
	 */
	protected function getCintureSelect($ida, $pre, &$prima, &$dopo) {
		if ($this->isErrato("sesso"))
			$prima = '<select class="err"';
		else
			$prima = '<select';
		$prima .= " name=\"{$pre}cintura[$ida]\">";
		$dopo = '</select>';
	}
}