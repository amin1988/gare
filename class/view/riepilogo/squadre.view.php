<?php
if (!defined("_BASEDIR_")) exit();
include_view("riepilogo/base");

class RiepilogoCompletoSquadreView extends RiepilogoCompletoView {
	
	/**
	 * @param RiepilogoSquadreCompleto $ctrl
	 * @param int $idsoc
	 */
	public function __construct($ctrl, $idsoc = NULL) {
		parent::__construct($ctrl, $idsoc);
	}
	
	public function stampaCorpo($squadre, $resp=false) {
		$this->altroRiepilogo($squadre, $resp);
		if (!$this->ctrl->haIscritti()) {
			$this->noPartecipanti();
		} else {
			if (!$this->ctrl->mostraAccorpamenti()) {
				echo '<h2 style="color:red;text-decoration:underline;">'.Lingua::getParola("msg_non_accorpate").'</h2>';
			}
			$this->tabellaCategorie();
			$this->stampaCategorieComplete();
		}
	}
	
	public function stampaCategorieComplete() {
		foreach ($this->ctrl->getCategorie() as $c) {
			$this->stampaSquadreCategoria($c);
		}
	}
	
	/**
	 * @param Categoria $c
	 */
	private function stampaSquadreCategoria($c) {
		$npart = $this->ctrl->getNumPartecipanti($c->getChiave());
		if ($npart[0] == 0)
			return; //salta le categorie vuote
		$iscrpool = $this->ctrl->getSquadre($c->getChiave());
		ksort($iscrpool);
		foreach ($iscrpool as $pool => $iscr) {
			$this->stampaInizioCategoria($c, $pool);
			echo '<table class="atleti" >';
			foreach ($iscr as $sq) {
				/* @var $sq Squadra */
				$nomesq = $this->ctrl->getNomeSquadra($sq);
				$nomesoc = $this->ctrl->getNomeSocieta($sq);
				echo '<tr class="tr"><th colspan="4"><div class="thSquadra">';
				echo "$nomesq ($nomesoc)";
				if ($sq->getSocieta() == $this->idsoc)
					echo ' <img src="'._PATH_ROOT_.'img/proprio.png"> ';
				if ($sq->isAccorpato())
					$this->stampaIconaAccorpato($sq, false);
				echo "</div></th></tr>\n";
				$count=0;
				foreach ($sq->getComponenti() as $ida) {
					$a = $this->ctrl->getAtleta($sq, $ida);
					if (($count % 2) == 0) $classe = "riga1";
					else $classe = "riga2";
					?>
	<tr class="<?php echo $classe; ?>">
	<td class="riepilogo_center"><?php echo $a->getCognome()." ".$a->getNome(); ?></td>
	<td class='riepilogo_center'><?php echo $this->ctrl->getNomeSesso($a); ?></td>
	<td class='riepilogo_center'><?php echo $a->getDataNascita()->format("d/m/Y"); ?></td>
	<td class='riepilogo_center'><?php echo $this->ctrl->getNomeCintura($sq, $ida); ?></td>
	</tr>
<?php	
				$count++;	
				}
			}
			echo '</table>';
		} //foreach pool	
	} //function stampaSquadreCategoria
}

?>