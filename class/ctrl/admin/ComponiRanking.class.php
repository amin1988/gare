<?php

if (!defined("_BASEDIR_"))
    exit();

session_start();

include_model("Categoria", "Gara", "Ranking", "IscrittoIndividuale", "RankingErrorLog", "RankingClassifica");
include_esterni("AtletaRanking");
class ComponiRankingCtrl
{

    private $gare;
    private $criterio_punteggio = false;
    private $array_eventi = NULL;
    private $ranking = NULL;
    
    private $rank_classifica = NULL;

    public function __construct()
    {
        //$this->gare = Gara::getGarePassate();
        $this->ranking = new Ranking();
        $this->array_eventi = $this->ranking->getRankingEventi();
        $this->criterio_punteggio = $this->ranking->criterioPunteggioRanking();
         $this->rank_classifica = new RankingClassifica();
        $processa = empty($_POST['processa']) ? NULL : $_POST['processa'];
        if (!empty($processa))
        {
            $path = $_SERVER["REQUEST_URI"];
            $id_gara = empty($_POST['gare']) ? NULL : $_POST['gare'];
            if (empty($id_gara))
            {
                print '<script>alert("Non ci sono gare da  associare");</script>';
                $this->redirect($path);
                return;
            }
            $nome_db = "gara_" . $id_gara;
            $tipo_evento = empty($_POST['tipo_evento']) ? NULL : $_POST['tipo_evento'];
            if (empty($tipo_evento))
            {
                print '<script>alert("Selezionare il tipo di evento");</script>';
                $this->redirect($path);
                return;
            }
            $evento = $this->ranking->getRankingEventi();
            $evento = $evento[0];
            $dati = array('anno'=>$_POST['anno_gara']);
            $tipo_gara = $evento['tipo_gara'];
            if ($tipo_gara == "gara_esterna_o_stage") // se è una gara esterna o stage 
            {
                $this->ranking->aggiornaRankingEventi($id_gara, $tipo_evento); // aggiorna semplicemnte il campo
                $this->rank_classifica->componiClassificaRanking($dati,"s");
                 $this->redirect($path);
            } else
            {
                $row_inseriti = $this->ranking->componiTabellaRanking($id_gara, $tipo_evento);


                if ($row_inseriti)
                {
                    $row_aggiornato = $this->ranking->aggiornaRankingEventi($id_gara, $tipo_evento, true);
                    if ($row_aggiornato)
                    {
                        $ergebniseinzel_cancellato = $this->ranking->eliminaDBSwgare($id_gara,"ergebniseinzel_$id_gara");
                        $names_cancellato = $this->ranking->eliminaDBSwgare($id_gara,"names_$id_gara");
                        if ($ergebniseinzel_cancellato && $names_cancellato)          
                        {
                            
                            $this->rank_classifica->componiClassificaRanking($dati,"g");
                           $this->redirect($path);
                        }
                       
                    }
                } else
                {
                    print '<script>alert("Non ci sono atleti da aggiungere");</script>';
                    $this->redirect($path);
                }
            }
        }
    }

    public function redirect($path)
    {

        print '<META HTTP-EQUIV="refresh" CONTENT="0; URL=' . $path . '">';
    }

    public function getEventi()
    {
        return $this->array_eventi;
    }

    public function getCriterioPunteggioRanking()
    {
        return $this->criterio_punteggio;
    }

    public function getNomeGara($id_gara)
    {
        return $this->ranking->getNomeGara($id_gara);
    }

    public function getGare()
    {

        return $this->gare;
    }

}