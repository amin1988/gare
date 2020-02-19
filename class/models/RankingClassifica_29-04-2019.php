<?php

if (!defined("_BASEDIR_"))
        exit();

include_model("Modello");

class RankingClassifica extends Modello {

        private $criterio_punteggio = NULL;
        private $punteggio_atleta = NULL;
        private $classifica_ranking = NULL;
         private $gare_classifica = null;

        public function __construct($id = NULL) {

                parent::__construct("ranking_risultati", "id", $id);
                $this->conn = $GLOBALS["connint"];
                $this->conn->connetti();
                $this->gareCaricateInClassificaRanking();
        }
        
       
        public function gareCaricateInClassificaRanking() {
                $sql = "SELECT distinct id_gara "
	     . "FROM ranking_classifica ";
	    
                $res = $this->conn->query($sql);
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $this->gare_classifica[] = $row['id_gara'];
		  
	     }
                }
                return $this->gare_classifica;
        }

        public function setTipologiaGara($tipo_evento = NULL) {
                $this->getCriterioPunteggio($tipo_evento);
        }

        public function getCriterioPunteggio($tipo_evento) {
                $where = "";
                if (!empty($tipo_evento)) {
	     $where = " WHERE id=  " . $tipo_evento;
                }
                $sql = "SELECT * FROM ranking_criterio_punteggio " . $where;
                $res = $this->conn->query($sql);
                if ($res) {
	     if (empty($where))
	             $this->criterio_punteggio = $res->fetch_all(MYSQLI_ASSOC);
	     else {
	             $this->criterio_punteggio = $res->fetch_assoc();
	     }
                }
        }

        public function getAllIdCategoriaInRis() {
                $sql = " SELECT DISTINCT kategoria FROM ranking_risultati";
                $res = $this->conn->query($sql);
                $array_id_kar = array();
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $array_id_kar[] = $row['kategoria'];
	     }
                }
                return $array_id_kar;
        }

        public function getAllSocieta() {
                $sql = "SELECT idsocieta, nomebreve FROM societa order by nomebreve";
                $conn = $GLOBALS["connest"];
                $conn->connetti();
                //$res = $this->conn->query($sql);
                $res = $conn->query($sql);
                $array_categorie = array();
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $array_categorie[] = $row;
	     }
                }
                return $array_categorie;
        }

        public function getSocietaRegola() {
                $conn = $GLOBALS["connest"];
                $conn->connetti();
                $sql = "SELECT * "
	     . " from pagamenti_correnti "
	     . " where DATEDIFF(scadenza, CURDATE()) > 0";
        }

        /*         * *****************************  ALGORITMO CALCOLO RANKING ********************* */

        public function getClassificaRanking($dati_parametri_ricerca) {
                $sql = "SELECT distinct kategoria FROM ranking_classifica";
                $res = $this->conn->query($sql);
                $array_categorie = array();
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $categoria = $row['kategoria'];
	             $array_risultati_cat = $this->getRowClassificaRanking($categoria, $dati_parametri_ricerca);
	             $this->classifica_ranking[$categoria] = $array_risultati_cat;
	     }
                }
                return $this->classifica_ranking;
        }

        public function getPunteggioAtleta($categoria, $id_atleta) {
                $query = "SELECT * "
	     . " FROM ranking_classifica "
	     . "   WHERE id_atleta=$id_atleta AND kategoria = " . $categoria . ""
	     . " order by punteggio_ranking desc ";
                $ranking_classifica = array();
                $result = $this->conn->query($query);
                if ($result) {
	     while ($row = $result->fetch_assoc()) {
	             $ranking_classifica [] = $row;
	     }
                }
                return $ranking_classifica;
        }

        private function getRowClassificaRanking($categoria, $dati_parametri_ricerca) {
                // $query = "SELECT * FROM ranking_classifica WHERE kategoria = " . $categoria . " order by punteggio_ranking desc LIMIT 5";
                $query = "SELECT id_atleta,kategoria,anno,sum(punteggio_ranking) as punteggio_ranking,id_gara "
	     . " FROM ranking_classifica "
	     . "   WHERE kategoria = " . $categoria . ""
	     . " GROUP BY id_atleta"
	     . " order by punteggio_ranking desc ";

                $result = $this->conn->query($query);
                $array_risultati_cat = array();

                if ($categoria == 200426) {
	     $t = "d";
                }
                if ($result) {
	     if (empty($dati_parametri_ricerca['anno'])) {
	             $dati_parametri_ricerca['anno'] = date('Y');
	     }

	     while ($row = $result->fetch_assoc()) {
	             $id_atleta = $row['id_atleta'];
	             $atleta = new AtletaRanking("tesserati", $id_atleta);
	             $atleta->carica();
	             $cognome = strtolower($atleta->getCognome());
	             $nome = strtolower($atleta->getNome());
	             $sesso_atleta = $atleta->getSesso();
	             $id_societa = $atleta->getSocieta();

	             $id_kategoria = $row['kategoria'];
	             $anno = $row['anno'];

	             /*	              * **** ricavo dati pos ** */
	             $carica_atleta = false;
	             if (isset($dati_parametri_ricerca['nome'])) {
		  $nome_cercato = trim(strtolower($dati_parametri_ricerca['nome']));
		  if ($nome_cercato == $nome) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }
	             if (isset($dati_parametri_ricerca['cognome'])) {
		  $cognome_cercato = trim(strtolower($dati_parametri_ricerca['cognome']));
		  if ($cognome_cercato == $cognome) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }
	             if (isset($dati_parametri_ricerca['sesso'])) {

		  $sesso_cercato = $dati_parametri_ricerca['sesso'];
		  if ($sesso_cercato == $sesso_atleta) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }
	             if (isset($dati_parametri_ricerca['id_soc'])) {
		  $id_soc_cercato = $dati_parametri_ricerca['id_soc'];
		  if ($id_soc_cercato == $id_societa) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }
	             if (isset($dati_parametri_ricerca['id_kat'])) {
		  $id_cat_cercato = $dati_parametri_ricerca['id_kat'];
		  if ($id_cat_cercato == $id_kategoria) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }
	             if (isset($dati_parametri_ricerca['anno'])) {
		  $anno_cercato = $dati_parametri_ricerca['anno'];
		  if ($anno_cercato = $anno) {
		          $carica_atleta = true;
		  } else {
		          continue;
		  }
	             }

	             if ($carica_atleta) {
		  $anno_corrente = date('Y');
		  $anno_precendente = $anno_corrente - 1;
		  if ($anno_precendente == $anno) {
		          $row['punteggio_ranking'] = $row['punteggio_ranking'] / 2;
		  }
		  $array_atleta_somma_classifica_gare[] += $row['punteggio_ranking'];
		  $array_risultati_cat[] = $row;
	             }
	     }
                }
                return $array_risultati_cat;
        }
        
        private function controllaInRankingClassifica($id_gara)
        {
                if (in_array($id_gara, $this->gare_classifica))
                {
	     return true;
                }
                return false;
        }

        public function componiClassificaRanking($dati_post) {
                /*
                  //svuoto la tabella ranking_classifica
                  $sql = " TRUNCATE  ranking_classifica";
                  $svuotato = $this->conn->query($sql);
                 */

                $anno_selezionato = $dati_post['anno'];
                //cerco le gare dell'anno selezionato
                $sql = " SELECT distinct r.id_gara, YEAR(g.data) AS anno_gara, r.tipo_evento 
            FROM ranking_risultati as r INNER JOIN gare AS g ON r.id_gara = g.idgara 
             WHERE YEAR(g.data) = " . $anno_selezionato;

                $res = $this->conn->query($sql);
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $id_gara = $row['id_gara'];
	             $trovato = $this->controllaInRankingClassifica($id_gara);
	             if ( $trovato)
	             {
		  continue;
	             }
	             $tipologia_evento = $row['tipo_evento'];
	             $this->setTipologiaGara($tipologia_evento);
	             $risultati_gara = $this->getAllRisultatiGara($id_gara);
	             $this->calcola_punteggio($risultati_gara);
	             $this->insertResult($anno_selezionato, $id_gara);
	     }
                }
                //prendo i partecipanti dello stage
                $query = " SELECT distinct s.id_gara, YEAR(g.data) AS anno_gara
            FROM partecipanti_stage as s INNER JOIN gare AS g ON s.id_gara = g.idgara 
             WHERE  s.convalidato = 1 AND YEAR(g.data) = " . $anno_selezionato;

                $res = $this->conn->query($query);
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $id_gara = $row['id_gara'];
	             $gara = new gara($id_gara);
	             $tipologia_evento = $gara->getTipoGara();
	             $this->setTipologiaGara($tipologia_evento);
	             $risultati_gara = $this->getAllRisultatiGara($id_gara);
	             $this->calcola_punteggio($risultati_gara, $id_gara);
	             $this->insertResult($anno_selezionato, $id_gara);
	     }
                }
        }

        public function insertResult($anno_selezionato, $id_gara = 0) {
                if (empty($this->punteggio_atleta)) {
	     return false;
                }

                foreach ($this->punteggio_atleta as $id_atleta => $kat_res) {
	     $id_atleta = $id_atleta;
	     foreach ($kat_res as $kat => $point) {
	             $kategoria = $kat;
	             $punteggio = $point;
	             $sql = " INSERT INTO ranking_classifica (id_atleta,kategoria,anno,punteggio_ranking,id_gara) VALUES($id_atleta,$kategoria,$anno_selezionato,$punteggio,$id_gara) ";
	             $res = $this->conn->query($sql);
	     }
                }
        }

        public function calcola_punteggio($risultati_gara) {
                if (!empty($risultati_gara)) {
	     foreach ($risultati_gara as $id_kat => $array_cat) {
	             foreach ($array_cat as $k => $row) {
		  $id_atleta = $row['id_atleta'];
		  $kategoria = $id_kat;
		  $classificato = (int) $row['classificato'];
		  $id_gara = $row['id_gara'];
		  $tipo_evento = $row['tipo_evento'];
		  $punteggio_rank_per_gara = 0;

		  switch ($classificato) {
		          case 1:
		                  $punteggio_rank_per_gara = $this->criterio_punteggio['primo'];
		                  break;
		          case 2:
		                  $punteggio_rank_per_gara = $this->criterio_punteggio['secondo'];
		                  break;

		          case 3:
		                  $punteggio_rank_per_gara = $this->criterio_punteggio['terzo'];
		                  break;

		          case 4:
		                  $punteggio_rank_per_gara = $this->criterio_punteggio['quarto'];
		                  break;

		          case 5:
		                  $punteggio_rank_per_gara = $this->criterio_punteggio['quinto'];
		                  break;
		  }
		  $punteggio_rank_per_gara +=$this->criterio_punteggio['partecipazione']; //punteggio partecipazione
		  switch ($this->criterio_punteggio['id']) {
		          case 8:
		                  $punteggio_rank_per_gara +=$this->criterio_punteggio['raduno'];
		                  break;
		          case 9:
		                  $punteggio_rank_per_gara +=$this->criterio_punteggio['cpr'];
		                  break;
		  }


		  if (!isset($this->punteggio_atleta[$id_atleta][$kategoria])) {
		          $this->punteggio_atleta[$id_atleta][$kategoria] = $punteggio_rank_per_gara;
		  } else {

		          $this->punteggio_atleta[$id_atleta][$kategoria] += $punteggio_rank_per_gara;
		  }
	             }
	     }
                }
        }

        public function getCalcolaPunteggioStage($tipo_evento) {
                if ($tipo_evento == 8) {
	     $punteggio = $this->criterio_punteggio['raduno'];
                } else if ($tipo_evento == 9) {
	     $punteggio = $this->criterio_punteggio['cpr'];
                }
                $punteggio_stage = $this->getCriterioPunteggio($tipo_evento);
                $this->punteggio_atleta[$id_atleta]['stage'] = $punteggio;
        }

        public function getAllRisultatiGara($id_gara) {
                $sql = "SELECT * "
	     . " FROM ranking_risultati "
	     . " WHERE id_gara = " . $id_gara . " AND classificato<6  ";
                $res = $this->conn->query($sql);
                $tutti_record_gara = array();
                $array_classifica_cat = array();
                if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             $id_kat = $row['kategoria'];
	             $id_atleta = $row['id_atleta'];
	             $array_classifica_cat[$id_kat][$id_atleta] = $row;
	     }
                }
                return $array_classifica_cat;
        }

        /**
         * metood che viene estratto la lista dei risultati in base all'atleta e kategoria passato per paramentro
         */
        public function getAtletaRisultato($id_atleta, $kat) {
                $sql = "SELECT * "
	     . " FROM ranking_risultati "
	     . " WHERE id_gara = " . $id_gara . " AND classificato<6  ";
        }

        /*         * ******************** STAGE ****************** */

        public function convalidaStageRanking($id_gara) {
                $sql = "UPDATE ranking_eventi SET convalidato= " . 1 . " WHERE id_gara=$id_gara AND  (tipo_evento = 8 OR tipo_evento = 9 ) ";
                $res = $this->conn->query($sql);
        }

        /*
          public function getAllKat()
          {
          $sql = " SELECT idcategoria "
          . "FROM categorie "
          . " where idgruppo!=-1 AND idgruppo in (select idgruppo from gruppicat)  ";
          $res = $this->conn->query($sql);
          $array_id_kat = array();
          if ($res)
          {
          while ($row = $res->fetch_assoc())
          {
          $array_id_kar[] = $row['idcategoria'];
          }
          }
          return $array_id_kar;
          }
         */
}