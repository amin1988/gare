<?php

if (!defined("_BASEDIR_")) exit();

include_model("UtSocieta", "Gara", "IscrittoIndividuale", "Cintura", "Stile","CoachEsterno");

include_class("Sesso");

include_controller("VerificaPaginaIndividuale");



class Riepilogo {

	/** @var UtSocieta */

	private $ut;

	/** @var Gara */

	private $gara;

	

	private $unita;

	

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

	 * @var IscrittoIndividuale[][] formato: idatleta => IscrittoIndividuale[]

	 */

	private $iscritti;

	/**

	 * @var Atleta[]

	 */

	private $atleti;

	/**

	 * @var int

	 */

	private $numisc;

	

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

		

		if ($this->gara->usaPeso()) {

			$this->unita = " Kg";

		} else {

			$this->unita = " cm";

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

			$this->coach = CoachEsterno::lista($this->gara->getChiave(),$soc->getChiave());

			$this->arb = ArbitroEsterno::lista($this->gara->getChiave(),$soc->getChiave(),1);//getConvocatiGara($this->gara->getChiave(),$soc->getChiave(),1);//array();

		}

		

		//lettura iscritti

		$iscr = IscrittoIndividuale::listaGara($this->gara->getChiave(),

				$soc->getChiave());

		if (count($iscr) == 0) {

			redirect("soc/iscrivi.php?id=$_GET[id]");

			exit();

		}

		$this->numisc = count($iscr);

		$this->iscritti = array();

		foreach ($iscr as $value) {

			$this->iscritti[$value->getAtleta()][] = $value;

		}

		

		$this->atleti = $this->ut->getSocieta()->getAtleti(array_keys($this->iscritti));

		Menu::setVerificaOpzionale(new VerificaPaginaIndividuale($this->gara));

	}

	

	/**

	 * @return Gara

	 */

	public function getGara() {

		return $this->gara;

	}

	

	public function usaPeso() {

		return $this->gara->usaPeso();

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

	 * @return Atleta[]

	 */

	public function getAtleti() {

		return $this->atleti;

	}

	

	public function getIscrizioni($idatleta) {

		return $this->iscritti[$idatleta];

	}

	

	/**

	 * @param Atleta $a

	 * @return string

	 */

	public function getNomeSesso($a) {

		return Sesso::toStringBreve($a->getSesso());

	}

	

	/**

	 * @param IscrittoIndividuale $i

	 * @return string

	 */

	public function getNomeTipoGara($i) {

		//TODO generalizzare

		switch ($i->getTipoGara()) {

			case 0:

				return "Kata";

			case 1:

				return "Shobu Sanbon";

			case 2:

				return "Shobu Ippon";
                         case 3: 
                            
                            return "Kata Rengokai";
                            
                        case 4: 
                            
                            return "Shobu Kumite";

		}

	}

	

	

	/**

	 * @param IscrittoIndividuale $i

	 * @return string

	 */

	public function getNomeCintura($i) {

		return Cintura::getCintura($i->getCintura())->getNome();

	}

	

	/**

	 * @param IscrittoIndividuale $i

	 * @return string

	 */

	public function getStile($i) {

		if (is_null($i->getStile()))

			return "";

		else

			return Stile::getStile($i->getStile())->getNome();

	}

	

	/**

	 * @param IscrittoIndividuale $i

	 * @return string

	 */

	public function getPeso($i) {

		if (is_null($i->getPeso()))

			return "";

		else

			return $i->getPeso() . $this->unita;

	}

	

	public function getNomeCategoria($idcat) {

		$c = $this->gara->getCategorieIndiv();

		return $c[$idcat]->getNome();

	}

	

	public function haSquadre() {

		if (!$this->gara->isSquadre()) 

			return false;

		return $this->ut->getSocieta()->haSquadre($this->gara->getChiave());

	}

	

	public function getNumIscritti() {

		return $this->numisc;

	}

	

	public function getNumAtleti() {

		return count($this->atleti);

	}

	

	public function getPagamentoCoach() {

		return $this->gara->getPagamentoCoach();

	}

	

	public function getNumCoach() {

		return count($this->coach);

	}

	

	public function getNumArb() {

		return count($this->arb);

	}

	

	public function getNumTur() {

		$tot_tur = 0;

		$turni = ArbitroAffiliato::getTurni($this->gara->getChiave());

		foreach($this->arb as $ida=>$a)

		{

			$tot_tur += $turni[$ida];

		}

		

		return $tot_tur;

	}

	

	public function getPrezzoIndividuale()

	{

		return $this->gara->getPrezzoIndividuale();

	}

	

	public function getRimborsoArb()

	{

		return $this->gara->getRimborsoArb();

	}

	

	public function getPrezzoTotale() {

		$ind = $this->getNumIscritti() * $this->gara->getPrezzoIndividuale();

		$ind -= $this->getNumTur() * $this->gara->getRimborsoArb();

		if ($this->getPagamentoCoach())

			return $ind + $this->getNumCoach() * $this->gara->getPrezzoCoach(); 

		else

			return $ind;

	}

	

	public function getIstruzioniPagamento() {

		$soc = "Soc: ".$this->ut->getSocieta()->getNomeBreve();

		$gara = "Gara: ".$this->getGara()->getNome();

		$id_g = $this->getGara()->getChiave();

		

		if(_WKC_MODE_)

		{

			$lang = Lingua::getParole();

			return Lingua::getParola("paga_cash");

		}

		

		if($id_g == 99 || $id_g == 100)

			{

			//$str = "Le quote d'iscrizione della gara vanno pagate esclusivamente in loco.";

 			$str = "<strong>Unione Italiana Karate</strong><br>IBAN <strong>IT 37 O 05584 03241 000000002111</strong><br>Causale \"<em>$gara</em>\"";

			}

		else 

			/*$str = "<strong>BANCA POPOLARE DI Roma</strong><br>C/C <strong>0000000920</strong> intestato a FIAM  (Federazione Italiana Arti Marziali a.s.d.)<br>

				IBAN <strong>IT 22 W 05584 03241 000000000920</strong><br>Causale \"<em>$soc - $gara</em>\"";
*/
		 $str = "PER LE MODALITA' DI VERSAMENTO DELLE QUOTE DELLE GARE ATTENERSI ALLE ISTRUZIONI CONTENUTE NELLE COMUNICAZIONI ALLEGATE";

		return $str;

	}

	

	public function getUtenteSocieta() {

		return $this->ut;

	}

	

}