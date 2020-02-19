<?php
if (!defined("_BASEDIR_")) exit();
include_view("riepilogo/indiv_soc", "riepilogo/squadre_soc");

class RiepilogoSocietaView {
	/**
	 * @var RiepilogoSocieta
	 */
	protected $ctrl;
	
	/**
	 * @var TabellaIndividuali
	 */
	private $tabind;
	
	/**
	 * @var TabellaSquadre
	 */
	private $tabsq;
	
	/**
	 * @param int $tipout tipo utente
	 */
	public static function stampaPagina($tipout) {
		include_controller("riepilogo_societa");
		include_controller("riepilogo_societa_soc_est");
		include_view("Header", "Template");
		$lang = Lingua::getParole();
		$f = Utente::ORGANIZZATORE;
		$utente = Utente::crea();
		$nome_fed = $utente->getNome();
		$fed_est = false;
		if ($nome_fed == "fki" || $nome_fed == "wuka" || $nome_fed == "tka") 
		{
		    $fed_est = true;    
		}
		if ( $fed_est )
		  $ctrl = new RiepilogoSocietaSocEst($tipout);
		else 
		        $ctrl = new RiepilogoSocieta($tipout);
		
		$head = new Header();
		$head->setStampa(true);
		$templ = new Template($head);
		$templ->includeJs("popup");
	
		$view = new RiepilogoSocietaView($ctrl);
	
		$templ->stampaTagHead(false);
		$view->stampaJavascript();
		echo "</head>";
		$templ->apriBody();
	
		$view->stampaCorpo();
	
		$templ->chiudiBody();
	}
	
	/**
	 * @param RiepilogoSocieta $ctrl
	 * @param int $idsoc
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
		if ($ctrl->isIndividuale())
			$this->tabind = new TabellaIndividuali($ctrl->getGara());
		if ($ctrl->isSquadre())
			$this->tabsq = new TabellaSquadre($ctrl->getGara());
	}
	
	public function stampaJavascript() { ?>
<script type="text/javascript">
function mostraCatOriginale(nome) {
	showPopup("<?php echo Lingua::getParola("categoria_originale"); ?>:<br>"+nome);
}
</script>
<?php 
	}
	
	public function stampaCorpo() {
		if (!$this->ctrl->haIscritti()) {
			$this->noPartecipanti();
		} else {
			$this->tabellaSocieta();
			$this->stampaSocietaComplete();
		}
	}
	
	public function noPartecipanti() {
		echo '<h1>'.Lingua::getParola("no_partecipanti").'</h1>';
	}
	
	public function tabellaSocieta() {
		echo '<h1>'.Lingua::getParola("riepilogo_societa").'</h1>';
		?>
		<table class="atleti" >
		<tr  class="tr">
		<th><div class='thAtleti'></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("societa"); ?></div></th>
		<?php if ($this->ctrl->isIndividuale()) { ?>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_individuali_soc"); ?></div></th>
		<?php } if ($this->ctrl->isSquadre()) {?>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_squadre_soc"); ?></div></th>
		<?php } ?>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_coach"); ?></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("num_arb_conv"); ?></div></th>
		<th><div class='thAtleti'><?php echo Lingua::getParola("prezzo"); ?></div></th>
		<th class="nostampa"><div class='thAtleti'></div></th>
		</tr>
		
		<?php 
		
		$count=1;
		$dettagli = Lingua::getParola("dettagli_societa");
		foreach ($this->ctrl->getSocieta() as $s) {
			/* @var $s Societa */
			$id = $s->getChiave();
			$prezzo = $this->ctrl->getPrezzo($id);
			$ancora = "soc$id";
			$nome = $s->getNome();
			$fed_est = $s->getFedEst();
			if($fed_est === NULL)
				$fed_est = "F.I.A.M.";
				
			if (($count % 2) != 0) $classe = "riga1";
			else $classe = "riga2";

			echo "<tr class=\"$classe\">\n";
			echo "<td class=\"riepilogo_center\">$count</td>\n";
			if(_WKC_MODE_)
				echo "<td class=\"riepilogo_center\">$nome</td>\n";
			else
				echo "<td class=\"riepilogo_center\">$nome - $fed_est</td>\n";
			if ($this->ctrl->isIndividuale()) {
				echo "<td class=\"riepilogo_center\">".$this->ctrl->getNumIndividuali($id);
				echo " (".$this->ctrl->getNumAtletiIndividuali($id).") </td>\n";
			}
			if ($this->ctrl->isSquadre())
				echo "<td class=\"riepilogo_center\">".$this->ctrl->getNumSquadre($id)."</td>\n";
			echo "<td class=\"riepilogo_center\">".$this->ctrl->getNumCoach($id)."</td>\n";
			if($this->ctrl->haArbitri($id))
				echo "<td class=\"riepilogo_center\">".$this->ctrl->getNumArbitri($id)."</td>\n";
			else 
				echo "<td class=\"riepilogo_center\">0</td>\n";
			echo "<td class=\"riepilogo_center\">$prezzo &#8364;</td>\n";
			echo "<td class=\"riepilogo_center nostampa\"><a class=\"smallBut\" href=\"#$ancora\">$dettagli</a></td>\n";
			echo '</tr>';
				
			$count++;
		}
		
		echo '</table>';
	}
	
	public function stampaSocietaComplete() {
		foreach ($this->ctrl->getSocieta() as $soc) {
			/* @var $soc Societa */
			$ids = $soc->getChiave();
			$atl = $this->ctrl->getAtleti($ids);
			$ancora = 'soc'.$soc->getChiave();
			$nome = $soc->getNome();
			$fed_est = $soc->getFedEst();
			if($fed_est === NULL)
				$fed_est = "F.I.A.M.";
			if(_WKC_MODE_)
				echo "<a name=\"$ancora\"></a><h1>$nome</h1>\n";
			else
				echo "<a name=\"$ancora\"></a><h1>$nome - $fed_est</h1>\n";
			$this->stampaCoach($ids);
			$this->stampaArbitri($ids);
			if ($this->ctrl->isIndividuale()) {
				$this->tabind->stampa($atl, $this->ctrl->getIndividuali($ids));
			}
			if ($this->ctrl->isSquadre()) {
				$this->tabsq->stampa($atl, $this->ctrl->getSquadre($ids), $soc->getNomeBreve());
			}
		}
	}
	
	private function stampaCoach($ids) {
		if (!$this->ctrl->haCoach($ids)) return;
		?>
<div class="Gare_soc_right"><h1><?php echo Lingua::getParola("coach"); ?></h1></div>
<table width="100%" class="atleti">
		<?php 
		$c = 0;
		foreach ($this->ctrl->getCoach($ids) as $p) {
			/* @var $c Persona */
			if (($c % 2) == 0) $classe = "riga1";
			else $classe = "riga2";
			
			?>
				<tr class="<?php echo $classe; ?>">
				<td class="riepilogo_center" width="20"><?php echo ($c+1); ?></td>
				<td class="riepilogo_center"><?php echo $p->getCognome()." ".$p->getNome(); ?></td>
			<?php 
			$c++;
		}
		?>
</table>
		<?php 
	}
	
	private function stampaArbitri($ids) {
		if (!$this->ctrl->haArbitri($ids)) return;
		?>
	<div class="Gare_soc_right"><h1><?php echo Lingua::getParola("arbitri"); ?></h1></div>
	<table width="100%" class="atleti">
			<?php 
			$c = 0;
			foreach ($this->ctrl->getArbitri($ids) as $p) {
				/* @var $c Persona */
				if (($c % 2) == 0) $classe = "riga1";
				else $classe = "riga2";
				
				?>
					<tr class="<?php echo $classe; ?>">
					<td class="riepilogo_center" width="20"><?php echo ($c+1); ?></td>
					<td class="riepilogo_center"><?php echo $p->getCognome()." ".$p->getNome(); ?></td>
				<?php 
				$c++;
			}
			?>
	</table>
			<?php 
		}
		
	
}
?>