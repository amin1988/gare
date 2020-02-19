<?php

if (!defined("_BASEDIR_")) exit();

include_model("UtSocieta", "Gara", "Squadra", "Categoria");

include_class("Sesso");

include_controller("VerificaPaginaIndividuale");



class RiepilogoSquadre {

	/** @var UtSocieta */

	private $ut;

	/** @var Gara */

	private $gara;

	

	/**

	 * @var Squadra[]

	 */

	private $squadre;

	

	/**

	 * @var Persona[]

	 */

	private $coach;

	

	/**

	 *

	 * @var Persona[]

	 */

	private $arb;

	

	/**

	 * @var Atleta[] formato idatleta => Atleta

	 */

	private $comp;

	/**

	 * @var Categoria[] formato idcategoria => Categoria

	 */

	private $cat;

	

	public function __construct() {

		$this->ut = UtSocieta::crea();

		if (is_null($this->ut)) nologin();

		

		if (!isset($_GET["id"])) {

			homeutente($this->ut);

			exit();

		}

		$this->gara = new Gara($_GET["id"]);

		if (!$this->gara->esiste() || $this->gara->passata()) {

			homeutente($this->ut);

			exit();

		}

		

		$soc = $this->ut->getSocieta();

		if($soc->isAffiliata())

		{

		$this->coach = $soc->getCoach(Coach::lista($this->gara->getChiave(),

				$soc->getChiave()));

		$this->arb = $soc->getArbitri(Arbitro::lista($this->gara->getChiave(),

				$soc->getChiave(),1));

		}

		else 

		{

			$this->coach = Coach::lista($this->gara->getChiave(),

					$soc->getChiave());

			$this->arb = Arbitro::lista($this->gara->getChiave(),

					$soc->getChiave(),1);

		}

		

		$this->squadre = Squadra::listaSocieta($soc->getChiave(), $this->gara->getChiave());

		if (count($this->squadre) == 0) {

			redirect("soc/iscrivisq.php?id=$_GET[id]");

			exit();

		}

			

		$pubbl = $this->gara->listaPubblicata();

		foreach ($this->squadre as $sq) {

			/* @var $sq Squadra */

			if ($pubbl) {

				$idc = $sq->getAccorpamento();

				if (!is_null($idc)) $cat[$idc] = $idc;

			}

			$idc = $sq->getCategoria();

			$cat[$idc] = $idc;

			foreach ($sq->getComponenti() as $ida) {

				$atl[$ida] = $ida;

			}

		}

		

		$this->comp = $soc->getAtleti($atl);

		$this->cat = Categoria::lista($cat);

		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));

	}

	

	/**

	 * @return Gara

	 */

	public function getGara() {

		return $this->gara;

	}



	/**

	 * @return Squadra[]

	 */

	public function getSquadre() {

		return $this->squadre;

	}

	

	/**

	 * @return Persona[]

	 */

	public function getCoach() {

		return $this->coach;

	}

	

	public function getArbitri() {

		return $this->arb;

	}

	

	/**

	 * @param Squadra $sq

	 * @return Atleta[]

	 */

	public function getComponenti($sq) {

		foreach ($sq->getComponenti() as $ida) {

			$comp[$ida] = $this->comp[$ida];

		}

		return $comp;

	}

	

	/**

	 * @param Squadra $sq

	 * @return string

	 */

	public function getNomeCategoria($sq) {

		if ($this->gara->listaPubblicata())

			return $this->cat[$sq->getCategoriaFinale()]->getNome();

		else

			return $this->cat[$sq->getCategoria()]->getNome();

	}

	

	/**

	 * @param Squadra $sq

	 * @return string

	 */

	public function getNomeCategoriaOriginale($sq) {

		return $this->cat[$sq->getCategoria()]->getNome();

	}

	

	/**

	 * @param Squadra $sq

	 * @param Atleta $a

	 */

	public function getNomeCintura($sq, $a) {

		$idc = $sq->getCinturaComponente($a->getChiave());

		return Cintura::getCintura($idc)->getNome();

	}

	

	/**

	 * @param Squadra $sq

	 */

	public function getNomeSquadra($sq) {

		return Lingua::getParola("squadra").' '.$sq->getNumero();

	}

	

	public function haIndividuali() {

		if (!$this->gara->isIndividuale())

			return false;

		return $this->ut->getSocieta()->haIndividuali($this->gara->getChiave());

	}

	

	public function getNumIscritti() {

		return $this->numisc;

	}

	

	public function getNumSquadre() {

		return count($this->squadre);

	}

	

	public function getNumArb() {

		return count($this->arb);

	}

	

	public function getPrezzoSquadra() {

		return $this->gara->getPrezzoSquadra();

	}

	

	public function getRimborsoArb()

	{

		if ($this->gara->isSquadre() && $this->gara->isIndividuale())

			return 0;

		else

			return $this->gara->getRimborsoArb();

	}

	

	public function getPrezzoTotale() {

		$ind = $this->getNumSquadre() * $this->getPrezzoSquadra();

		$ind -= $this->getNumArb() * $this->getRimborsoArb();

		return $ind;

	}

	

	public function getIstruzioniPagamento() {

		

		if(_WKC_MODE_)

		{

			$lang = Lingua::getParole();

			return Lingua::getParola("paga_cash");

		}

		

		$soc = "Soc: ".$this->ut->getSocieta()->getNomeBreve();

		$gara = "Gara: ".$this->getGara()->getNome();

		$str = " intestato a FIAM  (Federazione Italiana Arti Marziali a.s.d.)<br>

		IBAN <strong>IT19 R 05034 03300 000000005372</strong><br>Causale \"<em>$soc - $gara</em>\"";

		$str = "PER LE MODALITA' DI VERSAMENTO DELLE QUOTE DELLE GARE ATTENERSI ALLE ISTRUZIONI CONTENUTE NELLE COMUNICAZIONI ALLEGATE";

		return $str;

	}

	

	public function getUtenteSocieta() {

		return $this->ut;

	}

}