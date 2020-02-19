<?php

if (!defined("_BASEDIR_"))
        exit();
include_class("Sesso");
include_model("Modello", "Cintura", "Stile", "GruppoCat");

//TODO pulire commenti
class Categoria extends Modello {

        const MAX_PESO = 500;
        const MAX_ETA = 500;

        /**
         * @var int[]
         */
        private $cinture = NULL;

        /**
         * @var int[]
         */
        private $stili = NULL;

        /**
         * @var string
         */
        private $nome = NULL;

        /**
         * 
         * @param string $where
         * @return Categoria[]
         */
        public static function elenco($where = NULL) {
                /* @var $conn Connessione */
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                if ($where === NULL)
	     $where = "1";
                $mr = $conn->select("categorie", $where);
                $cat = array();
                while ($row = $mr->fetch_assoc()) {
	     $c = new Categoria();
	     $c->carica($row);
	     $cat[$c->getChiave()] = $c;
                }
                return $cat;
        }

        /**
         * Restituisce le categorie in un gruppo.
         * @param int $idgruppo
         * @return Categoria[]
         */
        public static function listaGruppo($idgruppo) {
                /* @var $conn Connessione */
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                /* @var $mr mysqli_result */
                $mr = $conn->select("categorie", "idgruppo = '$idgruppo'");
                $cat = array();
                while ($row = $mr->fetch_assoc()) {
	     $c = new Categoria();
	     $c->carica($row);
	     $cat[$c->getChiave()] = $c;
                }
                return $cat;
        }

        /**
         * @param int[] $lista
         * @return Categoria[] formato idcategoria => Categoria
         */
        public static function lista($lista) {
                if (is_null($lista) || count($lista) == 0)
	     return array();

                /* @var $conn Connessione */
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                /* @var $mr mysqli_result */
                $mr = $conn->select("categorie", "idcategoria IN " . $conn->flatArray($lista));
                $cat = array();
                while ($row = $mr->fetch_assoc()) {
	     $c = new Categoria();
	     $c->carica($row);
	     $cat[$c->getChiave()] = $c;
                }
                return $cat;
        }

        /**
         * Restituisce le categorie relative ad una gara.
         * @param int $idgara
         * @param boolean $separa true per separare le categorie individuali 
         * da quelle a squadre
         * @return Categoria[][] formato: array[individuale][idcategoria] se separa = true
         * altrimenti formato: array[idcategoria]
         */
        public static function listaGara($idgara, $separa = true, $lista = NULL) {
                /* @var $conn Connessione */
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $where = "idgara = '$idgara'";
                if (!is_null($lista) && count($lista) > 0)
	     $where .= " AND cg.idcategoria IN " . $conn->flatArray($lista);
                /* @var $mr mysqli_result */
                $mr = $conn->query("SELECT cg.individuale AS ind, numero, c.* "
	     . "FROM categoriegara cg INNER JOIN categorie c ON cg.idcategoria = c.idcategoria "
	     . "WHERE $where");
                if ($separa) {
	     $cat[0] = array();
	     $cat[1] = array();
                }
                while ($row = $mr->fetch_assoc()) {
	     $ind = $row["ind"];
	     unset($row["ind"]);
	     $c = new Categoria();
	     $c->carica($row);
	     if ($separa)
	             $cat[intval($ind)][$c->getChiave()] = $c;
	     else
	             $cat[$c->getChiave()] = $c;
                }
                return $cat;
        }

        /**
         * Restituisce i gruppi di categoria che fanno parte della gara
         * indice=>gruppocat
         * @param int $idgara
         * @return int[]
         */
        public static function listaGruppiGara($idgara) {
                /* @var $conn Connessione */
                $conn = $GLOBALS["connint"];
                $conn->connetti();

                $mr = $conn->query("SELECT DISTINCT(c.idgruppo) FROM categorie c INNER JOIN categoriegara cg 
				ON ( c.idcategoria = cg.idcategoria )
				WHERE idgara ='$idgara'");

                $gruppi = array();
                while ($row = $mr->fetch_assoc())
	     $gruppi[] = $row["idgruppo"];

                return $gruppi;
        }

        /**
         * Crea una categoria dal suo codice
         * @param string $codice
         * @return Categoria
         */
        public static function parse($codice) {
                if ($codice[0] != 'W')
	     return NULL;
                if (strlen($codice) != 15)
	     return NULL;
                $c = new Categoria();

                //sesso
                switch ($codice[1]) {
	     case "M":
	             $c->setSesso(Sesso::M);
	             break;
	     case "F":
	             $c->setSesso(Sesso::F);
	             break;
	     case "E":
	             $c->setSesso(Sesso::MISTO);
	             break;
	     default:
	             return NULL;
                }

                //tipo
                switch ($codice[2]) {
	     case "K":
	             $c->setTipo(0);
	             break;
	     case "S":
	             $c->setTipo(1);
	             break;
	     case "I":
	             $c->setTipo(2);
	             break;
	     default:
	             return NULL;
                }

                //grado
                $grado = substr($codice, 3, 2);
                $grado = hexdec($grado);
                if ($grado <= 0 || $grado >= 128)
	     return NULL;
                $g = array();
                if (($grado & 1) != 0)
	     $g[] = 1;
                if (($grado & 2) != 0)
	     $g[] = 2;
                if (($grado & 4) != 0)
	     $g[] = 3;
                if (($grado & 8) != 0)
	     $g[] = 4;
                if (($grado & 16) != 0)
	     $g[] = 5;
                if (($grado & 32) != 0)
	     $g[] = 6;
                if (($grado & 64) != 0)
	     $g[] = 7;
                $c->setCinture($g);

                //eta
                $p = substr($codice, 5, 2);
                if (!is_numeric($p))
	     return NULL;
                $etamin = intval($p);
                $p = substr($codice, 7, 2);
                if ($p == "99") {
	     $etamax = self::MAX_ETA;
                } else {
	     if (!is_numeric($p))
	             return NULL;
	     $etamax = intval($p) - 1;
                }

                if ($etamin < 0 || $etamax < 0 || $etamin >= $etamax)
	     return NULL;
                $c->setEtaMin($etamin);
                $c->setEtaMax($etamax);

                //stile
                if ($c->getTipo() == 0) {
	     $stile = hexdec($codice[9]);
	     if ($stile <= 0 || $stile >= 16)
	             return NULL;
	     $s = array(); //TODO prendere stili dal db?
	     if (($stile & 1) != 0)
	             $s[] = 1;
	     if (($stile & 2) != 0)
	             $s[] = 2;
	     if (($stile & 4) != 0)
	             $s[] = 3;
	     if (($stile & 8) != 0)
	             $s[] = 4;
	     $c->setStili($s);
                }

                //peso
                if ($c->getTipo() == 1 || $c->getTipo() == 2) {
	     $pesomin = hexdec(substr($codice, 10, 2));
	     $pesomax = hexdec(substr($codice, 12, 2)) - 1;
	     if ($pesomax >= 254)
	             $pesomax = self::MAX_PESO;
	     if ($pesomin < 0 || $pesomax < 0 || $pesomin >= $pesomax)
	             return NULL;
	     $c->setPesoMin($pesomin);
	     $c->setPesoMax($pesomax);
                }

                //speciale
                $c->setHandicap($codice[strlen($codice) - 1] == 's');

                return $c;
        }

        /**
         * @param Categoria $ca
         * @param Categoria $cb
         */
        public static function compare($ca, $cb) {
                $na = $ca->getNumero();
                $nb = $cb->getNumero();
                if ($na !== NULL) {
	     if ($nb !== NULL) {
	             //hanno numero, confrontali
	             if ($na > $nb)
		  return 1;
	             if ($na < $nb)
		  return -1;
	     } else {
	             //solo ca ha numero, ca va sopra
	             return -1;
	     }
                } else if ($nb !== NULL) {
	     //solo cb ha numero, ca va sotto
	     return 1;
                }
                //speciali
                $hpa = $ca->isHandicap();
                $hpb = $cb->isHandicap();
                if ($hpb && !$hpa)
	     return 1;
                if ($hpa && !$hpb)
	     return -1;
                //preagonisti-agonisti
                $aga = $ca->isAgonista();
                $agb = $cb->isAgonista();
                if ($agb && !$aga)
	     return -1;
                if ($aga && !$agb)
	     return 1;
                //TODO colorate-nere
                //tipo
                $va = $ca->getTipo();
                $vb = $cb->getTipo();
                if ($va != $vb)
	     return $va - $vb;
                //eta
                $va = $ca->getEtaMin();
                $vb = $cb->getEtaMin();
                if ($va != $vb)
	     return $va - $vb;
                $va = $ca->getEtaMax();
                $vb = $cb->getEtaMax();
                if ($va != $vb)
	     return $va - $vb;
                //sesso
                $va = $ca->getSesso();
                $vb = $cb->getSesso();
                if ($va != $vb)
	     return $va - $vb;
                //cinture
                //TODO dipende da id!
                $va = $ca->getCinture();
                $vb = $cb->getCinture();
                for ($i = 1; $i <= 7; $i++) {
	     $ia = in_array($i, $va);
	     $ib = in_array($i, $vb);
	     if ($ia && !$ib)
	             return -1;
	     if ($ib && !$ia)
	             return 1;
                }
                //stile
                if ($ca->getTipo() == 0) {
	     $va = $ca->getStili();
	     $vb = $cb->getStili();
	     $val = array_keys($va) + array_keys($vb);
	     $max = max($val);
	     for ($i = min($val); $i < $max; $i++) {
	             $ia = in_array($i, $va);
	             $ib = in_array($i, $vb);
	             if ($ia && !$ib)
		  return -1;
	             if ($ib && !$ia)
		  return 1;
	     }
                }
                //peso
                if ($ca->getTipo() == 1) {
	     $va = $ca->getPesoMin();
	     $vb = $cb->getPesoMin();
	     if ($va != $vb)
	             return $va - $vb;
	     $va = $ca->getPesoMax();
	     $vb = $cb->getPesoMax();
	     if ($va != $vb)
	             return $va - $vb;
                }

                return 0;
        }

        public function __construct($id = NULL) {
                parent::__construct("categorie", "idcategoria", $id);
        }

        /**
         * @return string
         */
        public function getCodice() {
                //versione
                $codice = "W";
                //sesso
                switch ($this->getSesso()) {
	     case Sesso::M:
	             $codice .= "M";
	             break;
	     case Sesso::F:
	             $codice .= "F";
	             break;
	     case Sesso::MISTO:
	             $codice .= "E";
	             break;
                }
                //tipo
                $tipo = $this->getTipo();
                switch ($tipo) {
	     case 0:
	             $codice .= "K";
	             break;
	     case 1:
	             $codice .= "S";
	             break;
	     case 2:
	             $codice .= "I";
	             break;
                }
                //grado
                $gr = 0;
                foreach ($this->getCinture() as $idc) {
	     switch ($idc) {
	             case 1:
		  $gr += 1;
		  break;
	             case 2:
		  $gr += 2;
		  break;
	             case 3:
		  $gr += 4;
		  break;
	             case 4:
		  $gr += 8;
		  break;
	             case 5:
		  $gr += 16;
		  break;
	             case 6:
		  $gr += 32;
		  break;
	             case 7:
		  $gr += 64;
		  break;
	     }
                }
                $codice .= sprintf("%02X", $gr);
                //eta
                $codice .= sprintf("%02d", $this->getEtaMin());
                $emax = $this->getEtaMax() + 1;
                if ($emax < 99)
	     $codice .= sprintf("%02d", $emax);
                else
	     $codice .= "99";
                //stile
                if ($tipo == 0) {
	     $st = 0;
	     foreach ($this->getStili() as $ids) {
	             switch ($ids) {
		  case 1:
		          $st += 1;
		          break;
		  case 2:
		          $st += 2;
		          break;
		  case 3:
		          $st += 4;
		          break;
		  case 4:
		          $st += 8;
		          break;
	             }
	     }
	     $codice .= sprintf("%X", $st);
                } else {
	     $codice .= "0";
                }

                //peso
                $idgr = $this->getGruppo();
                if ($tipo == 1 || $idgr == 29) {
	     $codice .= sprintf("%02X", $this->getPesoMin());
	     $pmax = $this->getPesoMax() + 1;
	     if ($pmax < 255)
	             $codice .= sprintf("%02X", $pmax);
	     else
	             $codice .= "FF";
                } else {
	     $codice .= "0000";
                }

                //speciale
                if ($this->isHandicap())
	     $codice .= "s";
                else
	     $codice .= "n";

                return $codice;
        }

        /**
         * @access public
         * @return int[]
         */
        public function getCinture() {
                if (!is_null($this->cinture))
	     return $this->cinture;
                $this->cinture = explode("|", $this->get("cinture"));
                return $this->cinture;
        }

        /**
         * @access public
         * @param int[] $cinture
         * @return void
         */
        public function setCinture($cinture) {
                $this->cinture = $cinture;
                $this->set("cinture", implode("|", $cinture));
        }

        /**
         * @access public
         * @return int
         */
        public function getEtaMin() {
                return $this->get("etamin");
        }

        /**
         * @access public
         * @param int $etaMin
         * @return void
         */
        public function setEtaMin($etaMin) {
                $this->set("etamin", $etaMin);
        }

        /**
         * @access public
         * @return int
         */
        public function getEtaMax() {
                return $this->get("etamax");
        }

        /**
         * @access public
         * @param int $etaMax
         * @return void
         */
        public function setEtaMax($etaMax) {
                $this->set("etamax", $etaMax);
        }

        /**
         * @access public
         * @return int
         */
        public function getEtaComponenteMin() {
                return $this->get("etacompmin");
        }

        /**
         * @access public
         * @param int $etaMax
         * @return void
         */
        public function setEtaComponenteMin($etaCompMin) {
                $this->set("etacompmin", $etaCompMin);
        }

        /**
         * @access public
         * @return int[]
         */
        public function getStili() {
                if (!is_null($this->stili))
	     return $this->stili;
                if (is_null($this->get("stili")))
	     $this->stili = array();
                else
	     $this->stili = explode("|", $this->get("stili"));
                return $this->stili;
        }

        /**
         * @access public
         * @param int[] $stili
         * @return void
         */
        public function setStili($stili) {
                $this->stili = $stili;
                $this->set("stili", implode("|", $stili));
        }

        /**
         * @access public
         * @return int
         */
        public function getPesoMin() {
                return $this->get("pesomin");
        }

        /**
         * @access public
         * @param int $pesoMin
         */
        public function setPesoMin($pesoMin) {
                $this->set("pesomin", $pesoMin);
        }

        /**
         * @access public
         * @return int
         */
        public function getPesoMax() {
                return $this->get("pesomax");
        }

        /**
         * @access public
         * @param int $pesoMax
         * @return void
         */
        public function setPesoMax($pesoMax) {
                $this->set("pesomax", $pesoMax);
        }

        /**
         * @access public
         * @return int
         */
        public function getTipo() {
                return $this->get("tipogara");
        }

        /**
         * @access public
         * @param int $tipo
         * @return void
         */
        public function setTipo($tipo) {
                $this->set("tipogara", $tipo);
        }

        /**
         * @access public
         * @return int
         */
        public function getSesso() {
                return $this->get("sesso");
        }

        /**
         * @access public
         * @param int $sesso
         */
        public function setSesso($sesso) {
                $this->set("sesso", $sesso);
        }

        /**
         * Indica se questa categoria accetta squadre miste
         * @return boolean
         */
        public function accettaMisti() {
                return $this->getBool('misti');
        }

        /**
         * 
         * @param bool $val
         */
        public function setAccettaMisti($val) {
                $this->setBool('misti', $val);
        }

        /**
         * @access public
         * @return boolean
         */
        public function isHandicap() {
                return $this->getBool("hp");
        }

        /**
         * @access public
         * @param boolean $hp
         */
        public function setHandicap($hp) {
                $this->setBool("hp", $hp);
        }

        /**
         * @return int
         */
        public function getGruppo() {
                return $this->get("idgruppo");
        }

        /**
         * @param int $idgruppo
         */
        public function setGruppo($idgruppo) {
                $this->set("idgruppo", $idgruppo);
        }

        /**
         * @return string
         */
        public function getNomeEta() {
                return $this->get("nome");
        }

        /**
         * @param string $nome
         */
        public function setNomeEta($nome) {
                $this->set("nome", $nome);
        }

        /**
         * @return int
         */
        public function getNumero() {
                return $this->get("numero");
        }

        /**
         * @param int $num
         */
        public function setNumero($num) {
                $this->set("numero", $num);
        }

// 	public function isSeparata() {
// 		return !is_null($this->get("idseparata"));
// 	}
// 	/**
// 	 * @return int
// 	 */
// 	public function getNumeroSeparata() {
// 		return $this->get("numsep");
// 	}
// 	public function getOrigineSeparata() {
// 		return $this->get("idseparata");
// 	}

        /**
         * @param boolean $rigenera false per utilizzare il nome generato precedentemente
         * @return string 
         */
        public function getNome($rigenera = false) {
                if (!$rigenera && !is_null($this->nome))
	     return $this->nome;
                /*
                  if($this->getGruppo() == 33)
                  return $this->getNomeEta();
                 */
                $tipo = $this->getTipo();
                $gruppo_obj = new GruppoCat($this->getGruppo());
                
                $ind = $gruppo_obj->isIndividuale();


                $gruppo = $this->getGruppo();
                if ($gruppo == 29 || $gruppo == 83 )
	            $n = Lingua::getParola("#catgr29_desc");
                 else if ($gruppo ==87 || $gruppo ==88) 
                 {
                    switch($tipo)
                    {
                    case 0:
                      $n = "Kata".Lingua::getParola("#catgrnaz_desc");
                         break;
                     
                     case 1:
                          $n = "Sanbon".Lingua::getParola("#catgrnaz_desc");
                          //$n = Lingua::getParola("#catgrnaz_desc");
                         break;
                     
                      case 2:
                           $n = "Ippon".Lingua::getParola("#catgrnaz_desc");
                           //$n = Lingua::getParola("#catgrnaz_desc");
                         break;
                     
                       case 3:
                      $n = "Kata".Lingua::getParola("#catgrnaz_desc");
                           // $n = Lingua::getParola("#catgrnaz_desc");
                         break;
                     
                      case 4:
                          $n = "Kumite".Lingua::getParola("#catgrnaz_desc");
                          //$n = Lingua::getParola("#catgrnaz_desc");
                         break;
                     
                     
                     
                     }
                }
                else {
                   if ($ind)
	             $n = Lingua::getParola("#cat{$tipo}_desc");
            
	     else
	             $n = Lingua::getParola("#cat{$tipo}_team_desc");
                }

                if ($this->isHandicap())
	     $n = str_replace("<HP>", Lingua::getParola("#cat_hp"), $n);
                else
	     $n = str_replace("<HP>", "", $n);

                $n = str_replace("<ID>", $this->getChiave(), $n);

                $n = str_replace("<SEX>", Sesso::toStringLungo($this->getSesso()), $n);

                $eta = $this->getNomeEta();
                if (is_null($eta)) {
	     if ($this->getEtaMin() == 0)
	             $eta = "-" . $this->getEtaMax();
	     else if ($this->getEtaMax() == self::MAX_ETA)
	             $eta = "+" . $this->getEtaMin();
	     else
	             $eta = $this->getEtaMin() . "-" . $this->getEtaMax();
	     $eta = str_replace("<AGENUM>", $eta, Lingua::getParola("#cat_age"));
                }
                $n = str_replace("<AGE>", $eta, $n);

                $cin = $this->getCinture();
                if (count($cin) == 1)
	     $cinstr = Cintura::getCintura(current($cin))->getNome();
                else {
	     //TODO dipende dall'id!!!!
	     $cmin = Cintura::getCintura(min($cin))->getNome();
	     $cmax = Cintura::getCintura(max($cin))->getNome();
	     $cinstr = "$cmin-$cmax";
                }
                $n = str_replace("<BELT>", $cinstr, $n);

                if (strpos($n, "<WEIGHT>") !== false) {
	     if ($this->getPesoMax() == self::MAX_PESO) {
	             $pmin = $this->getPesoMin();
	             if ($pmin == 0)
		  $peso = Lingua::getParola("#peso_open");
	             else
		  $peso = '+' . ($pmin - 1);
	     } else {
	             $peso = "-" . $this->getPesoMax();
	     }
	     $n = str_replace("<WEIGHT>", $peso, $n);
	     //TODO unita misura
                }

                if (strpos($n, "<STYLE>") !== false) {
	     $stili = $this->getStili();
	     if (count($stili) >= 4)
	             $stilestr = "";
	     else if (count($stili) == 1) {
	             reset($stili);
	             $stilestr = Stile::getStile(current($stili))->getNome();
	     } else {
	             $stilestr = false;
	             foreach ($stili as $s) {
		  if ($stilestr !== false)
		          $stilestr .= "-";
		  $stilestr .= Stile::getStile($s)->getNome();
	             }
	     }
	     $n = str_replace("<STYLE>", $stilestr, $n);
                }
                $this->nome = htmlspecialchars($n);

                $num = $this->get("numero");
                if (!is_null($num)) {
	     $this->nome = sprintf("%03d - ", $num) . $this->nome;
                }

                $this->nome = trim(preg_replace('/\s+/', ' ', $this->nome));
                return $this->nome;
        }

        /**
         * indica se ad una certa data l'atleta pu� appartenere alla categoria
         * @access public
         * @param Atleta $atleta 
         * @param Data $data
         * @param boolean $cintura true per considerare anche la cintura
         * @return boolean
         */
        public function inCategoria($atleta, $data, $cintura = true) {
                if ($atleta->isHandicap() != $this->isHandicap())
	     return false;
                //sesso
                if (($atleta->getSesso() & $this->getSesso()) == 0)
	     return false;
                //cintura
                if ($cintura && !in_array($atleta->getCintura(), $this->getCinture()))
	     return false;

                $eta = $atleta->getEta($data);
                if ($eta < $this->getEtaMin())
	     return false;
                if ($eta > $this->getEtaMax())
	     return false;
                return true;
        }

        /**
         * indica se ad una certa data un iscritto individuale pu� appartenere alla categoria
         * @access public
         * @param Atleta $atleta l'atleta relativo all'iscrizione
         * @param IscrittoIndividuale $iscr
         * @param Data $data
         * @return boolean
         */
        public function individualeInCategoria($atleta, $iscr, $data) {
                if ($iscr->isHandicap() != $this->isHandicap())
	     return false;
                
                //tipo gara
                if ($iscr->getTipoGara() != $this->getTipo())
	     return false;
                //info base atleta
                if (!$this->inCategoria($atleta, $data, false))
	     return false;
                //cintura
                if (!in_array($iscr->getCintura(), $this->getCinture()))
	     return false;
                //peso
                if (!is_null($this->getPesoMin())) {
	     if ($iscr->getPeso() < $this->getPesoMin())
	             return false;
	     if ($iscr->getPeso() > $this->getPesoMax())
	             return false;
                }
               
                
            /*
                //stile
                if (count($this->getStili()) > 0 && !in_array($iscr->getStile(), $this->getStili()))
	     return false;
               */
                 
                return true;
        }

        /**
         * @param int[] $sesso numero di componenti per ogni sesso, chiavi Sesso::M e Sesso::F
         * @param int $etamin
         * @param int $etamax
         * @param int $tipo
         * @param int[] $cinture
         */
        public function squadraInCategoria($sesso, $etamin, $etamax, $tipo, $cinture) {
                if ($tipo != $this->getTipo())
	     return false;

                $gruppo = $this->getGruppo();

                $thsesso = $this->getSesso();
                if ($thsesso != Sesso::MISTO) {
	     if ($this->accettaMisti()) {
	             //la maggioranza dev'essere del sesso della categoria
	             //cerca il sesso maggiore
	             if ($sesso[Sesso::M] > $sesso[Sesso::F]) {
		  //maggioranza M, categoria M
		  if ($thsesso != Sesso::M)
		          return false;
	             } elseif ($sesso[Sesso::M] < $sesso[Sesso::F]) {
		  //maggioranza F, categoria F
		  if ($thsesso != Sesso::F)
		          return false;
	             }
	     } else {
	             //devono essere tutti dello stesso sesso
	             $totsessi = $sesso[Sesso::M] + $sesso[Sesso::F];
	             if ($sesso[$thsesso] != $totsessi)
		  return false;
	     }
                }
                else {
	     if ($gruppo == 47 || $gruppo == 48) {
	             //le categorie miste non accettano squadre di un solo sesso
	             if ($sesso[1] == 0)
		  return false;
	             if ($sesso[2] == 0)
		  return false;

	             if ($gruppo == 48) {
		  //questo gruppo non accetta gruppi misti con un numero di femmine superiore a quello di maschi
		  if ($sesso[2] > $sesso[1])
		          return false;
	             }
	     }
                }

                $ecm = $this->getEtaComponenteMin();
                if (is_null($ecm))
	     $ecm = $this->getEtaMin();
                if ($ecm > $etamin)
	     return false;
                if ($this->getEtaMin() > $etamax)
	     return false;
                if ($this->getEtaMax() < $etamax)
	     return false;

                //cerca la cintura pi� alta
                $max = NULL;
                foreach ($cinture as $idc) {
	     if ($max === NULL || $idc > $max)
	             $max = $idc;
                }
                //la citura pi� alta dev'essere in questa categoria
                if (!in_array($max, $this->getCinture()))
	     return false;

                if ($gruppo == 46 || $gruppo == 47 || $gruppo == 48) {
	     //in questi gruppi c'� sovrapposizione di cinture
	     //identifico la categoria giusta dalla cintura pi� bassa
	     //cerca cintura pi� bassa
	     $min = NULL;
	     foreach ($cinture as $idc) {
	             if ($min === NULL || $idc < $min)
		  $min = $idc;
	     }
	     //la citura pi� bassa dev'essere in questa categoria
	     if (!in_array($min, $this->getCinture()))
	             return false;
                }

                return true;
        }

        /**
         * Indica se la categoria � di agonisti
         * @return boolean
         */
        public function isAgonista() {
                return $this->getEtaMax() > 14;
        }

        /**
         * @param Categoria $cat
         */
        public function uguale($cat) {
                if ($this->getEtaMax() != $cat->getEtaMax())
	     return false;
                if ($this->getTipo() != $cat->getTipo())
	     return false;
                if ($this->getSesso() != $cat->getSesso())
	     return false;
                if ($this->getEtaMin() != $cat->getEtaMin())
	     return false;
                if ($this->getPesoMax() != $cat->getPesoMax())
	     return false;
                if ($this->getPesoMin() != $cat->getPesoMin())
	     return false;
                //cinture
                if (!$this->insiemeUguale($this->getCinture(), $cat->getCinture()))
	     return false;
                //stili
                if (!$this->insiemeUguale($this->getStili(), $cat->getStili()))
	     return false;
                return true;
        }

        private function insiemeUguale($arr1, $arr2) {
                if (is_null($arr1)) {
	     if (is_null($arr2))
	             return true; //entrambi null
	     else
	             return false; //solo 1 null
                }
                if (is_null($arr2))
	     return false; //solo 2 null
                if (count($arr1) != count($arr2))
	     return false;
                foreach ($arr1 as $e) {
	     if (!in_array($e, $arr2))
	             return false;
                }
                return true;
        }

// 	/**
// 	 * @param int $num numero di categorie da creare, 
// 	 * 0 per caricare solo quelle gi� esistenti
// 	 * @return Categoria[] le categorie separate
// 	 */
// 	public function separa($num=0) {
// 		for ($i = 1; $i<=$num; $i++) 
// 			$ret[$i] = NULL;
// 		$id = $this->getChiave();
// 		$where = "idseparata = '$id'";
// 		if ($num >= 0) $where .= " AND numsep <= '$num'";
// 		$mr = $this->_connessione->select("categorie", $where);
// 		while($row = $mr->fetch_assoc()) {
// 			$c = new Categoria();
// 			$c->carica($row);
// 			$ret[$c->getNumeroSeparata()] = $c;
// 		}
// 		//crea le categorie mancanti
// 		for ($i = 1; $i<=$num; $i++) {
// 			if (!is_null($ret[$i])) continue;
// 			$nc = new Categoria();
// 			$this->clona($nc);
// 			$nc->setGruppo(0);
// 			$nc->set("numsep",$i);
// 			$nc->salva();
// 			$ret[$i] = $nc;
// 		}
// 		return $ret;
// 	}

        protected function caricaListaResult($nome) {
                if (!$this->hasChiave())
	     return;
                switch ($nome) {
	     case 'cinture':
	             $tab = "cinturecategoria";
	             $col = "idcintura";
	             break;
	     case 'stili':
	             $tab = "stilicategoria";
	             $col = "idstile";
	             break;
	     default:
	             return NULL;
                }
                $id = $this->getChiave();
                return $this->_connessione->select($tab, "idcategoria = '$id'", $col);
        }

// 	/**
// 	 * @param Categoria $n
// 	 */
// 	protected function clona(&$n) {
// 		parent::clona(&$n);
// 		$n->stili = $this->stili;
// 		$n->cinture = $this->cinture;
// 		$n->nome = NULL;
// 	}
}

?>