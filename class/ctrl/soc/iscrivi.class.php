<?php

if (!defined("_BASEDIR_"))
    exit();

include_model("UtSocieta", "Gara", "Cintura", "Stile", "AtletaEsterno", "IscrittoIndividuale", "Zona", 'Stage');

include_controller("soc/iscrivi_base");

include_errori("VerificaIscritti", "VerificaCoach");

class Iscrivi extends IscriviBase
{

    /**

     * Atleti che possono partecipare 

     * @var Atleta[] 

     */
    protected $atok;

// 	/**
// 	 * Atleti che non possono partecipare
// 	 *  @var Atleta[] 
// 	 */
// 	protected $atno;

    /**

     * Atleti gi? iscritti. formato: idatleta => $idtipo => IscrittoIndividuale

     * @var IscrittoIndividuale[][]

     */
    protected $iscritti;

    /**

     * i tipi di gara a cui pu? partecipare l'atleta. formato idatleta => tipo[]

     * @var int[][]

     */
    protected $tipi;

    /**

     * @var VerificaIscritti

     */
    private $errori;

    /**

     * Campi vuoti da inserire

     * @var int

     */
    private $numCampi = 10;
    private $nonverif;
    private $hp = false;
    private  $tipo_gara = NULL;

    protected function initUtente()
    {

        return UtSocieta::crea();
    }

    protected function initGara()
    {

        return new Gara($_GET["id"]);
        
    }

    protected function initIscritti()
    {

        return IscrittoIndividuale::listaGara($this->gara->getChiave(), $this->ut->getSocieta()->getChiave());
    }

    /**

     * viene chiamato se non ci sono errori nella compilazione del modulo

     */
    protected function redirect()
    {

        redirect("soc/riepilogo.php?id=" . $this->getGara()->getChiave());

        exit();
    }

    public function __construct()
    {

        $this->ut = $this->initUtente();

        if (is_null($this->ut))
            nologin();



        if (!isset($_GET["id"]))
        {

            homeutente($this->ut);

            exit();
        }



        $this->gara = $this->initGara();
        $this->tipo_gara = $this->gara->getTipoGara();

        if (!$this->gara->esiste() || $this->gara->iscrizioniChiuse())
        {

            if ($_SESSION["backdoor"] != "aprigara")
            {

                homeutente($this->ut);

                exit();
            }
        }



        if (!$this->gara->isIndividuale())
        {

            redirect("soc/iscrivisq.php?id=" . $this->gara->getChiave());

            exit();
        }



        //controllo zone

        $soc = $this->ut->getSocieta();

        $zonaut = $soc->getZona();

        $zonegara = $this->gara->getZone();

        $trovata = false;

        while (!is_null($zonaut))
        {

            if (in_array($zonaut, $this->gara->getZone()))
            {

                $trovata = true;

                break;
            }

            $zonaut = Zona::getZona($zonaut)->getPadre();
        }

        if (!$trovata)
        {

            homeutente($this->ut);

            exit();
        }



        if (isset($_POST["tipo"]))
            foreach (array_keys($_POST["tipo"]) as $id)
                $_POST["check"][$id] = $id;
        else
            unset($_POST["check"]);





        if (isset($_POST["newtipo"]))
            foreach (array_keys($_POST["newtipo"]) as $id)
                $_POST["newcheck"][$id] = $id;
        else
            unset($_POST["newcheck"]);



        //calcolo nuovi campi

        if ($soc->isAffiliata())
            $this->numCampi = 0;

        if (isset($_POST["newcheck"]))
        {

            $max = max($_POST["newcheck"]) + 1;

            if ($max > $this->numCampi)
                $this->numCampi = $max;
        }



        //caricamento atleti

        $atl = $soc->getAtleti();

        $this->nere = array();

        $this->caricaCoach();

        $this->caricaArbitri();



        //lettura iscritti

        $iscr = $this->initIscritti();

        $this->iscritti = array();

        foreach ($iscr as $value)
        {

            /** @var $value Iscritto */
            $this->iscritti[$value->getAtleta()][$value->getTipoGara()] = $value;

            if ($value->isHandicap())
            {

                $a = $atl[$value->getAtleta()];

                $a->setHandicap(true);
            }
        }



        $this->errori = new VerificaIscritti();

        $this->errCoach = new VerificaCoach($this->gara);



        $salva = true;

        if (isset($_POST["pageid"]))
        {

            if (isset($_SESSION["iscrivi_pageid"]) && $_SESSION["iscrivi_pageid"] == $_POST["pageid"])
            {

                $salva = false;
            } else
            {

                $_SESSION["iscrivi_pageid"] = $_POST["pageid"];

                $_SESSION["iscrivi_nuoviid"] = array();
            }
        }





        $modch = $this->salvaCoach($atl, $salva && !$this->getErroriCoach()->haErroreNum());

        if ($modch)
        {

            $this->gara->setCoachSocieta($this->coach, $soc->getChiave());

            $this->gara->salva();
        }

        $this->salvaArb($this->arb);
        if (empty($this->tipo_gara))
        {
           
            if ( isset($_POST['stage_nazionale']) )
            {
                $this->tipo_gara = $_POST['stage_nazionale'];
            }
            
            else {
                $this->tipo_gara = NULL;
            }
        }

        if ( $this->tipo_gara == 'gara')
        {
            $this->salvaIscrizioni($salva);
        }
        //lettura atleti

        $this->atok = array();

        $cintura = $this->cintureFisse();

        $this->nonverif = false;

        foreach ($atl as $a)
        {

            /* @var $a Atleta */

            $hp = $a->isHandicap();

            $a->setHandicap(false);

            $tipi = $this->gara->puoPartecipareIndiv($a, $cintura);



            //FIXME patch per la gara nazionale 2013, eliminare

            if ($this->gara->getChiave() == 61 && $a->getDataNascita()->getAnno() == 2000)
                $tipi = array();



            if (count($tipi) > 0)
            {

                if (!$a->isVerificato())
                    $this->nonverif = true;

                if ($hp || isset($_POST["hp"][$a->getChiave()]))
                    $a->setHandicap(true);

                $this->atok[] = $a;

                $this->tipi[$a->getChiave()] = array_keys($tipi);
            }

            //controllo se nera

            if ($a->getCintura() == Cintura::cinturaNera())
            {

                $this->nere[$a->getChiave()] = $a;
            }
        }



        $this->setHandicap($this->gara->getCategorieIndiv());

        $this->setHandicap($this->gara->getCategorieSquadre());



        $this->pulisciCoach();
    }

    public function salvaPresAtleti($salva)
    {
         if (!isset($_POST["pageid"]))
            return;
         
         if ( !empty($this->tipo_gara))
        {
            $this->salvaPresStageAtleti($salva, 'stage_nazionale');
        } 
        else{
            $this->salvaPresStageAtleti($salva, null);
        }
    }

    private function setHandicap($catlist)
    {

        if ($this->hp)
            return;

        foreach ($catlist as $c)
        {

            /* @var $c Categoria */

            if ($c->isHandicap())
            {

                $this->hp = true;

                return;
            }
        }
    }

    public function haErrori()
    {

        return $this->errCoach->haErrori() || $this->errori->haErrori();
    }

    /**

     * @return VerificaIscritti

     */
    public function getErrori()
    {

        return $this->errori;
    }

    /**

     * @return Gara

     */
    public function getGara()
    {

        return $this->gara;
    }

    public function getMinCoach()
    {

        return $this->gara->getMinCoach();
    }

    public function getMaxCoach()
    {

        return $this->gara->getMaxCoach();
    }

    /**

     * Restituisce gli atleti che possono partecipare alla gara

     * @return Atleta[]

     */
    public function getAtletiOk()
    {

        return $this->atok;
    }

    public function getCinture()
    {

        return Cintura::listaCinture();
    }

    /**

     * Indica se un atleta pu? iscriversi ad un certo tipo di gara

     * @param Atleta $atleta

     * @param int $tipo

     */
    public function tipoGaraOk($atleta, $tipo)
    {

        if (isset($this->tipi[$atleta->getChiave()]))
            return in_array($tipo, $this->tipi[$atleta->getChiave()]);

        return false;
    }

    public function getNomeCintura($id)
    {

        return Cintura::getCintura($id)->getNome();
    }

    public function getStili()
    {

        return Stile::listaStili();
    }

    public function usaPeso()
    {

        return $this->gara->usaPeso();
    }

    public function cintureFisse()
    {

        return $this->ut->getSocieta()->isAffiliata();
    }

    public function nuoviCampi()
    {

        return $this->numCampi;
    }

    public function puoAggiungereCampi()
    {

        return $this->numCampi > 0;
    }

    public function numRigaJs()
    {

        return (count($this->atok) + $this->numCampi) % 2;
    }

    /**

     * @param Atleta $a o int se ? un nuovo campo

     * @return boolean

     */
    public function isIscritto($a)
    {

        if (is_numeric($a))
        {

            $id = $a;

            $c = "newcheck";
        } else
        {

            $id = $a->getChiave();

            $c = "check";

            if (isset($this->iscritti[$id]))
                return true;
        }

        if (!isset($_POST[$c]))
            return false;

        return in_array($id, $_POST[$c]);
    }

    /**

     * @param Atleta $a o int se ? un nuovo campo

     * @return int idcintura

     */
    public function cinturaIscritto($a)
    {

        if (is_numeric($a))
        {

            $indb = false;

            $id = $a;

            $c = "newcintura";

            $def = 0;
        } else
        {

            $id = $a->getChiave();

            $c = "cintura";

            $def = $a->getCintura();

            $indb = isset($this->iscritti[$id]);
        }

        if (isset($_POST[$c][$id]))
            return $_POST[$c][$id];

        if ($indb)
        {

            foreach ($this->iscritti[$id] as $i)
                return $i->getCintura();
        }

        return $def;
    }

    /**

     * @param Atleta $a o int se ? un nuovo campo

     * @return int idcintura

     */
    public function stileIscritto($a)
    {

        if (is_numeric($a))
        {

            $id = $a;

            $c = "newstile";

            $indb = false;
        } else
        {

            $id = $a->getChiave();

            $c = "stile";

            $indb = isset($this->iscritti[$id]);
        }

        if (isset($_POST[$c][$id]))
            return $_POST[$c][$id];

        if ($indb)
        {

            foreach ($this->iscritti[$id] as $isc)
            {

                /* @var $isc IscrittoIndividuale */

                if (!is_null($isc->getStile()))
                    return $isc->getStile();
            }
        }

        return $this->ut->getSocieta()->getStile();
    }

    public function getStileDefault()
    {

        return $this->ut->getSocieta()->getStile();
    }

    /**

     * @param Atleta $a o int se ? un nuovo campo

     * @param int $idtipo

     * @return boolean

     */
    public function tipoIscritto($a, $idtipo)
    {

        if (is_numeric($a))
        {

            $id = $a;

            $c = "newtipo";

            $indb = false;
        } else
        {

            $id = $a->getChiave();

            $c = "tipo";

            $indb = isset($this->iscritti[$id]);
        }

        if (isset($_POST[$c][$id]))
            return isset($_POST[$c][$id][$idtipo]); //in_array($idtipo, $_POST[$c][$id]);

        if ($indb)
        {

            foreach ($this->iscritti[$id] as $isc)
            {

                /* @var $isc IscrittoIndividuale */

                if ($isc->getTipoGara() == $idtipo)
                    return true;
            }
        }

        return false;
    }

    /**

     * @param Atleta $a o int se ? un nuovo campo

     * @return int

     */
    public function pesoIscritto($a)
    {

        if (is_numeric($a))
        {

            $id = $a;

            $c = "newpeso";

            $indb = false;
        } else
        {

            $id = $a->getChiave();

            $c = "peso";

            $indb = isset($this->iscritti[$id]);
        }

        if (isset($_POST[$c][$id]))
            return $_POST[$c][$id];

        if ($indb)
        {

            foreach ($this->iscritti[$id] as $isc)
            {

                /* @var $isc IscrittoIndividuale */

                if (!is_null($isc->getPeso()))
                    return $isc->getPeso();
            }
        }

        return "";
    }

    public function nuovoCampo($campo, $id)
    {

        if (!isset($_POST[$campo][$id]))
            return "";

        return $_POST[$campo][$id];
    }

    /**

     * @param string $nome

     * @param boolean $new

     * @return string

     */
    public static function nomeCampo($nome, $new)
    {

        if ($new)
            return "new$nome";

        return $nome;
    }

    /**

     * @deprecated

     */
    public static function leggiData($valore)
    { //TODO eliminare
        //formato dd/mm/yyyy
        return Data::parseDMY($valore);
    }

    /**

     * @return boolean true se ci sono atleti non verificati

     */
    public function haNonVerificati()
    {

        return $this->nonverif;
    }

    /**

     * @return boolean true se ci sono categorie handicap

     */
    public function haHandicap()
    {

        return $this->hp;
    }

    public function salvaPresStageAtleti($salva, $tipo_evento)
    {

        $id_gara = $this->gara->getIDGara();
        $soc = $this->ut->getSocieta();
        $lista_atleti_presenti = $_POST[$tipo_evento];
        $stage = new Stage(null, 'partecipanti_stage');
        if (!empty($tipo_evento))
        {
            $elenco_atleti_da_eliminare = $stage->getRowPresenzeDiff($id_gara, $lista_atleti_presenti, $soc->getIdAffiliata());
            if (!empty($elenco_atleti_da_eliminare))
            {
                $stage->deleteFromPresenzeStage($id_gara, $elenco_atleti_da_eliminare, $soc->getIdAffiliata());
            }
        }
        else{
             $stage->deleteFromPresenzeStage($id_gara, null, $soc->getIdAffiliata());
        }
        for ($i = 0; $i < count($lista_atleti_presenti); $i++)
        {
            $id_atleta = $lista_atleti_presenti[$i];

            $stage->insertPresenze($id_gara, $id_atleta, $soc->getIdAffiliata());
        }
    }

    /**

     * politica: modificare il database solo per gli atleti senza nessun errore 

     */
    protected function salvaIscrizioni($salva)
    {

        if (!isset($_POST["pageid"]))
            return; //chiamata non effettuata



        $del = $this->rimuoviCancellati();

        $this->iscriviVecchiAtleti($salva);

        $this->iscriviNuoviAtleti($salva);



        foreach ($del as $i)
        {

            /* @var $i IscrittoIndividuale */

            $ida = $i->getAtleta();

            if (!$salva && in_array($ida, $_SESSION["iscrivi_nuoviid"]))
                continue; //non eliminare i nuovi atleti

            if (!$this->errori->isErrato($ida))
            {

                unset($this->iscritti[$ida][$i->getTipoGara()]);

                if (count($this->iscritti[$ida]) == 0)
                    unset($this->iscritti[$ida]);

                if ($salva)
                    $i->elimina();
            }
        }



        if (!$this->haErrori())
            $this->redirect();
    }

    /**

     * Elimina da $iscritti e dal db gli atleti che non risultano

     * pi? iscritti

     * @return IscrittoIndividuale[] gli iscritti da eliminare

     */
    protected function rimuoviCancellati()
    {

        $ret = array();

        foreach ($this->iscritti as $ida => $isc)
        {

            if (!isset($_POST["check"][$ida]))
            {

                //l'atleta non ? pi? iscritto a nessuna gara

                foreach ($isc as $i)
                    $ret[] = $i;
            } else if (!$this->errori->isErrato($ida))
            {

                //se non ha errori
                //cancella i tipi gara a cui non ? pi? iscritto

                foreach ($isc as $k => $i)
                {

                    /* @var $i IscrittoIndividuale */

                    if (!isset($_POST["tipo"][$ida][$i->getTipoGara()]))
                    {

                        $ret[] = $i;
                    }
                }
            }
        }

        return $ret;
    }

    protected function iscriviVecchiAtleti($salva = true)
    {

        if (!isset($_POST["check"]))
            return;



        $soc = $this->ut->getSocieta();

        foreach ($_POST["check"] as $ida)
        {

            //se ha errori non viene salvato

            if ($this->errori->isErrato($ida))
                continue;



            $a = $soc->getAtleta($ida);

            $newcin = false;

            $inCat = true;

            $tmpisc = array();

            foreach ($_POST["tipo"][$ida] as $tipo)
            {

                if (!$a->isVerificato())
                    continue; //non iscrive atleti non verificati

                if (isset($this->iscritti[$ida][$tipo]))
                {

                    //se ? gi? iscritto

                    $i = $this->iscritti[$ida][$tipo];

                    $newcin |= self::aggiornaIscritto($i, $ida, $a);

                    $i->calcolaCategoria($this->gara, $a);
                } else
                {

                    //nuova iscrizione

                    $idcin = $this->cinturaIscritto($a);

                    $dati = array(
                        "atleta" => $a,
                        "cintura" => $idcin,
                        "tipo" => $tipo,
                        "stile" => $_POST["stile"][$ida],
                        "peso" => $_POST["peso"][$ida],
                        "hp" => isset($_POST["hp"][$ida]));

                    $i = IscrittoIndividuale::nuovo($this->gara, $dati);

                    $newcin |= ($idcin != $a->getCintura());
                }

                $inCat &= $i->inCategoria();

                if (!$i->inCategoria())
                {

                    $inCat = false;

                    $this->errori->setErroreCat($ida, $tipo, false);
                }

                $tmpisc[] = $i;
            }



            if (_WKC_MODE_)
            {

                $san = 0;

                $ipo = 0;

                foreach ($tmpisc as $iscr)
                {

                    if ($iscr->getTipoGara() == 1)
                        $san = 1;

                    if ($iscr->getTipoGara() == 2)
                        $ipo = 1;
                }

                if ($san + $ipo > 1)
                {

                    $this->errori->setErroreCat($ida, 90, false);

                    $inCat = false;
                }
            }



            if ($inCat)
            {

                //tutto ok, salva

                if ($newcin)
                {

                    $a->setCintura($_POST["cintura"][$ida]);

                    if ($salva)
                        $a->salva();
                }

                foreach ($tmpisc as $i)
                {

                    /* @var $i IscrittoIndividuale */

                    if ($salva)
                        $i->salva();

                    $this->iscritti[$ida][$i->getTipoGara()] = $i;
                }
            } else
            {

                //non va bene, ripristina tutto

                foreach ($tmpisc as $i)
                {

                    /* @var $i IscrittoIndividuale */

                    $i->ripristina();
                }
            }
        }
    }

    protected function iscriviNuoviAtleti($salva = true)
    {

        if (!isset($_POST["newcheck"]))
            return;



        echo "entro";



        /* @var $soc Societa */

        $soc = $this->ut->getSocieta();

        foreach ($_POST["newcheck"] as $ida)
        {

            //se ha errori non viene salvato

            if ($this->errori->isErratoNuovo($ida))
                continue;



            $dati = array(
                "societa" => $soc->getChiave(),
                "nome" => $_POST["nome"][$ida],
                "cognome" => $_POST["cognome"][$ida],
                "sesso" => $_POST["sesso"][$ida],
                "nascita" => $this->leggiData($_POST["nascita"][$ida])
            );

            $a = AtletaEsterno::nuovo($dati);

            $a->setCintura($_POST["newcintura"][$ida]);

            $isc = array();

            $inCat = true;

            foreach ($_POST["newtipo"][$ida] as $tipo)
            {

                $dati = array(
                    "atleta" => $a,
                    "cintura" => $_POST["newcintura"][$ida],
                    "tipo" => $tipo,
                    "stile" => $_POST["newstile"][$ida],
                    "peso" => $_POST["newpeso"][$ida],
                    "hp" => isset($_POST["newhp"][$ida])
                );

                $i = IscrittoIndividuale::nuovo($this->gara, $dati);

                if (!$i->inCategoria())
                {

                    $inCat = false;

                    $this->errori->setErroreCat($ida, $tipo, true);
                } else
                    $isc[] = $i;
            }



            if (_WKC_MODE_)
            {

                $san = 0;

                $ipo = 0;

                foreach ($isc as $iscr)
                {

                    if ($iscr->getTipoGara() == 1)
                        $san = 1;

                    if ($iscr->getTipoGara() == 2)
                        $ipo = 1;
                }



                if ($san + $ipo > 1)
                {

                    $this->errori->setErroreCat($ida, 90, true);

                    $inCat = false;
                }
            }



            if ($inCat)
            {

                if ($salva)
                {

                    $a->salva();

                    $iddb = $a->getChiave();

                    $_SESSION["iscrivi_nuoviid"][] = $iddb;

                    $this->atok[$iddb] = $a;

                    foreach ($isc as $i)
                    {

                        $i->salva();

                        $this->iscritti[$iddb][$i->getTipoGara()] = $i;
                    }
                }

                $this->unsetNuovo($ida);
            }
        }
    }

    /**

     * @param IscrittoIndividuale $iscritto

     * @param int $ida

     * @param Atleta $a

     * @return boolean true se la cintura ? cambiata

     */
    private static function aggiornaIscritto($isc, $ida, $a)
    {

        if (!is_null($isc->getStile()) && $isc->getStile() != $_POST["stile"][$ida])
            $isc->setStile($_POST["stile"][$ida]);

        if (!is_null($isc->getPeso()) && $isc->getPeso() != $_POST["peso"][$ida])
            $isc->setPeso($_POST["peso"][$ida]);

        //controllo con cintura variabile

        if (isset($_POST["cintura"][$ida]) && $isc->getCintura() != $_POST["cintura"][$ida])
        {

            $isc->setCintura($_POST["cintura"][$ida]);

            return true;
        }

        $isc->setHandicap(isset($_POST["hp"][$ida]));

        $a->setHandicap(isset($_POST["hp"][$ida]));

        //controllo con cintra fissa

        if ($isc->getCintura() != $a->getCintura())
            $isc->setCintura($a->getCintura());

        return false;
    }

    private function unsetNuovo($ida)
    {

        unset($_POST["newcheck"][$ida]);

        unset($_POST["nome"][$ida]);

        unset($_POST["cognome"][$ida]);

        unset($_POST["sesso"][$ida]);

        unset($_POST["nascita"][$ida]);

        unset($_POST["newcintura"][$ida]);

        unset($_POST["newtipo"][$ida]);

        unset($_POST["newstile"][$ida]);

        unset($_POST["newpeso"][$ida]);
    }

}
