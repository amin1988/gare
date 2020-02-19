<?php

if (!defined("_BASEDIR_"))
    exit();

session_start();

include_model("Categoria", "Gara", "Ranking", "IscrittoIndividuale", "Utente", "RankingLog");
include_controller("resp/riepilogo_individuale");

class StageResultCtrl
{

    private $gare;
    private $array_eventi = NULL;
    private $ranking = NULL;
    private $elenco_categorie = NULL;
    private $categorie = NULL;
    private $id_gara = 0;

    public function __construct($id_gara)
    {

        $modo = isset($_GET['modo']) ? $_GET['modo'] : NULL;
        $this->id_gara = isset($_GET['id']) ? $_GET['id'] : NULL;
        if (!empty($modo))
        {
            if ($modo == "convalida")
            {
                $this->convalidaStageRanking();
            } else if ($modo == "vista")
            {
                
            }
        }
        $this->ranking = new Ranking();
        $this->array_eventi = $this->ranking->getRankingEventi();
    }

    public function getEventi()
    {
        return $this->array_eventi;
    }

    public function convalidaStageRanking()
    {
        if (!empty($this->id_gara))
        {
            $ranking_classifica = new RankingClassifica();
            $ranking_classifica->convalidaStageRanking($this->id_gara);
        }
    }

    public function getPresentiStage()
    {
        
    }

}
