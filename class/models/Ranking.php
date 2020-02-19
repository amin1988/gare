<?php

if (!defined("_BASEDIR_"))
        exit();

include_model("Modello");

class Ranking extends Modello {

        private $tipo = NULL;
        private $conn_sw_gara = null;

        public function __construct($id = NULL) {

                parent::__construct("ranking", "id", $id);
        }

        public function getConnessioneGaraSw() {
                $this->sw_gara = $GLOBALS["connsw"];
                $this->sw_gara->connetti();
                //$this->sw_gara->dbname = $database_name;
        }

        public function getConSw_gara() {
                return $this->sw_gara;
        }

        public function seEsisteDB($nome_database) {
                $sql = "SHOW DATABASES LIKE '$nome_database';";
                $res = $this->sw_gara->query($sql);
                $row = 0;
                if ($res) {
	     $row = $res->num_rows;
                }
                if ($row > 0)
	     return true;

                return false;
        }

        /*
          public function creaDatabase($database_name)
          {
          $sql = "CREATE DATABASE " . $database_name;
          $res = $this->sw_gara->query($sql);
          if ($res == TRUE)
          {

          $this->sw_gara->dbname = $database_name;
          return true;
          }
          return false;
          }
         */

        public function popolaTab_sw_gara($sql_file, $idgara) {
                $sqlScript = file($sql_file);
                // $sql_table_use = "USE " . $this->sw_gara->dbname . "  ";
                //$res = $this->sw_gara->query($sql_table_use);
                $this->createTableSw($idgara);

                $conn = $GLOBALS["connint"];
                $conn->connetti();

                foreach ($sqlScript as $line) {
	     $startWith = substr(trim($line), 0, 2);
	     $endWith = substr(trim($line), -5);

	     if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
	             continue;
	     }

	     $query = $query . $line;
	     if ($endWith == '[EOL]') {
	             $query = substr(trim($query), 0, -5);
	             $ins_names = "INSERT INTO names VALUES";
	             $len_names = strlen($ins_names);
	             $sub_stringa_names = substr($query, 0, $len_names);
	             if ($sub_stringa_names == $ins_names) {
		  $query = str_replace("INSERT INTO names VALUES", "INSERT INTO names_$idgara VALUES", $query);
		  $res = $conn->query($query);
		  if ($res == false) {
		          $ranking_err = new RankingErrorLog();
		          $ranking_err->errorLogRanking($query, basename($_SERVER['PHP_SELF']), "errore inserimento tabella names ");
		          return false;
		  }
	             }
	             $ins_ergebniseinzel = "INSERT INTO ergebniseinzel VALUES";
	             $len_ergebniseinzel = strlen($ins_ergebniseinzel);
	             $sub_stringa_ergebniseinzel = substr($query, 0, $len_ergebniseinzel);
	             if ($sub_stringa_ergebniseinzel == $ins_ergebniseinzel) {
		  $query = str_replace("INSERT INTO ergebniseinzel VALUES", "INSERT INTO ergebniseinzel_$idgara VALUES", $query);
		  $res = $conn->query($query);
		  if ($res == false) {
		          $ranking_err = new RankingErrorLog();
		          $ranking_err->errorLogRanking($query, basename($_SERVER['PHP_SELF']), "errore inserimento tabella ergebniseinzel ");
		          return false;
		  }
	             }
	     }

	     $query = '';
                }


                return $res;
        }

        public function createTableSw($idgara) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = $this->getQueryCreateNames($idgara);
                $eliminato_name = $conn->query("DROP TABLE IF EXISTS names_$idgara");
                $aggiunto_name = $conn->query($sql);

                $sql = $this->getQueryCreateErgebniseinzel($idgara);
                $eliminato_ergebniseinzel = $conn->query(" DROP TABLE IF EXISTS ergebniseinzel_$idgara");
                $aggiunto_ergebniseinzel = $conn->query($sql);
        }

        private function getQueryCreateNames($idgara) {
                $stringa_query = "
           
            CREATE TABLE names_$idgara (
                nnr int(11) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL DEFAULT '',
                geburt date NOT NULL DEFAULT '0000-00-00',
                vereinnr int(11) NOT NULL DEFAULT '3',
                geschlecht char(1) DEFAULT NULL,
                gewicht int(5) DEFAULT NULL,
                groesse int(11) DEFAULT NULL,
                sichtbar int(1) unsigned NOT NULL DEFAULT '1',
                kyu int(1) DEFAULT '0',
                dan int(1) DEFAULT '0',
                nationnr int(11) DEFAULT '0',
                stpktnr int(11) DEFAULT '0',
                nationalid varchar(30) DEFAULT NULL,
                sonstiges text,
                wkfid varchar(100) DEFAULT NULL,
                passportid varchar(50) DEFAULT NULL,
                extid int(11) DEFAULT NULL,
                exthasprivatecomment int(1) DEFAULT NULL,
                exthaspubliccomment int(1) DEFAULT NULL,
                puuid varchar(50) DEFAULT NULL,
                PRIMARY KEY (nnr)
            )               
           
        ";
                return $stringa_query;
        }

        private function getQueryCreateErgebniseinzel($idgara) {
                $stringa_query = " 
                            CREATE TABLE ergebniseinzel_$idgara (
                                    vernr int(11) NOT NULL DEFAULT '0',
                                    knr int(11) NOT NULL DEFAULT '0',
                                    nnr int(11) NOT NULL DEFAULT '0',
                                    erg int(2) NOT NULL DEFAULT '0',
                                    done int(1) DEFAULT NULL
                            ) 
                        ";
                return $stringa_query;
        }

        public function popola_ranking_eventiGare($id_gara, $tipo_gara) {
                if (empty($id_gara)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];

                $conn->connetti();
                $mr = $conn->select("ranking_eventi", "id_gara='$id_gara'", "count(*) as TOT");
                if ($mr) {
	     $row = $mr->fetch_assoc();
	     $tot = $row['TOT'];
	     if ($tot > 0) {//non aggiungere
	             return false;
	     }
	     $sql = "INSERT INTO ranking_eventi (id_gara,tipo_gara) VALUES($id_gara,'$tipo_gara') ";
	     $aggiunto = $conn->query($sql);
	     if (!$aggiunto)
	             return false;
                }
                else {
	     return false;
                }
                return true;
        }

        public function seEventoEsiste($id_gara) {
                if (empty($id_gara)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $mr = $conn->select("ranking_eventi", "id_gara='$id_gara'");
                $row = null;
                if ($mr) {
	     $row = $mr->fetch_assoc();
                }
                return (empty($row) ? false : true);
        }

        public function getRankingEvento($id_gara) {
                if (empty($id_gara)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $mr = $conn->select("ranking_eventi", "id_gara='$id_gara'");
                $row = null;
                if ($mr) {
	     $row = $mr->fetch_assoc();
                }
                return $row;
        }

        public function getRankingEventi($tipo_evento = 0) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $mr = $conn->select("ranking_eventi", "tipo_evento='$tipo_evento'");
                $array_ranking_eventi = array();
                if ($mr) {
	     while ($row = $mr->fetch_assoc()) {
	             $array_ranking_eventi[] = $row;
	     }
                }
                return $array_ranking_eventi;
        }

        public function getAllRankingEventi($convalidato = -1) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $cond = "";
                if ($convalidato == 0) {
	     $cond = " WHERE convalidato=0 ";
                }
                if ($convalidato == 1) {
	     $cond = " WHERE convalidato=1 ";
                } else if ($convalidato == -1) {
	     $cond = "";
                }

                $sql = "SELECT * FROM ranking_eventi $cond ";
                $mr = $conn->query($sql);
                $array_ranking_eventi = array();
                if ($mr) {
	     while ($row = $mr->fetch_assoc()) {
	             $array_ranking_eventi[] = $row;
	     }
                }
                return $array_ranking_eventi;
        }

        public function getRankingEventiConvalidati($convalidato = 0) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                //   $mr = $conn->select("ranking_eventi", "convalidato='$convalidato'");
                $mr = $conn->select("ranking_eventi", " tipo_gara like 'gara_esterna_o_stage' AND tipo_evento!=0 ");
                $array_ranking_eventi = array();
                if ($mr) {
	     while ($row = $mr->fetch_assoc()) {
	             $array_ranking_eventi[] = $row;
	     }
                }
                return $array_ranking_eventi;
        }

        public function deleteGaraFromRanking($id_gara) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $mr = $conn->select("ranking_eventi", "tipo_evento = 0 AND id_gara = " . $id_gara);
                $row = null;
                $eliminato = false;
                if ($mr) {
	     $row = $mr->fetch_assoc();
	     if (!empty($row)) {
	             $sql = "DELETE FROM ranking_eventi where id_gara = " . $id_gara;
	             $eliminato = $conn->query($sql);
	     }
                }
                return $eliminato;
        }

        public function criterioPunteggioRanking() {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $array_criterio_punteggio = array();
                $mr = $conn->select("ranking_criterio_punteggio");
                if ($mr) {
	     while ($row = $mr->fetch_assoc()) {
	             $array_criterio_punteggio[] = $row;
	     }
                }
                return $array_criterio_punteggio;
        }

        public function getNomeGara($id_gara) {
                $conn = $GLOBALS["connint"];

                $conn->connetti();
                $mr = $conn->select("gare", "idgara=$id_gara", 'nome');
                $row = NULL;
                if ($mr) {
	     $row = $mr->fetch_assoc();
                }
                return $row;
        }

        public static function getPuntiAtleta($idatleta, $tipogara) {
                $ida = array();
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                if ($tipogara == 1)
	     $tipogara = "1,2";
                else
	     $tipogara = "0";

                $mr = $conn->select("ranking", "idatleta='$idatleta' AND tipogara IN ($tipogara)", "SUM(punti) as TOT");
                $row = $mr->fetch_assoc();
                return $row["TOT"];
        }

        public function aggiornaRankingEventi($id_gara, $tipo_evento, $da_convalidare = false) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();

                $sql = "UPDATE ranking_eventi SET tipo_evento= " . $tipo_evento . " WHERE id_gara=$id_gara AND  tipo_evento = 0 ";
                $aggiornato = $conn->query($sql);

                if ($da_convalidare) {
	     $sql = "UPDATE ranking_eventi SET convalidato=1  WHERE id_gara=$id_gara AND  tipo_evento = $tipo_evento ";
	     $aggiornato_conv = $conn->query($sql);
                }

                return $aggiornato;
        }

        /*         * ************************ GESTIONE COMPOSIONE TABELLE DEL RANKING ******************** */

        /**
         * Se ci sono categorie da escludere (per esempio calcolo del ranking escluso i bambini) specificare id categoria
         * @param type $id_gara
         * @param type $tipo_evento
         * @param type $kat_da_escludere array
         */
        public function controllaCategoria($id_categoria) {
                if (empty($id_categoria)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = " SELECT idcategoria "
	     . "FROM categorie "
	     . " where ( idgruppo!=-1 AND idgruppo in (select idgruppo from gruppicat) ) AND  idcategoria=" . $id_categoria;
                $res = $conn->query($sql);
                $trovato = false;
                if ($res) {
	     $row = $res->fetch_assoc();
	     if (!empty($row)) {
	             $id_kat = $row['idcategoria'];
	             if ($id_kat > 0)
		  $trovato = true;
	     }
                }
                return $trovato;
        }

        public function requisiti_classificazione($categoria, $atleta, $gara) {
                $eta_min = $categoria->getEtaMin();
                $eta_max = $categoria->getEtaMax();
                $cinture = $categoria->getCinture();
                if ($cinture[0] == 6) {
	     $cin = $cinture[0];
                }
                if ($cinture[0] == 7) {
	     $cin = $cinture[0];
                } else if (count($cinture) > 1) {
	     $cin = implode("|", $cinture);
                }
                if ( !isset($cin)) // non soddisfa requisiti essenziali
                {
	     return false;
                }

                /*                 * ***** verifica se le cinture sono marroni e nere o marroni-nere **** */
                $cin_marrone = "6";
                $cin_nero = "7";
                $marroni_nere = "6|7";
                switch ($cin) {
	     case $cin_marrone:
	             return true;
	     case $cin_nero:
	             return true;
	     case $marroni_nere:
	             return true;
                }
                //preagonisti bambini fino a 14 anni (esclusi)
                $verde_blu = "4|5";

                 $array_data_gara = $gara->getDataGara();
                
                 
                $anno = $array_data_gara->getAnno();
                $mese = $array_data_gara->getMese();
                $giorno = $array_data_gara->getGiorno();
                
                $datetime1 = new DateTime(date("$anno-$mese-$giorno")); // data gara
                $data_nascita = $atleta->getDataNascita();
                $datetime2 = new DateTime(date($data_nascita));
                $diff = $datetime1->diff($datetime2); 
                $eta_atleta_alla_gara = $diff->format('%y');
                
                
                if ($eta_atleta_alla_gara < 14) {
	     if ($cin == $verde_blu) {
	             return true;
	     }
                }


                return false;
        }

        public function componiTabellaRanking($id_gara, $tipo_evento, $kat_da_escludere = NULL) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                // $conn_sw_gara = $this->getConDbGara($id_gara);
                //$where = " WHERE classifica.erg <=5 ";
                $where = "  ";
                if (!empty($kat_da_escludere)) { // se ci sono categorie da escludere
	     $lista_kat = implode(",", $kat_da_escludere);
	     //$where .= "  AND classifica.knr not in ($lista_kat) ";
	     $where .= " WHERE  classifica.knr not in ($lista_kat) ";
                }

                $sql_select = "SELECT $id_gara as 'id_gara', classifica.knr as kategoria, $tipo_evento as 'tipo_evento', atleti.extid as id_atleta, classifica.erg as classificato, atleti.vereinnr as id_societa " .
	     "FROM ergebniseinzel_$id_gara as classifica INNER JOIN names_$id_gara as atleti ON classifica.nnr = atleti.nnr " .
	     $where;


                $gara = new Gara($id_gara);
                $res = $conn->query($sql_select);
                $res_insert = false;
                if ($res) {

	     while ($row = $res->fetch_assoc()) {
	             if (empty($row)) {
		  break;
	             }
	             $kategoria = $row['kategoria'];
	             $cat_esiste = $this->controllaCategoria($kategoria);
	             if ($cat_esiste == false) {
		  $log_err = new RankingErrorLog();
		  $log_err->errorLogRanking("classe:Ranking.php; metodo componiTabellaRanking categoriaAtleta:$kategoria; idgara:$id_gara - Controllare gli idcategorie nel file csv", basename($_SERVER['PHP_SELF']), "Idcategoria inserito nel sw gara non presente nella tabella categoria di fiamGare");
		  continue;
	             }
	             $categoria = new Categoria($kategoria);
	             //$cinture = $categoria->getCinture();

	             $id_atleta = $row['id_atleta'];
	             $atleta = new AtletaRanking("tesserati", $id_atleta);
	             $atleta->carica();
	             $cognome = $atleta->getCognome();
	             $nome = $atleta->getNome();
	             
	             if (empty($nome) || empty($cognome)) {
		  $ranking_err = new RankingErrorLog();
		  $ranking_err->errorLogRanking("Errore caricamento atleta, verificare esistenza - tipo:", basename($_SERVER['PHP_SELF']), "Controllare idatleta nei tesseramenti $id_atleta ");
		  continue;
	             }

	             if (empty($id_atleta)) {
		  $log_err = new RankingErrorLog();
		  $log_err->errorLogRanking("classe:Ranking.php; metodo componiTabellaRanking categoriaAtleta:$kategoria; idgara:$id_gara - Controllare id atleta ($id_atleta) nel file csv", basename($_SERVER['PHP_SELF']), "idatleta inserito nel sw gara risulta un valore zero.Verificare il file .sql");
		  continue;
	             }

	             $requisito_ok = $this->requisiti_classificazione($categoria, $atleta, $gara);
	             if (!$requisito_ok) { // se non soddisfa i requisiti per la visualizzazione classifica
		  continue; // passa al record successivo
	             }


	             $classificato = $row['classificato'];
	             $id_societa = $atleta->getSocieta();
	             $sql_insert = " INSERT INTO ranking_risultati"
		  . " (id_gara,kategoria,tipo_evento,id_societa,id_atleta,classificato) "
		  . "   VALUES($id_gara,$kategoria,$tipo_evento,$id_societa,$id_atleta,$classificato) ";

	             // print $sql_insert;
	             $res_insert = $conn->query($sql_insert);
	     }
                }
                return $res_insert;
        }

        public function getConDbGara($id_gara) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();

                $conn_sw_gara = new Connessione();
                $conn_sw_gara->host = $conn->host;
                $conn_sw_gara->user = $conn->user;
                $conn_sw_gara->psw = $this->conn_sw_gara->psw;
                $conn_sw_gara->dbname = "gara_" . $id_gara;
                $conn_sw_gara->connetti();

                $conn_sw_gara->port = ini_get("mysqli.default_port");

                return $conn_sw_gara;
        }

        /**
         * Elimina il database (che viene caricato temporanemente)
         * @param type $id_gara
         * @return boolean
         */
        public function eliminaDBSwgare($id_gara, $nome_gara) {
                if (empty($id_gara)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = "DROP table $nome_gara";
                $res_drop = $conn->query($sql);
                if ($res_drop == false) {
	     $ranking_err = new RankingErrorLog();
	     $ranking_err->errorLogRanking($sql, basename($_SERVER['PHP_SELF']), "errore delete tabella " . $nome_gara);
                }
                return $res_drop;
        }

        public function getRankingRis($id_gara, $id_atleta, $id_categoria) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = "SELECT * FROM ranking_risultati "
	     . " WHERE id_gara =" . $id_gara . " AND " . "id_atleta=" . $id_atleta . " AND " . " kategoria = " . $id_categoria;

                $res = $conn->query($sql);
                $row = NULL;
                if ($res) {
	     $row = $res->fetch_assoc();
                }
                return $row;
        }

        /**
         * viene verificato se l'atleta già esiste nella tabella ranking_risultati
         * @param  $id_atleta
         * @param  $id_gara
         * @param  $id_categoria
         * @return boolean true se già esiste altrimenti false
         */
        public function atletaGiaInserito($id_atleta, $id_gara, $id_categoria, $flag_gara_chiusa = false) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $cond_tipo_evento = "";
                if ($flag_gara_chiusa) {
	     $cond_tipo_evento = " r.classificato > 0 AND evento.convalidato=0 AND ";
                } else {
	     $cond_tipo_evento = " r.classificato = 0 AND evento.convalidato=0  AND ";
                }

                $sql = "SELECT COUNT(*) AS tot_atleti "
	     . "FROM ranking_risultati as r INNER JOIN ranking_eventi AS evento ON r.id_gara = evento.id_gara "
	     . "WHERE " . $cond_tipo_evento . " r.id_atleta = " . $id_atleta . " AND " . " r.id_gara= " . $id_gara . " AND " . " r.kategoria= " . $id_categoria;
                $res = $conn->query($sql);

                if (!$res)
	     return false;
                else if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             if (empty($row)) {
		  return false;
	             }
	             $count_atleti = $row['tot_atleti'];
	             if ($count_atleti > 0) {
		  return true;
	             }
	     }
                }
                return false;
        }

        public function riepiloGaraEsternoPresenze($id_atleta, $id_gara, $id_categoria) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = "SELECT COUNT(*) AS tot_atleti  "
	     . "FROM ranking_risultati as r INNER JOIN ranking_eventi AS evento ON r.id_gara = evento.id_gara "
	     . "WHERE evento.convalidato=1 AND  r.id_atleta = " . $id_atleta . " AND " . " r.id_gara= " . $id_gara . " AND " . " r.kategoria= " . $id_categoria;
                $res = $conn->query($sql);
                if (!$res)
	     return false;
                else if ($res) {
	     while ($row = $res->fetch_assoc()) {
	             if (empty($row)) {
		  return false;
	             }
	             $count_atleti = $row['tot_atleti'];
	             if ($count_atleti > 0) {
		  return true;
	             }
	     }
                }
                return false;
        }

        /**
         * inserisce i dati nella tabella ranking_risultati
         * @param type $tabella (nome tabella)
         * @param type $array_lista_campi (lista di campi di insert)
         * @param type $array_lista_valori (lista valori dei campi)
         * @param type $id_gara
         * @return type
         */
        public function insertRankingEventiEsterni($tabella, $array_lista_campi, $array_lista_valori, $id_gara) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $campi = implode(",", $array_lista_campi);
                $valori = implode(",", $array_lista_valori);
                $sql = "INSERT INTO $tabella  ($campi) VALUES($valori) ";
                $res_insert = $conn->query($sql);
                return $res_insert;
        }

        /**
         * elimina un record datta tabaella ranking_risultati.
         * viene chiamato qualora ci si accorge di aver sbagliato l'inserimento di un risultato delle gare esterne
         * @param type nome tabella
         * @param type condizione di filtro
         * @return boolena
         */
        public function eliminaRankingEventiEsterni($tabella, $where_condition) {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = "DELETE FROM  $tabella  $where_condition ";
                $res_insert = $conn->query($sql);
                return $res_insert;
        }

        /**
         * Restituisce l'ultimo record inserito.
         * @return type
         */
        public function getLastIdInsert() {
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $last_id = $conn->lastId();
                return $last_id;
        }

        /**
         * convalida i record della tabella ranking_risultati, impostando il flag di tipo evento.
         * Al termine della procedura non sarà più possibile riaprire l'evento per poter aggiungere altri risultati.
         * @param type $id_gara se = 0 error metodo chiamante.
         * @return boolean true se è stato aggiornato, altrimenti errore exec
         */
        public function convalidaRisEsterni($id_gara = 0) {
                if (empty($id_gara)) {
	     return false;
                }
                $conn = $GLOBALS["connint"];
                $conn->connetti();
                $sql = " UPDATE ranking_eventi SET convalidato=1 "
	     . " WHERE id_gara =  " . $id_gara;
                $res_insert = $conn->query($sql);
                return $res_insert;
        }

}