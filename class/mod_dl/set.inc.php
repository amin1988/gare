<?php
if (!defined("_BASEDIR_")) exit();
include_model("Gara");

//controlli
if (!isset($_GET["id"])) {
	exit(Lingua::getParola("errore"));
}

$gara = new Gara($_GET["id"]);
if (!$gara->esiste()) {
	exit(Lingua::getParola("errore"));
}


if (isset($_GET["fotocoach"])) {
	downloadZip($gara, isset($_GET["db"]));
} else {
	$db = new SetDb($gara);
	$db->download();
}
exit();

/**
 * Genera il file zip con le foto dei coach
 * @param Gara $gara 
 * @param bool $addDb true per includere anche il file del database
 */
function downloadZip($gara, $addDb) {
	$zip = new ZipArchive();
	
	$filename = tempnam('/tmp', 'dlisc');
	
	if ($zip->open($filename, ZIPARCHIVE::OVERWRITE)!==TRUE) {
		exit("cannot open <$filename>\n");
	}
	
	if ($addDb) {
		$db = new SetDb($gara);
		$zip->addFromString($db->getFile(), $db->getText());
		$coach = $db->getCoach();
	} else {
		$coach = $gara->getCoach();
	}
	foreach ($coach as $cl) {
		foreach ($cl as $c) {
			/* @var $c Coach */
			$foto = $c->getFoto();
			$idc = $c->getChiave();
			$zip->addFile(_BASEDIR_.$foto,"foto/coach_pics/$idc.jpg");
		}
	}
 	$zip->close();
	
	$idg = $gara->getChiave();
	
	header("Content-Disposition: attachment; filename=gara$idg.zip");
	header("Content-Type: application/zip");
	header("Content-length: " . filesize($filename) . "\n\n");
	header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	
	readfile($filename);
}

class SetDb {
	const EOL = ";[EOL]\n";
	const DATA = "Y.m.d";
	
	private $cont = NULL;
	/**
	 * DA USARE ATTRAVERSO getIdAtleta()
	 * prossimo id atleta da utilizzare
	 * @var int
	 */
	private $nextIda = 1;
	/**
	 * @var Gara
	 */
	private $gara;
	private $indiv;
	private $squadre;
	private $comp;
	private $comp_bis;
	private $societa = array();
	/**
	 * @var int[][]
	 */
	private $atleti = array();
	private $atlobj = array();
	/**
	 * @var int[][]
	 */
	private $pesi;
	/**
	 * numero partecipati in categoria
	 * @var int[] formato: id categoria => int
	 */
	private $categ = array();
	/**
	 * DA USARE ATTRAVERSO getCoach()
	 */
	private $coach = NULL;
	
	private $official = NULL;
	private $referee = NULL;
	
	/**
	 * @param Gara $gara
	 */
	public function __construct($gara) {
		include_model("Societa","Official","Nazione");
		include_class("Sesso");
		
		$this->gara = $gara;
		
		$this->preelabora();
		
		//carica la struttura del db
		$this->cont = file_get_contents(_MOD_DIR_."set.db.php")."\n\n\n";
		
		//genera l'evento
		$this->cont .= $this->evento();
		$this->cont .= $this->categorie();
		$this->cont .= $this->societa();
		$this->cont .= $this->coach();
		$this->cont .= $this->official();
		$this->cont .= $this->referee();
		$this->cont .= $this->atleti();
		$this->cont .= $this->indiv();
		$this->cont .= $this->squadre();
		$this->cont .= $this->nazioni();
	}
	
	/**
	 * @return Coach[][] formato: id societa => id persona => Coach
	 */
	public function getCoach() {
		if ($this->coach === NULL) {
			$this->coach = array();
			$tmp = Coach::lista($this->gara->getChiave());
			foreach ($tmp as $ids => $ts) {
				foreach ($ts as $c) {
					/* @var $c Coach */
					$this->coach[$ids][$c->getPersona()] = $c;
				}
			}
		} 
		return $this->coach;
	}
	
	public function getArbitri() {
		if($this->referee === NULL)
		{
			if(_WKC_MODE_)
			{
				$soc_wkc = Societa::elencoWKC();
				foreach($soc_wkc as $ids=>$s)
				{
					$tmp = Arbitro::elencoSoc($ids,$this->gara->getChiave());
					if($tmp !== NULL)
						foreach ($tmp as $ida=>$ar)
						{
							/* @var $ar Arbitro */
							$this->referee[$ids][$ar->getPersona()] = $ar;
						}
				}
				
				return $this->referee;
			}
		
			else
			{
				$this->referee = array();
				$tmp = Arbitro::lista($this->gara->getChiave(), NULL, 1);
				foreach ($tmp as $ids=>$ar)
					foreach ($ar as $a) {
						/* @var $a Arbitro */
						$this->referee[$ids][$a->getPersona()] = $a;
					}
			}
		}
		return $this->referee;
	}
	
	public function getOfficial() {
		if($this->official === NULL)
		{
			$this->official = array();
			foreach($this->societa as $ids=>$soc)
			{
				$tmp = Official::officialSocieta($ids);
				foreach($tmp as $ido=>$off)
					$this->official[$ido] = $off;
			}
		}
		return $this->official;
	}
	
	public function getFile() {
		return "iscrizioni.".$this->gara->getChiave().".sql";
	}
	
	public function getText() {
		return $this->cont;
	}
	
	public function download() {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream; charset="utf-8');
		header('Content-Disposition: attachment; filename='.$this->getFile());
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		
		echo $this->getText();
	}
	
	private function getIdAtleta($ida) {
		//verificare se atleta gi� inserito, se si recupera idatleta per non sovrascrivere e perdere dati
		foreach($this->atleti as $ids=>$aratl)
		{
			if(array_key_exists($ida,$aratl))
			{
				$idass = $aratl[$ida];
				return $idass;
			}
		}
		$id = $this->nextIda;
		$this->nextIda++;
		return $id;
	}
	
	private function preelabora() {
		$g = $this->gara;
		$idg = $g->getChiave();
		
		$atl = array();
		//iscritti individuali
		if ($g->isIndividuale()) {
			include_model("IscrittoIndividuale");
			$this->indiv = IscrittoIndividuale::listaGara($idg);
			$this->analizzaIndiv($this->indiv);
		} else $this->indiv = array();
		//iscritti squadre
		if ($g->isSquadre()) {
			include_model("Squadra", "Prestito");
			$this->squadre = Squadra::listaGara($idg);
			$this->analizzaSquadre($this->squadre);
		} else $this->squadre = array();
		
	}

	/**
	 * @param Iscritto $isc
	 */
	private function analizzaIscritto($i) {
		$ids = $i->getSocieta();
		if (!isset($this->societa[$ids]))
			$this->societa[$ids] = new Societa($ids);
		$idc = $i->getCategoriaFinale();
		if (isset($this->categ[$idc]))
			$this->categ[$idc]++;
		else
			$this->categ[$idc] = 1;
	}

	/**
	 * @param IscrittoIndividuale[] $isc
	 */
	private function analizzaIndiv($isc) {
		foreach ($isc as $ii) {
			/* @var $ii IscrittoIndividuale */
			$this->analizzaIscritto($ii);
			$ida = $ii->getAtleta();
			$ids = $ii->getSocieta();
			$this->atleti[$ids][$ida] = $this->getIdAtleta($ida);
			$peso = $ii->getPeso();
			if ($ii != NULL)
				$this->pesi[$ids][$ida] = $peso;
		}
	}

	/**
	 * @param Squadra[] $isc
	 */
	private function analizzaSquadre($isc) {
		foreach ($isc as $i) {
			/* @var $i Squadra */
			$this->analizzaIscritto($i);
			$ids = $i->getSocieta();
			$idsq = $i->getChiave();
			foreach ($i->getComponenti() as $ida) {
				$idx = $this->getIdAtleta($ida);
				$this->atleti[$ids][$ida] = $idx;
				$this->comp[$idsq][$idx] = $idx;
				$this->comp_bis[$idsq][$ida] = $idx;
			}
			$pres = Prestito::squadra($i->getChiave());
			if ($pres !== NULL) {
				$idp = $pres->getAtleta();
				$idsp = $pres->getOrigine();
				if (isset($this->atleti[$ids][$idp])) {
					$this->atleti[$idsp][$idp] = $this->atleti[$ids][$idp];
					unset($this->atleti[$ids][$idp]);
				} else {
					$idx = $this->getIdAtleta($idp);
					$this->atleti[$idsp][$idp] = $idx;
					$this->comp[$idsq][$idx] = $idx;
				}
				if (!isset($this->societa[$idsp]))
					$this->societa[$idsp] = new Societa($idsp);
			}
		}
	}

	private function esc($s) {
		return str_replace(array("\\", "'"), array("\\\\","`"), $s);
	}
	
	private function getSessoSet($s) {
		switch($s) {
			case Sesso::M:
				return 'm';
			case Sesso::F:
				return 'f';
			default:
				return '';
		}
	}
	
	/**
	 * @param Gara $gara
	 */
	private function evento() {
		$eol = self::EOL;
		$gara = $this->gara;
		$t = "TRUNCATE TABLE veranstaltung$eol";
		//TODO eventi separati
		$idev = 1;
		$nome = $this->esc($gara->getNome());
		$inizio = $gara->getDataGara()->format(self::DATA);
		if ($gara->getDataFineGara() === NULL)
			$fine = $inizio;
		else
			$fine = $gara->getDataFineGara()->format(self::DATA);
		
		$t.="INSERT INTO veranstaltung (vernr,bezeichnung, verdatum,nennstart,nennende,"
			."user,gesperrt,regmode,land,waehrung,typ,bisdatum,"
			."useothercutoffday,othercutoffday,usebothcutoffdates) VALUES\n";
		$t.="('$idev','$nome','$inizio','$inizio','$inizio',"
			."'1','0','0','106','45','1','$fine',"
			."'0','$inizio','0')$eol\n";
		if ($gara->getPagamentoCoach()) {
			$prezzo = $gara->getPrezzoCoach();
			$t.="INSERT INTO entryfeemodel(verid,coachfee) VALUES ('$idev','$prezzo')$eol\n";
		}
		return $t;
	}
	
	private function categorie() {
		$eol = self::EOL;
		$tc = "TRUNCATE TABLE kategorie$eol";
		$te = "TRUNCATE TABLE veranstaltungkat$eol";
		$cat[0] = $this->gara->getCategorieIndiv();
		$cat[1] = $this->gara->getCategorieSquadre();
		if (!isset($_GET["kata"])) $_GET["kata"]=0;
		if (!isset($_GET["sanbon"])) $_GET["sanbon"]=0;
		if (!isset($_GET["ippon"])) $_GET["ippon"]=0;
		
		$tc.="INSERT INTO kategorie (knr,katbez,alternichtmehr,alterVon,sportart,geschlecht,team,typ) VALUES\n";
		$te.="INSERT INTO veranstaltungkat (vernr,knr,alterVon,alternichtmehr,startgeld,roundrobin) VALUES\n";
		$primoc = true;
		$primoe = true;
		foreach ($cat as $tipo=>$list) {
			if ($tipo==0) {
				$team = '';
				$prezzo = $this->gara->getPrezzoIndividuale();
			}
			else {
				$team='t';
				$prezzo = $this->gara->getPrezzoSquadra();
			}
			
			foreach($list as $cat) {
				/* @var $cat Categoria */
				$idc = $cat->getChiave();
				if (!isset($this->categ[$idc])) continue;
				$elem = $this->categ[$idc];
				$nome = $this->esc($cat->getNome());
				$emin = $cat->getEtaMin();
				$emax = $cat->getEtaMax()+1; //MODIFICA 11/03/2014 deve fornire l'eta non pi� valida e non l'eta massima
				$sesso = $this->getSessoSet($cat->getSesso());
				if ($elem == 3) {
					$tab = 1; //round robin
				} else { 
					$tab = 0;
					switch ($cat->getTipo()) {
						case 0:
							$tabn = "kata";
							break;
						case 1:
							$tabn = "sanbon";
							break;
						case 2:
							$tabn = "ippon";
							break;
						default:
							$tabn = NULL;
					}
					if ($tabn != NULL && isset($_GET[$tabn]))
						$tab = intval($_GET[$tabn]);
				}
				
				if ($primoc) $primoc=false;
				else $tc.=",\n";
				$tc.="('$idc','$nome','$emax','$emin','1','$sesso','$team','1')";
				//TODO eventi separati
				if ($primoe) $primoe=false;
				else $te.=",\n";
				$te.="('1','$idc','$emin','$emax','$prezzo','$tab')";
			}
		}
		return "$tc$eol\n$te$eol\n";
	}
	
	/**
	 * @param Gara $gara
	 */
	private function societa() {
		$eol = self::EOL;
		$t = "TRUNCATE TABLE verein$eol";
		$t.="INSERT INTO verein (vereinnr, bezeichnung, nation, createdbymanager,lvnr) VALUES \n";
		$primo = true;
		foreach ($this->societa as $ids => $soc) {
			/* @var $soc Societa */
			$nome = $this->esc($soc->getNomeBreve());
			$nat = $soc->getNazione();//TODO DA APPROVARE
// 			$nat = 106;
			if ($primo) $primo=false;
			else $t.=",\n";
			$t.="('$ids', '$nome($ids)', '$nat', '1','0')";
		}
		return "$t$eol\n";
	}
	
	private function coach() {
		$coach = $this->getCoach();
		if (count($coach) == 0) return;
		$eol = self::EOL;
		$tc = "TRUNCATE TABLE coach$eol";
		$tn = "TRUNCATE TABLE nennungencoach$eol";

		$tc.="INSERT INTO coach  (id, titel, vorname, nachname, geburt, vereinnr, "
			."sonstiges, geschlecht,kyu,dan,wkfid,passportid) VALUES\n";
		$tn.="INSERT INTO  nennungencoach (id, vernr) VALUES \n";
		$primo = true;
		foreach ($coach as $ids => $cs) {
			if (isset($this->societa[$ids])) {
				$soc = $this->societa[$ids];
				$plist = $soc->getCoach($cs);
				foreach ($plist as $p) {
					/* @var $p Persona */
					/* @var $c Coach */
					$c = $cs[$p->getChiave()];
					$idc = $c->getChiave();
					$nome = $this->esc($p->getNome());
					$cogn = $this->esc($p->getCognome());
					$nascita = $p->getDataNascita()->format(self::DATA);
					$sesso = $this->getSessoSet($p->getSesso());
					
					if ($primo) $primo=false;
					else {
						$tc.=",\n";
						$tn.=",\n";
					}
					$tc.="('$idc','','$nome','$cogn','$nascita','$ids',"
						."'','$sesso','0','0','','')";
					$tn.="('$idc', '1')"; //TODO separa eventi (1 = id evento)
				}
			}
		}
		
		return "$tc$eol\n$tn$eol\n";
	}
	
	private function official() {
		$official = $this->getOfficial();
		if (count($official) == 0) return;
		$eol = self::EOL;
		$tc = "TRUNCATE TABLE official$eol";
		$tn = "TRUNCATE TABLE nennungenofficial$eol";
	
		$tc.="INSERT INTO official (id, titel, vorname, nachname, geburt, sichtbar, geschlecht, "
				."vereinnr, roleid, sonstiges, wkfid, passportid) VALUES\n";
		$tn.="INSERT INTO nennungenofficial (id, vernr) VALUES \n";
		$primo = true;
		foreach ($official as $ido => $off) {
					/* @var $off Official */
					$ids = $off->getIDSocieta();
					$nome = $this->esc($off->getNome());
					$cogn = $this->esc($off->getCognome());
					$nascita = $off->getDataNascita()->format(self::DATA);
					$sesso = $this->getSessoSet($off->getSesso());
						
					if ($primo) $primo=false;
					else {
						$tc.=",\n";
						$tn.=",\n";
					}
					$tc.="('$ido','','$nome','$cogn','$nascita',1,'$sesso','$ids',"
					."1,'','','')";
					$tn.="('$ido', '1')"; //TODO separa eventi (1 = id evento)
			}
	
			return "$tc$eol\n$tn$eol\n";
	}
	
	private function referee() {
		$referee = $this->getArbitri();
		if(count($referee) == 0) return;
		$eol = self::EOL;
		
		$tc = "TRUNCATE TABLE referee$eol";
		$tn = "TRUNCATE TABLE nennungenreferee$eol";
		
		$tc.="INSERT INTO referee (id, titel, vorname, nachname, geburt, sichtbar, kyu, dan, lizenznat, geschlecht, "
				."vereinnr, nationnr, lizenzint, lizenznr, wkfid, passportid) VALUES\n";
		$tn.="INSERT INTO nennungenreferee (id, vernr) VALUES \n";
		$primo = true;
		foreach ($referee as $ids => $ref) {
			if (isset($this->societa[$ids])) {
				$soc = $this->societa[$ids];
				$plist = $soc->getArbitri($ref);
				foreach ($plist as $p) {
					/* @var $p Persona */
					/* @var $c Arbitro */
					$c = $ref[$p->getChiave()];
					$idc = $p->getChiave();
					$nome = $this->esc($p->getNome());
					$cogn = $this->esc($p->getCognome());
					$nascita = $p->getDataNascita()->format(self::DATA);
					$sesso = $this->getSessoSet($p->getSesso());
					$nat = $soc->getNazione();
											
					if ($primo) $primo=false;
					else {
						$tc.=",\n";
						$tn.=",\n";
					}
					$tc.="('$idc','','$nome','$cogn','$nascita','1','0','0','$nat','$sesso',"
					."'$ids','$nat','$idc','$idc','','')";
					$tn.="('$idc', '1')"; //TODO separa eventi (1 = id evento)
				}
				}
			}
		
			return "$tc$eol\n$tn$eol\n";
	}
	
	private function atleti() {
		$eol=self::EOL;
		$t="TRUNCATE TABLE `names`$eol";
		$t.="INSERT INTO names  (nnr, name, geburt, vereinnr,gewicht,groesse,"
				."geschlecht,kyu,dan,sonstiges,wkfid,passportid,extid) VALUES\n";
		$primo = true;
		foreach ($this->atleti as $ids => $al) {
			/* @var $soc Societa */
			$soc = $this->societa[$ids];
			foreach ($soc->getAtleti(array_keys($al)) as $a) {
				/* @var $a Atleta */
				$ida = $a->getChiave();
				$idaset = $this->atleti[$ids][$ida];
				$this->atlobj[$idaset] = $a;
				$nome = $this->esc(str_replace(" ", "_", $a->getCognome())
						." ".str_replace(" ", "_", $a->getNome()));
				$nascita = $a->getDataNascita()->format(self::DATA);
				$sesso = $this->getSessoSet($a->getSesso());
				if (isset($this->pesi[$ids][$ida]))
					$peso = $this->pesi[$ids][$ida];
				else
					$peso = 0;
				if ($this->gara->usaPeso())
					$alt = 0;
				else {
					$alt = $peso;
					$peso = 0;
				}
				$cint = $a->getCintura();
				if ($cint == Cintura::cinturaNera()) {
					$dan = 1;
					$kyu = 0;
				} else {
					$dan = 0;
					//TODO fare bene cinture
					switch ($cint) {
						case 1:
							$kyu = 6;
							break;
						case 2:
							$kyu = 5;
							break;
						case 3:
							$kyu = 4;
							break;
						case 4:
							$kyu = 3;
							break;
						case 5:
							$kyu = 2;
							break;
						case 6:
							$kyu = 1;
							break;
					}
				}
				
				if ($primo) $primo = false;
				else $t.=",\n";
				$t.="('$idaset','$nome','$nascita','$ids','$peso','$alt',"
					."'$sesso','$kyu','$dan','','','','$ida')";
			}
		}
		
		return "$t$eol\n";
	} 
	
	private function indiv() {
		if (count($this->indiv) == 0) return;
		$eol = self::EOL;
		$ts="TRUNCATE TABLE `stili`$eol";
		$ti="TRUNCATE TABLE nennungeneinzel$eol";
		$ti.="INSERT INTO nennungeneinzel (vernr, katnr, nnr) VALUES \n";
		$ts.="INSERT INTO `stili` (`idpart`, `tipo`, `idstile`) VALUES \n";
		$primoi = true;
		$primos = true;
		foreach ($this->indiv as $i) {
			/* @var $i IscrittoIndividuale */
			$idi = $this->atleti[$i->getSocieta()][$i->getAtleta()];
			$idc = $i->getCategoriaFinale();
			
			if ($primoi) $primoi=false;
			else $ti.=",\n";
			//TODO eventi separati (1 = id evento) 
			$ti.="('1', '$idc', '$idi')";
			
			$stile = $i->getStile();
			if ($stile !== NULL) {
				if ($primos)  $primos=false;
				else $ts.=",\n";
				$ts.="('$idi', '0', '$stile')";
			}
		}
		if ($primos) return $ti."$eol\n";
		else return "$ti$eol\n$ts$eol\n";
	}

	private function squadre() {
		if (count($this->squadre) == 0) return;
		$eol = self::EOL;
		$ti="TRUNCATE TABLE nennungenteam$eol";
		$tc="TRUNCATE TABLE team$eol";
		$ti.="INSERT INTO nennungenteam  (teamid, vernr, knr, vereinnr, mannschaft) VALUES\n";
		$tc.="INSERT INTO team (teamid, nnr) VALUES \n";
		$primoi = true;
		$primoc = true;
		foreach ($this->squadre as $sq) {
			/* @var $sq Squadra */
			$idsq = $sq->getChiave();
			$idcat = $sq->getCategoriaFinale();
			$idsoc = $sq->getSocieta();
			$soc = $this->societa[$idsoc];
			$nome = $this->esc($soc->getNomeBreve()." ".$sq->getNumero()."(");
			$primo = true;
			foreach ($this->comp[$idsq] as $idaset) {
				if ($primo) $primo = false;
				else $nome .= ', ';
				if (isset($this->atlobj[$idaset])) {
					$a = $this->atlobj[$idaset];
					$nome .= $this->esc(str_replace(" ", "_", $a->getCognome()));
				}
				elseif(array_search($idaset,$this->comp_bis[$idsq]) != false)
				{
					$idatl = array_search($idaset,$this->comp_bis[$idsq]);
					$a = $this->atlobj[$this->atleti[$idsoc][$idatl]];
					$nome .= $this->esc(str_replace(" ", "_", $a->getCognome()));
				}
			}
			$nome .= ')';
			
			
			if ($primoi) $primoi=false;
			else $ti.=",\n";
			//TODO separare eventi (1 = id evento)
			$ti.="('$idsq','1','$idcat','$idsoc','$nome')";
// 			foreach ($sq->getComponenti() as $idcm) {
			foreach($this->comp[$idsq] as $idcm) {
				if ($primoc) $primoc=false;
				else $tc.=",\n";
// 				$tc.="('$idsq', '{$this->atleti[$idsoc][$idcm]}')";
				$tc.="('$idsq', '$idcm')";
			}
		}
		return "$ti$eol\n$tc$eol\n";
	}
	
	private function nazioni()
	{
		$eol = self::EOL;
		
		$nat = "TRUNCATE TABLE nation$eol";
		$nat .= "INSERT INTO nation (id, bezeichnung, iso, kurz) VALUES\n";
		
		$primo = true;
		foreach(Nazione::listaNazioni() as $idn=>$naz)
		{
			if($primo)
				$primo = false;
			else
				$nat .= ",\n";
			
			$nome = $naz->getNome();
			$iso = $naz->getIso();
			$kurz = $naz->getKurz();
			
			$nat .="('$idn','$nome','$iso','$kurz')";
		}
		
		return "$nat$eol\n";
	}
}