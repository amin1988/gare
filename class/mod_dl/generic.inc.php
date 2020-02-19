<?php
if (!defined("_BASEDIR_")) exit();
include_model("Gara", "IscrittoIndividuale", "Squadra", "Societa");

$ctrl = new DownloadIscrizioniGenerale();
$ctrl->setHeader();
$ctrl->stampaContenuto();

/**
 * Codici:
 * A	atleta
 * C	categoria
 * G	gara
 * H	coach
 * I	iscritto individuale
 * M	componente squadra (membro)
 * P	società (palestra)
 * T	squadra (team)
 * V	versione
 */
class DownloadIscrizioniGenerale {
	const FINE_RIGA = "#\r\n";
	
	/**
	 * @var string
	 */
	private $file;
	/**
	 * @var Gara
	 */
	private $gara;
	
	/**
	 * @var Categoria[] formato: idcategoria => Categoria
	 */
	private $cat;
	
	public function __construct() {
		$this->gara = new Gara($_GET["id"]);
		if (!$this->gara->esiste()) {
			exit(Lingua::getParola("errore"));
		}
		$this->cat = Categoria::listaGara($this->gara->getChiave(), false);
		
		$this->file = "iscrizioni.".$this->gara->getChiave().".iscr";
	}
	
	public function setHeader() {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream; charset="utf-8');
		header('Content-Disposition: attachment; filename='.$this->file);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
// 		echo "<pre>";
	}
	
	public function stampaContenuto() {
		echo ":V=2".self::FINE_RIGA;
		if ($this->gara->isIndividuale())
			$indiv = IscrittoIndividuale::listaGara($this->gara->getChiave());
		else 
			$indiv = array();
		if ($this->gara->isSquadre())
			$squadre = Squadra::listaGara($this->gara->getChiave());
		else
			$squadre = array();
		
		// formato: idsoc => idatleta[]
		$atlsoc = array();
		//divide iscritti per societa
		foreach ($indiv as $ii) {
			/* @var $ii IscrittoIndividuale */
			$ida = $ii->getAtleta();
			$atlsoc[$ii->getSocieta()][$ida] = $ida;
		}
		foreach ($squadre as $sq) {
			/* @var $sq Squadra */
			$ids = $sq->getSocieta();
			foreach ($sq->getComponenti() as $ida) {
				$atlsoc[$ids][$ida] = $ida;
			}
		}
		
		$coach = Coach::lista($this->gara->getChiave());
		
		$this->stampaGara($this->gara);
		$this->stampaCategorie($this->cat);
		//carica gli atleti
		foreach (array_keys($atlsoc) as $idsoc) {
			$soc = new Societa($idsoc);
			$this->stampaSocieta($soc);
			if (isset($coach[$idsoc]))
				$this->stampaCoach($soc, $coach[$idsoc]);
			foreach($soc->getAtleti($atlsoc[$idsoc]) as $a)
				$this->stampaAtleta($a, $idsoc);
		}
		
		//salva le iscrizioni
		foreach ($indiv as $ii)
			$this->stampaIscrittoIndividuale($ii);
		foreach ($squadre as $sq) {
			$this->stampaSquadra($sq);
		}
	}
	
	private static function escape($string) {
		return str_replace(array("%",",","#"), array("%25","%2C","%23"), $string);
	}
	
	/**
	 * @param Gara $g
	 */
	private function stampaGara($g) {
		$id = $g->getChiave();
		$nome = self::escape($g->getNome());
		$data = $g->getDataGara()->format("Y-m-d");
		$fine = $g->getDataFineGara();
		if (is_null($fine))
			$fine = $data;
		else
			$fine = $fine->format("Y-m-d");
		echo ":G=$id,$nome,$data,$fine".self::FINE_RIGA;
	}
	
	/**
	 * @param Categoria[][] $cat formato: array[individuale][idcategoria] 
	 */
	private function stampaCategorie($cat) {
		foreach ($cat as $id => $c) {
			/* @var $c Categoria */
			$cod = $c->getCodice();
			$nome = $this->escape($c->getNome());
			echo ":C=$id,$cod,$nome".self::FINE_RIGA;
		}
	}
	
	/**
	 * @param Societa $soc
	 */
	private function stampaSocieta($soc) {
		$ids = $soc->getChiave();
		$nsoc = self::escape($soc->getNome());
		$nbreve = self::escape($soc->getNomeBreve());
		echo ":P=$ids,$nsoc,$nbreve".self::FINE_RIGA;
	}
	
	/**
	 * @param Societa $soc
	 * @param Coach $coach
	 */
	private function stampaCoach($soc, $coach) {
		foreach ($soc->getCoach($coach) as $p) {
			/* @var $p Persona */
			$ids = $p->getSocieta();
			$idp = $p->getChiave();
			$cogn = $p->getCognome();
			$nome = $p->getNome();
			$sesso = $p->getSesso();
			$nascita = $p->getDataNascita()->format("Y-m-d");
			echo ":H=$idp,$ids,$cogn,$nome,$sesso,$nascita".self::FINE_RIGA;
		}
	}
	
	/**
	 * @param Atleta $a
	 */
	private function stampaAtleta($a, $ids = NULL) {
		//TODO differenziare id esterni e affiliati
		$ida = $a->getChiave();
		if (is_null($ids))
			$ids = $a->getSocieta();
		$cognome = self::escape($a->getCognome());
		$nome = self::escape($a->getNome());
		$sesso = $a->getSesso();
		$nascita = $a->getDataNascita()->format("Y-m-d");
		echo ":A=$ida,$ids,$cognome,$nome,$sesso,$nascita".self::FINE_RIGA;
	}
	
	private function stampaIscrittoIndividuale($ii) {
		$ida = $ii->getAtleta();
		$cintura = $ii->getCintura();
		$tipogara = $ii->getTipoGara();
		$stile = $ii->getStile();
		$peso = $ii->getPeso();
		$cat = $ii->getCategoria();
		$pool = $ii->getPool();
		if ($ii->isAccorpato())
			$acc = $ii->getAccorpamento();
		else
			$acc = "";
		echo ":I=$ida,$cintura,$tipogara,$stile,$peso,$cat,$pool,$acc".self::FINE_RIGA;
	}
	
	/**
	 * @param Squadra $sq
	 */
	private function stampaSquadra($sq) {
		$id = $sq->getChiave();
		$ids = $sq->getSocieta();
		$num = $sq->getNumero();
		/* @var $cat Categoria */
		$cat = $this->cat[$sq->getCategoria()];
		$tipo = $cat->getTipo();
		$catid = $cat->getChiave();
		$pool = $sq->getPool();
		if ($sq->isAccorpato())
			$acc = $sq->getAccorpamento();
		else
			$acc = "";
		echo ":T=$id,$ids,$num,$tipo,$catid,$pool,$acc".self::FINE_RIGA;
		//componenti
		foreach ($sq->getComponenti() as $ida) {
			$idc = $sq->getCinturaComponente($ida);
			echo ":M=$id,$ida,$idc".self::FINE_RIGA;
		}
	}
}