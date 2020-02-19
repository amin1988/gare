<?php
if (!defined("_BASEDIR_")) exit();

class TabellaSquadre {
	/**
	 * @var Categoria[]
	 */
	private $categorie;
	
	/**
	 * @param Gara $gara
	 */
	public function __construct($gara) {
		$this->categorie = $gara->getCategorieSquadre();
	}
	
	/**
	 * @param Atleta[] $atleti
	 * @param Squadra[] $squadre
	 * @param string $nome
	 */
	public function stampa($atleti, $squadre, $nome = NULL) {
		if (count($squadre) == 0) return;
		$lang = Lingua::getParole();
		?>
		<br>
		<div class="Gare_soc_right"><h1><?php echo $lang["squadre"]; ?></h1></div>
		<table width="100%" class="atleti" id="atleti" >
		<?php
		$poolstr = $lang["pool"];
		if (is_null($nome))
			$nomesq = $lang["squadra"] . " ";
		else
			$nomesq = "$nome ";
		foreach ($squadre as $sq) {
			/* @var $sq Squadra */
			?>
			<tr class="tr">
			<th colspan="4"><div class='thSquadra'>
			<?php 
			echo $nomesq . $sq->getNumero() .' - '. $this->getNomeCategoria($sq->getCategoriaFinale()); 
			if ($sq->isAccorpato()) {
				$nomeorig = $this->getNomeCategoria($sq->getCategoria());
				echo ' <img style="cursor:pointer;" src="';
				echo _PATH_ROOT_."img/spostato.png\" title=\"$nomeorig\" onclick=\"javascript:mostraCatOriginale('$nomeorig')\">";
			} else if ($sq->isSeparato()) {
				echo ' - '.str_replace("<NUM>", $sq->getPool(), $poolstr);
				echo ' <img src="'._PATH_ROOT_.'img/separa.png">';
			}
			?>
			</div></td>
			</tr>
				
		<?php 
			$c=0;
			foreach ($sq->getComponenti() as $ida) {
				/* @var $a Atleta */
				$a = $atleti[$ida];
				if (($c % 2) == 0) $classe = "riga1";
				else $classe = "riga2";
				
				?>
			<tr class="<?php echo $classe; ?>">
			<td class="riepilogo_center"><?php echo $a->getCognome()." ".$a->getNome(); ?></td>
			<td class='riepilogo_center'><?php echo $this->getNomeSesso($a); ?></td>
			<td class='riepilogo_center'><?php echo $a->getDataNascita()->format("d/m/Y"); ?></td>
			<td class='riepilogo_center'><?php echo $this->getNomeCintura($sq, $a); ?></td>
			</tr>	
		<?php	
				$c++;	
			}
		}
		
		echo '</table>';		
	}
	
	/**
	 * @param Atleta $a
	 * @return string
	 */
	private function getNomeSesso($a) {
		return Sesso::toStringBreve($a->getSesso());
	}	
	
	/**
	 * @param Squadra $sq
	 * @param Atleta $a
	 * @return string
	 */
	private function getNomeCintura($sq, $a) {
		$idc = $sq->getCinturaComponente($a->getChiave());
		return Cintura::getCintura($idc)->getNome();
	}
	
	private function getNomeCategoria($idcat) {
		return $this->categorie[$idcat]->getNome();
	}
}