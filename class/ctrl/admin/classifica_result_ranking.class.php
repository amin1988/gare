<?php

if (!defined("_BASEDIR_"))
    exit();

session_start();

include_model("Categoria", "Gara", "Ranking", "IscrittoIndividuale", "Utente", "RankingLog", "RankingClassifica","GruppoCat");
include_controller("resp/riepilogo_individuale");

class ClassificaResultCtrl
{

    private $gare;
    private $array_eventi = NULL;
    private $ranking = NULL;
    private $categorie = NULL;
    private $criterio_punteggio = 0;
    private $array_societa = NULL;
    private $dati = NULL; //conterrà i dati di post (form ricerca con parametri di ricerca - Ranking)

   private $rank_classifica = NULL;
   
   private $classifica_ranking = NULL;
    
    public function __construct()
    {
        
        
        $this->ranking = new Ranking();
        $this->rank_classifica = new RankingClassifica();
        $this->array_eventi = $this->ranking->getRankingEventi();
        $this->criterio_punteggio = $this->ranking->criterioPunteggioRanking();
        $this->caricaCategorie();
        $this->getAllSocieta();
        $this->setDatiPost();
        $this->getClassifica();
        //$this->AllCat();
    }
    
    public function getClassifica()
    {
        //$this->rank_classifica->componiClassificaRanking($this->dati);
        $this->classifica_ranking = $this->rank_classifica->getClassificaRanking($this->dati);
        return $this->classifica_ranking;
    }

    private function setDatiPost()
    {
        if (!empty($_POST['nome']))
            $this->dati['nome'] = $_POST['nome'];

        if (!empty($_POST['cognome']))
            $this->dati['cognome'] = $_POST['cognome'];
        
        if (!empty($_POST['sesso']))
            $this->dati['sesso'] = $_POST['sesso'];

        if (!empty($_POST['id_soc']))
            $this->dati['id_soc'] = $_POST['id_soc'];

        if (!empty($_POST['id_kat']))
            $this->dati['id_kat'] = $_POST['id_kat'];

        if (!empty($_POST['tipologia_evento']))
            $this->dati['tipologia_evento'] = $_POST['tipologia_evento'];

        if (!empty($_POST['anno']))
            $this->dati['anno'] = $_POST['anno'];

    }

    public function getCriterioPunteggio()
    {
        return $this->criterio_punteggio;
    }

    public function getIscritti()
    {
        return $this->iscritti;
    }

    public function getAllSocieta()
    {
        $rank_societa = new RankingClassifica();
        $array_soc = array();
        foreach ($rank_societa->getAllSocieta() as $chiave => $soc)
        {
            $array_soc['nomebreve'] = $soc['nomebreve'];
            $array_soc['idsocieta'] = $soc['idsocieta'];
            $this->array_societa [] = $array_soc;
        }
        return $this->array_societa;
    }

    public function caricaCategorie()
    {
       
        $array_categorie = array();
        foreach ($this->rank_classifica->getAllIdCategoriaInRis() as $id_kat)
        {
            $cat = new Categoria($id_kat);
            $array_categorie['nome'] = $cat->getNome();
            $array_categorie['id_categoria'] = $id_kat;
            $this->categorie [] = $array_categorie;
        }
        return $this->categorie;
    }

    /**

     * @param IscrittoIndividuale[] $iscr

     */
    private function caricaAtleti($iscr, $array_parametri_ricerca = NULL)
    {
        $socid = array();
        $this->iscritti = array();
        $ranking_evento = $this->ranking->getRankingEvento($this->id_gara);
        $convalidato = 0;
        if (!empty($ranking_evento))
        {
            $convalidato = $ranking_evento['convalidato'];
        }
        if ($convalidato > 0)
            return;

        foreach ($iscr as $value)
        {

            /* @var $value IscrittoIndividuale */
            $ida = $value->getAtleta();
            $ids = $value->getSocieta();

            $socid[$ids][$ida] = $ida;
            $id_categoria = $value->getCategoriaFinale();
            $atleta_gia_inserito = $this->ranking->atletaGiaInserito($ida, $this->id_gara, $id_categoria);
            if (!$atleta_gia_inserito) // se non è stato già inserito
            {
                $atleta = new AtletaRanking("tesserati", $ida);
                $atleta->carica();
                $cognome = strtolower($atleta->getCognome());
                $nome = strtolower($atleta->getNome());

                if (count($array_parametri_ricerca) == 0)
                {
                    $zero_selez = true;
                    $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                } else if (count($array_parametri_ricerca) == 1)
                {
                    if (isset($array_parametri_ricerca['nome']) && $array_parametri_ricerca['nome'] == $nome)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if (isset($array_parametri_ricerca['cognome']) && $array_parametri_ricerca['cognome'] == $cognome)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if (isset($array_parametri_ricerca['id_kat_selezionato']) && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if (isset($array_parametri_ricerca['numero_tesserato']) && $array_parametri_ricerca['numero_tesserato'] == $ida)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                } else if (count($array_parametri_ricerca) == 2)
                {
                    if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['cognome'] == $cognome)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['numero_tesserato'] == $ida)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if ($array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['numero_tesserato'] == $ida)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if ($array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    } else if ($array_parametri_ricerca['numero_tesserato'] == $ida && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                } else if (count($array_parametri_ricerca) == 3)
                {
                    //escluso categoria
                    if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['numero_tesserato'] == $ida)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                    // escluso num tesserato
                    else if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                    //escluso cognome
                    else if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['numero_tesserato'] == $ida && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                    //escluso nome
                    else if ($array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['numero_tesserato'] == $ida && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                } else if (count($array_parametri_ricerca) == 4)
                {
                    //controlla tutto
                    if ($array_parametri_ricerca['nome'] == $nome && $array_parametri_ricerca['cognome'] == $cognome && $array_parametri_ricerca['numero_tesserato'] == $ida && $array_parametri_ricerca['id_kat_selezionato'] == $id_categoria)
                    {
                        $this->iscritti[$id_categoria][$value->getPool()][] = $value;
                    }
                }
            }
        }
        foreach ($this->iscritti as $idc => $pools)
        {

            foreach (array_keys($pools) as $pool)
                usort($this->iscritti[$idc][$pool], array($this, "compareIsc"));
        }
    }

    public function allCategorie()
    {
        return $this->categorie;
    }

    public function redirect($path)
    {

        print '<META HTTP-EQUIV="refresh" CONTENT="0; URL=' . $path . '">';
    }

    public function getEventi()
    {
        return $this->array_eventi;
    }

    public function getNomeGara($id_gara)
    {
        return $this->ranking->getNomeGara($id_gara);
    }

    public function getGare()
    {

        return $this->gare;
    }

    /*
      public function AllCat()
      {
      $rank_cat = new RankingClassifica();
      $array_all_categorie = $rank_cat->getAllKat();
      $array_categorie = array();
      $kategorie = array();
      for ($i = 0; $i < count($array_all_categorie); $i++)
      {
      $id_kat = (int) $array_all_categorie[$i];

      $cat = new Categoria($id_kat);
      $array_categorie['nome'] = $cat->getNome();
      $array_categorie['id_categoria'] = $id_kat;
      $kategorie [] = $array_categorie;
      }

      if (!empty($kategorie))
      {
      $fd = fopen("categorie.csv", "w");
      for ($j = 0; $j < count($kategorie); $j++)
      {
      $nome_cat = $kategorie[$j]['nome'];
      $id_kat = $kategorie[$j]['id_categoria'];
      $str = $nome_cat . ";" . $id_kat . ";" . "\n";
      fwrite($fd, $str);
      }
      }
      }
     */
}