<?php
if (!defined("_BASEDIR_")) exit();
include_class("Foto");
include_model("ArbitroEsterno");
include_esterni("ArbitroAffiliato");

class ArbitroView {
	/**
	 * @var IscriviBase
	 */
	private $ctrl;
	
	private $action;
	
	public function __construct($ctrl, $action=NULL) {
		$this->ctrl = $ctrl;
		$this->action = $action; 
	}
	
	public function stampaJs() {
?>
<script type="text/javascript">
var loadatlc = true;

function apri(cont, idimg) {
	var img = document.getElementById(idimg);
	if (cont.style.display != "none") {
		cont.style.display="none";
		img.src = "<?php echo _PATH_ROOT_; ?>img/down.png";
	} else {
		cont.style.display="";
		img.src = "<?php echo _PATH_ROOT_; ?>img/up.png";
	}
}

function apriAtletiCoach() {
	var cont = document.getElementById("atlclist");
	apri(cont,"mostra_atlc");
	if (loadatlc) {
		cont.innerHTML = '<tr><td colspan="4" align="center"><img src="<?php echo _PATH_ROOT_; ?>img/wait.gif"></td></tr>';
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/atleticoach.php?id=<?php echo $this->ctrl->getGara()->getChiave(); ?>", cont, mostraAtletiCoach);
	}
}

function mostraAtletiCoach(json, cont) {
	if (json == "null") return;
	
	var res = JSON.parse(json);
	cont.innerHTML = "";
//	var txt = "";
	for (var i=0; i<res.length; i++) {
		var id=res[i].id;
		if (document.getElementById("coach_"+id) != null)
			continue;
		var riga=document.createElement("tr");
		riga.className = 'riga' + ((i%2)+1);
		
		var td=document.createElement("td");
		td.className = "tipo";
		var s=document.createElement("span");
		s.className="checkbox";
		var c=document.createElement("input");
		c.type='checkbox';
		c.id="coach_"+id;
		c.className='styled';
		c.name='coach[<?php echo Persona::TIPO_ATLETA; ?>]['+id+']';
		c.value=id;
		<?php if (!is_null($this->action)) { ?>
		c.setAttribute("onchange","<?php echo addslashes($this->action) ?>");
		<?php } //if action != null?>
		initSpan(s);
		td.appendChild(s);
		td.appendChild(c);
		riga.appendChild(td);

		td=document.createElement("td");
		td.className="cognome";
		td.innerHTML = res[i].nome;
		riga.appendChild(td);

		td=document.createElement("td");
		td.align="right";
		var foto = "<?php echo Lingua::getParola("foto"); ?>: ";
		if (res[i].foto == "")
			foto += '<input name="foto['+id+']" id="foto_'+id+'" type="file">'
					+ "<button onclick=\"cancFoto('foto_"+id+"'); return false;\"><?php echo Lingua::getParola("cancella_foto"); ?></button>";
		else
			foto += '<a href="<?php echo _PATH_ROOT_; ?>'+res[i].foto+'" target="_blank">'
					+ '<img src="<?php echo _PATH_ROOT_; ?>img/foto.png" align="middle"></a>';
		td.innerHTML = foto;
		riga.appendChild(td);

		cont.appendChild(riga);
//		txt += '<tr class="riga' + ((i%2)+1);
//		txt += '"><td class="tipo"><span class="checkbox"></span><input class="styled" type="checkbox"></td>';
//		txt += '<td class="cognome">'+res[1][i].nome+'</td></tr>';
	}
//	cont.innerHTML = txt;
	
	loadatlc = false;
}

function cancFoto(id) {
	document.getElementById(id).value="";
}
</script>
<?php 
	} //function stampaJs()
	
	public function stampaRiepilogo() {
		$arb = $this->ctrl->getArbitri();
		$turni = ArbitroAffiliato::getTurni($this->ctrl->getGara()->getChiave());
		if (count($arb) == 0) {
			echo "<h2>".Lingua::getParola("no_arb_conv")."</h2>\n";
		} else {
			echo '<table width="100%" class="atleti">';
			$c = 0;
			foreach ($this->ctrl->getArbitri() as $p) {
				/* @var $p Persona */
				if (($c % 2) == 0) $classe = "riga1";
				else $classe = "riga2";
				
				if(!$this->ctrl->getUtenteSocieta()->getSocieta()->isAffiliata())
				{
					//arbitro esterno
					$p = ArbitroEsterno::fromId($p->getPersona());
				}
				
				$tur_arb = $turni[$p->getChiave()];
				if($tur_arb == 1)
					$tur_str = Lingua::getParola("turno");
				else
					$tur_str = Lingua::getParola("turni");
			
				?>
					<tr class="<?php echo $classe; ?>">
					<td class="riepilogo_center" width="20"><?php echo ($c+1); ?></td>
					<td class="riepilogo_center"><?php echo $p->getCognome()." ".$p->getNome()." - $tur_arb $tur_str"; ?></td>
				<?php 
				$c++;
			}
			echo "</table>\n";	
		}	
	}
	
	public function stampaArbitri($idgara, $id_soc, $id_est=NULL)
	{
		if($id_est === NULL)
		{
			$this->tabellaArbitri($idgara, $id_soc);
			$this->tabellaArbConv($idgara, $id_soc);
		}
		else
		{
			$this->tabellaArbitri($idgara, $id_soc, $id_est);
			$this->tabellaArbConv($idgara, $id_soc, $id_est);
		}
	}
	
	public function tabellaArbitri($id_gara,$id_soc, $id_est=NULL)
	{
		if($id_est === NULL)
		{
			$arb = ArbitroAffiliato::getConvocatiGara($id_gara, $id_soc,1);
			$est = false;
		}
		else 
		{
			$arb = ArbitroEsterno::getConvocatiGara($id_gara, $id_est,1);
			$est = true;
		}
		echo '<table class="atleti">';
		if(count($arb) > 0)
			$this->bodyArb($arb, true, $est);
		else
			echo "<h2>".Lingua::getParola("no_arb_conf")."</h2>";
		echo "</table>\n";
	}
	
	public function tabellaArbConv($id_gara, $id_soc, $id_est=NULL)
	{
		if($id_est === NULL)
		{
			$arb = ArbitroAffiliato::getConvocatiGara($id_gara, $id_soc,0);
			$est = false;
		}
		else 
		{
			$arb = ArbitroEsterno::getConvocatiGara($id_gara, $id_est,0);
			$est = true;
		}
		
		$this->tabellaArbDaConf("arbitri", "arb", $arb, NULL, $est);
		/*
		echo '<table class="atleti">';
		if(count($arb) > 0)
			$this->bodyArb($arb, false);
		else
			echo "<h2>".Lingua::getParola("no_arb_conv")."</h2>";
		echo "</table>\n";
		*/
	}
	
	public function tabellaCoach() {
		$coach = $this->ctrl->getCoach();
		echo '<table class="atleti">';
		if (count($coach) > 0)
			$this->bodyCoach($coach, true);
		else
			echo "<h2>".Lingua::getParola("no_coach")."</h2>";
		echo "</table>\n";
	}
	
	public function tabellaTecnici() {
		$this->tabellaNuoviCoach("tecnici_coach", "tec", $this->ctrl->getTecnici());
	}
	
	public function tabellaNere() {
		$this->tabellaNuoviCoach("nere_coach", "nere", $this->ctrl->getNere());
	}
	
	public function tabellaAtletiCoach() {
		$this->tabellaNuoviCoach("altri_coach", "atlc", array(), "apriAtletiCoach()");
	}
	
	protected function tabellaArbDaConf($titolo, $id, $lista, $js=NULL,$est=false) {//TODO MODIFICA
			
		if(count($lista) > 0)
		{
			if (is_null($js))
				$js = "apri(document.getElementById('{$id}list'),'mostra_$id')";
			?>
				<table class="atleti">
				<thead>
				<tr class="tr">
				<th class="tipo"><div class='thAtleti'><img src="<?php echo _PATH_ROOT_; ?>img/down.png" class="mostra" id="mostra_<?php echo $id; ?>" onclick="javascript:<?php echo $js; ?>;"></div></th>
				<th colspan=3><div class='thAtleti'><?php echo Lingua::getParola($titolo); ?></div></th>
				</tr>
				</thead>
				<tbody id="<?php echo $id; ?>list" style="display:none">
					<?php 
					$this->bodyArb($lista, false, $est);
					?>
				</tbody>
				</table>
				<?php
		}
		else
			echo "<table><h2>".Lingua::getParola("no_arb_conv")."</h2></table>\n";
		} //function tabellaArbDaConf
	
	private function bodyArb($lista, $value, $est=false)
	{
		if ($value)
			$check = 'checked="checked"';
		else
			$check = '';
		if (is_null($this->action))
			$act = '';
		else
			$act = "onchange=\"{$this->action}\"";
		$count = 0;

		foreach($lista as $id_a=>$cont)
		{
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			
			if(!$est)
			{
				$row = ArbitroAffiliato::extFromId($cont);
				$id = $row['idtesserato'];
			}
			else 
			{
				$row = ArbitroEsterno::rowFromId($cont);
				$id = $row['idarbitro'];
			}
			
			$nome = $row['nome'];
			$cogn = $row['cognome'];
// 			$id = $row['idtesserato'];
			$tipo = 3;
			
			echo "<tr class=\"$class\"><td class=\"tipo\">";
			echo "<input type=\"checkbox\" $check $act id=\"arb_$id\" name=\"arb[$tipo][$id]\" value=\"$id\" class=\"styled\">";
			echo "</td><td class=\"cognome\">$cogn $nome";
			echo "</td>\n";
			
			echo "</tr>";
			$count++;
			
			//echo $row['idtesserato'].' - '.$row['cognome'].' '.$row['nome'].'<br>';
		}
	}
	
			
	/**
	 * @param Persona[] $lista
	 */
	private function bodyCoach($lista, $value) {
		if ($value)
			$check = 'checked="checked"';
		else 
			$check = '';
		if (is_null($this->action))
			$act = '';
		else
			$act = "onchange=\"{$this->action}\"";
		$count = 0;
		$foto = $this->ctrl->isFotoCoachObbligatoria();
		foreach ($lista as $p) {
			/* @var $p Persona */
			if ($count%2 == 0)
				$class = "riga1";
			else
				$class = "riga2";
			$nome = $p->getNome();
			$cogn = $p->getCognome();
			$id = $p->getChiave();
			$tipo = $p->getTipo();
			echo "<tr class=\"$class\"><td class=\"tipo\">";
			echo "<input type=\"checkbox\" $check $act id=\"coach_$id\" name=\"coach[$tipo][$id]\" value=\"$id\" class=\"styled\">";
			echo "</td><td class=\"cognome\">$cogn $nome";
			if ($this->ctrl->getErroriCoach()->haErroreFoto($id)) {
				echo '<br><span style="color:red;text-transform:none;">';
				echo $this->ctrl->getErroriCoach()->toStringFoto($id);
				echo "</span>";
			}
			echo "</td>\n";
			if ($foto) {
				echo "<td align=\"right\">".Lingua::getParola("foto").": ";
				$foto = Foto::persona($p);
				if ($foto->esiste()) {
					$url = _PATH_ROOT_.$foto->getFoto();
					echo "<a href=\"$url\" target=\"_blank\"><img src=\""._PATH_ROOT_."img/foto.png\" align=\"middle\" id=\"foto_$id\"></a>";
				} else {
					echo "<input type=\"file\" name=\"foto[$id]\" id=\"foto_$id\" accept=\"image/jpeg\">";
					//TODO lingua 
					echo "<button onclick=\"cancFoto('foto_$id'); return false;\">".Lingua::getParola("cancella_foto")."</button>";
				}
				echo "</td>";
			}
			echo "</tr>";
			$count++;
		}
	}
}
?>