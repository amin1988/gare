<?php
if (!defined("_BASEDIR_")) exit();
include_view("riepilogo/base");

class RiepilogoCompletoIndividualeView extends RiepilogoCompletoView {
	private $peso_tit;
	
	/**
	 * @param int $tipout tipo utente
	 */
	public static function stampaPagina($tipout, $resp=false) {
		include_controller("riepilogo_comp_ind");
		include_view("Header", "Template");
		$lang = Lingua::getParole();
		
		$ctrl = new RiepilogoIndividualeCompleto($tipout);
		$head = new Header();
		$head->setStampa(true);
		$templ = new Template($head);
		$templ->includeJs("popup");
		
		$view = new RiepilogoCompletoIndividualeView($ctrl);
		
		$templ->stampaTagHead(false);
		$view->stampaJavascript();
		echo "</head>";
		$templ->apriBody();
		
		$view->stampaCorpo(true, $resp);
		
		$templ->chiudiBody();
	}
	
	/**
	 * @param RiepilogoCompletoIndividuale $ctrl
	 * @param int $idsoc
	 */
	public function __construct($ctrl, $idsoc = NULL) {
		parent::__construct($ctrl, $idsoc);
		if ($ctrl->getGara()->usaPeso()) {
			$this->peso_tit = Lingua::getParola("peso_iscrizioni");
		} else {
			$this->peso_tit = Lingua::getParola("altezza_iscrizioni");
		}
		
	}
	
	public function stampaCorpo($squadre, $resp=false) {
		$this->altroRiepilogo($squadre, $resp);
		if (!$this->ctrl->haIscritti()) {
			$this->noPartecipanti();
		} else {
			if (!$this->ctrl->mostraAccorpamenti()) {
				echo '<h2 style="color:red;text-decoration:underline;">'.Lingua::getParola("msg_non_accorpate").'</h2>';
			}
			if(!_WKC_MODE_ || $resp)
			{
				$this->tabellaCategorie();
				$this->stampaCategorieComplete();
			}
		}
	}
	
	public function stampaCategorieComplete() {
		foreach ($this->ctrl->getCategorie() as $c) {
			$this->stampaAtletiCategoria($c);
		}
	}
	
	/**
	 * @param Categoria $c
	 */
	private function stampaAtletiCategoria($c) {
		$lang = Lingua::getParole();
		$mostraAcc = $this->ctrl->mostraAccorpamenti();
		/* @var $c Categoria */
		$npart = $this->ctrl->getNumPartecipanti($c->getChiave());
		if ($npart[0] == 0)
			return; //salta le categorie vuote
		$iscrpool = $this->ctrl->getIscritti($c->getChiave());
		$hastile = $this->ctrl->mostraStile($c);
		$hapeso = $this->ctrl->mostraPeso($c);
		ksort($iscrpool);
		foreach ($iscrpool as $pool=>$iscr) {
			$this->stampaInizioCategoria($c,$pool);
			?>
		<table class="atleti" >
		<tr class="tr">
		<th><div class="thAtleti"></div></th>
		<th colspan="2"><div class='thAtleti'><?php echo $lang["cognome_iscrizioni"].'/'.$lang["nome_iscrizioni"]; ?></div></th>
		<th><div class="thAtleti" ><?php echo ucfirst($lang["societa"]); ?></div></th>
		<th><div class="thAtleti" ><?php echo $lang["sesso_iscrizioni"]; ?></div></th>
		<th><div class='thAtleti'><?php echo $lang["nascita_iscrizioni"]; ?></div></th>
		<th><div class='thAtleti'><?php echo $lang["cintura_iscrizioni"]; ?></div></th>
		<?php if ($hastile) { ?>
		<th><div class='thAtleti'><?php echo $lang["stile_iscrizioni"]; ?></div></th>
		<?php }
		if ($hapeso) {?>
		<th><div class='thAtleti '><?php echo $this->peso_tit; ?></div></th>
		<?php } ?>
		</tr>
		
		<?php
			$count=0;
			foreach ($iscr as $i) {
				/* @var $i IscrittoIndividuale */
				/* @var $a Atleta */
				$a = $this->ctrl->getAtleta($i);
				if (($count % 2) == 0) $classe = "riga1";
				else $classe = "riga2";
		?>
			<tr class="<?php echo $classe; ?>">
			<td class="riepilogo_center"><?php echo ($count+1); ?></td>
			<td class="riepilogo_center"><?php echo $a->getCognome()." ".$a->getNome(); ?></td>
			<td class="riepilogo_center">
			<?php 
				$proprio = $a->getSocieta() == $this->idsoc;
				if ($mostraAcc && $i->isAccorpato()) 
					$this->stampaIconaAccorpato($i, $proprio);
				else if ($proprio)
					echo '<img src="'._PATH_ROOT_.'img/proprio.png">';
				
			?>
			</td>
			<td class='riepilogo_center'><?php echo $this->ctrl->getNomeSocieta($a); ?></td>
			<td class='riepilogo_center'><?php echo $this->ctrl->getNomeSesso($a); ?></td>
			<td class='riepilogo_center'><?php echo $a->getDataNascita()->format("d/m/Y"); ?></td>
			<td class='riepilogo_center'><?php echo $this->ctrl->getNomeCintura($i); ?></td>
		<?php if ($hastile) { ?>
			<td class='riepilogo_center'><?php echo $this->ctrl->getStile($i); ?></td>
		<?php }
		if ($hapeso) {?>
			<td class='riepilogo_center'><?php echo $this->ctrl->getPeso($i); ?></td>
		<?php } ?>
			
			</tr>	
		<?php	
				$count++;	
			} //foreach iscritto
		?>
		</table>
		<?php 
		} //foreach pool		
	} //function stampaAtletiCategoria
}
?>