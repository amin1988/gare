<?php

require_once("../../config.inc.php");
include_controller("admin/classifica_result_ranking");
//include_esterni("AtletaRanking");

include_model("Categoria", "Gara", "Ranking", "IscrittoIndividuale", "RankingErrorLog", "RankingClassifica");


$id_atleta = $_GET['id_atleta'];
$kategoria = $_GET['kategoria'];
$nome = $_GET['nome'];
$cognome = $_GET['cognome'];


$rank_classifica = new RankingClassifica();
$dati_classifica_tess = $rank_classifica->getPunteggioAtleta($kategoria, $id_atleta);



if (!empty($dati_classifica_tess)) {
        print '<table border="1">';
         print '<tr>';
          print '<td> Nome </td>';
          print '<td> Cognome </td>';
          print '<td> Gara </td>';
          print '<td> Punteggio Ranking (singola gara) </td>';
          print '<td> Anno </td>';
          print '</tr>';

        for ($i = 0; $i < count($dati_classifica_tess); $i++) {

                $gara_classificato = $dati_classifica_tess[$i];
                $punteggio = $gara_classificato['punteggio_ranking'];
                $anno = $gara_classificato['anno'];
                $id_gara = $gara_classificato['id_gara'];
                $gara = new Gara($id_gara);
                $nome_gara = $gara->getNome();

                print '<tr>';
                print '<td>' . $nome . '</td>';
                print '<td>' . $cognome . '</td>';
                print '<td>' . $nome_gara . '</td>';
                print '<td>' . $punteggio . '</td>';
                print '<td>' . $anno . '</td>';


                print '</tr>';
        }
        print '</table>';
}