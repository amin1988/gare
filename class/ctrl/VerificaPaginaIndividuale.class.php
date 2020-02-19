<?php
if (!defined("_BASEDIR_")) exit();

class VerificaPaginaIndividuale {
	/**
	 * @var Gara
	 */
	private $gara;
	
	public function __construct($gara) {
		$this->gara = $gara;
	}
	
	/**
	 * @param PaginaMenu $pag
	 */
	public function verificaPagina($pag) {
		$g = $this->gara;
		switch ($pag->getInfo()) {
			case 0: //individuale
				return $g->isIndividuale();
			case 1: //squadre
				return $g->isSquadre();
			case 10: //completo individuale
				return $g->isIndividuale() && $g->iscrizioniChiuse();
			case 11: //completo squadre
				return $g->isSquadre() && $g->iscrizioniChiuse();
			case -1: //iscrizioni
				return !$g->iscrizioniChiuse();
		}
	}
}
