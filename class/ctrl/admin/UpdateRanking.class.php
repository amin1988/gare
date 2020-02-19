<?php

if (!defined("_BASEDIR_"))
    exit();

session_start();



include_model("Categoria", "Gara", "Ranking", "IscrittoIndividuale", "RankingErrorLog");

class UpdateRankingCtrl
{

    const DOC = 'doc';
    const PUNTI_STAGE = 5;

    private $gare;
    private $finished = false;
    private $punti = array(
        1 => array(1 => 2),
        2 => array(1 => 2,
            2 => 1),
        3 => array(1 => 4,
            2 => 2,
            3 => 1),
        4 => array(1 => 4,
            2 => 2,
            3 => 1,
            4 => 1),
        5 => array(1 => 6,
            2 => 4,
            3 => 2,
            4 => 2,
            5 => 1),
        6 => array(1 => 6,
            2 => 4,
            3 => 2,
            4 => 2,
            5 => 1,
            6 => 1),
        7 => array(1 => 8,
            2 => 6,
            3 => 4,
            4 => 4,
            5 => 2,
            6 => 2,
            7 => 1),
        8 => array(1 => 8,
            2 => 6,
            3 => 4,
            4 => 4,
            5 => 2,
            6 => 2,
            7 => 1,
            8 => 1),
        9 => array(1 => 12,
            2 => 8,
            3 => 6,
            4 => 6,
            5 => 4,
            6 => 4,
            7 => 2,
            8 => 2,
            9 => 1),
    );
    private $ranking_eventi = null;

    public function __construct()
    {
        $this->gare = Gara::getGarePassate();
        $ranking = new ranking ();
        
        $this->ranking_eventi =$ranking->getAllRankingEventi();

        if (isset($_POST["processa"]))
        {
            $idgara = intval($_POST["gare"]);

            /* @var $gara Gara */

            $gara = $this->gare[$idgara];
            $ranking = new Ranking();
            $tipo_gara = "gara";
            if (isset($_POST["gara_esterna_o_stage"]))
            {
                $tipo_gara = "gara_esterna_o_stage";
                $aggiunto_evento_ranking = $ranking->popola_ranking_eventiGare($idgara, $tipo_gara);
            }

            $doc = $_FILES[self::DOC];
            $evento_esiste = $ranking->seEventoEsiste($idgara);

            if ($doc["tmp_name"] != '' && $evento_esiste == FALSE)
            {
                $nome_database = "gara_$idgara";
                //$ranking->getConnessioneGaraSw();
                //$db_esiste = $ranking->seEsisteDB($nome_database);
                // $creato = $ranking->creaDatabase($nome_database);
                $popolato = $ranking->popolaTab_sw_gara($doc["tmp_name"],$idgara); //popola le tabelle del nuovo database sw gare
                $aggiunto_evento_ranking = $ranking->popola_ranking_eventiGare($idgara, $tipo_gara);
                
                if ($aggiunto_evento_ranking == false)
                {
                    $path = $_SERVER["REQUEST_URI"];
                    print '<script>alert("La gara è già stata caricata");</script>';
                    print '<META HTTP-EQUIV="refresh" CONTENT="0; URL=' . $path . '">';
                }
            }
           
        }
    }

// construct()

    private function getPunti($num_atl, $pos)
    {

        if ($pos > 9)
            return 1;



        if ($num_atl > 9)
            $num_atl = 9;



        return $this->punti[$num_atl][$pos];
    }

    public function getGare()
    {

        return $this->gare;
    }
    
    public function getRankingEventi()
    {
           return $this->ranking_eventi;
    }

    public function getFinished()
    {

        return $this->finished;
    }

}