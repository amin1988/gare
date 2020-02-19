<?php

if (!defined("_BASEDIR_"))
    exit();

require_once("../../config.inc.php");

include_controller("admin/convalida_result_ranking");
include_controller("admin/stage_result_ranking");

include_esterni("AtletaRanking");

class GareEsterneConvalidaRisView {

    private $ctrl;
    private $fin;
    private $id_gara = 0;
    private $id_kat_elezionato = 0;
    private $nome = null;
    private $cognome = null;

    public function __construct() {
        if (isset($_GET['id'])) {
            $this->id_gara = $_GET['id'];
        }
        $processa = empty($_POST['processa']) ? NULL : $_POST['processa'];
        if (!empty($processa)) {
            $this->id_kat_elezionato = empty($_POST['id_kat']) ? NULL : $_POST['id_kat'];
            $this->nome = empty($_POST['nome']) ? NULL : $_POST['nome'];
            $this->cognome = empty($_POST['cognome']) ? NULL : $_POST['cognome'];
            $this->numero_tesserato = empty($_POST['numero_tesserato']) ? NULL : $_POST['numero_tesserato'];
        }
        $tipo_gara = "gara_esterno";
        if (isset($_GET['tipo_gara'])) {
            $tipo_gara = $_GET['tipo_gara'];
            if ($tipo_gara == "stage") {
                $this->ctrl = new StageResultCtrl($this->id_gara);
            }
        }
        if ($tipo_gara == "gara_esterno")
            $this->ctrl = new GareEsterneInsertRisCtrl($this->id_gara, $this->id_kat_elezionato, $this->nome, $this->cognome, $this->numero_tesserato);
    }
    
    public function redirectListaStage()
    {
        $path = $_SERVER["REQUEST_URI"];
        redirect("admin/ranking/gare_esterne_addrisultati.php");
         print '<META HTTP-EQUIV="refresh" CONTENT="0; URL=' . $path . '">';
    }

    public function stampa() {
        $lang = Lingua::getParole();
        ?>
        <form enctype="multipart/form-data" method="POST">

            <h4> <center>  <input  class="tDescr" name="processa" type="submit" value="<?php echo $lang['convalida'] . " tutto"; ?>"> </center></h4>
            <h4>Gara caricata: <select disabled id="gare" name="gare" style="width: 252px;">

        <?php
        foreach ($this->ctrl->getEventi() as $idg => $g) {

            $id_gara_evento = $g['id_gara'];
            if ($this->id_gara == $id_gara_evento) {
                $ng = $this->ctrl->getNomeGara($id_gara_evento);
                $nome_gara = $ng['nome'];
                echo "<option value=$id_gara_evento >$nome_gara</option>";
            } else {
                echo "<option value=0>ERRORE</option>";
            }
        }
        ?>

                </select>
            </h4>
            <h4>

                <table>
                    <tr>
                        <td> <?php echo $lang["nome_iscrizioni"]; ?> <input  type="text" name="nome"  style="width: 150px;" id="nome" value="<?php if (isset($this->nome)) echo $this->nome;
            else echo ''; ?>">    </td> <td></td><td></td><td></td>
                        <td> <?php echo $lang["cognome_iscrizioni"]; ?> <input  type="text" name="cognome"  style="width: 150px;" id="nome" value="<?php if (isset($this->cognome)) echo $this->cognome;
            else echo ''; ?>"></td> <td></td><td></td><td></td>

                        <td> Num.Tesserato <input  type="text" name="numero_tesserato"  style="width: 150px;" id="nome" value="<?php if (isset($this->numero_tesserato)) echo $this->numero_tesserato;
            else echo ''; ?>"> </td>

                    </tr>
                </table>
            </h4>
            <h4>Categoria: <select id="gare" name="id_kat" style="width: 252px;">

        <?php
        echo "<option value=0></option>";
        foreach ($this->ctrl->allCategorie() as $i_esimo => $evento) {

            $nome_evento = $evento->getNome();

            $id_kat = $i_esimo;
            $selected = " ";
            if (!empty($this->id_kat_elezionato) && $id_kat == $this->id_kat_elezionato) {
                $selected = " selected ";
            }
            echo "<option value=$id_kat  $selected>$nome_evento</option>";
            $selected = "";
        }
        ?>
                </select>

            </h4>


            <br>
            <input class="button" name="processa" type="submit" value="<?php echo "Cerca " . $lang["atleta"]; ?>">
            &nbsp; &nbsp; &nbsp;<input class="button" name="processa" type="submit" value="Elimina"> &nbsp; &nbsp; &nbsp;

            <br>

        <?php
        print '<br>';
        // print ' <form enctype="multipart/form-data" method="POST">';
        print '<table class="atleti" >';
        print '<tr class="tr">';

        // print '<th><div class="thAtleti"></div></th>';
        print '<th colspan="2"><div class="thAtleti">' . $lang["cognome_iscrizioni"] . '/' . $lang["nome_iscrizioni"] . "</div></th>";
        print '<th><div class="thAtleti" >' . ucfirst($lang["societa"]) . "</div></th>";
        print '<th><div class="thAtleti" >' . $lang["sesso_iscrizioni"] . "</div></th>";
        print '<th><div class="thAtleti">' . $lang["nascita_iscrizioni"] . "</div></th>";
        print '<th><div class="thAtleti">' . $lang["categoria"] . "</div></th>";
        print '<th><div class="thAtleti">' . $lang["classificato"] . "</div></th>";
        print '<th><div class="thAtleti">' . $lang["elimina_squadra"] . "</div></th>";

        foreach ($this->ctrl->getIscritti() as $idc => $pools) {
            foreach ($pools as $chiave => $elenco_tesserati) {
                $count = 0;
                foreach ($elenco_tesserati as $tesserato) {
                    if (($count % 2) == 0)
                        $classe = "riga1";
                    else
                        $classe = "riga2";

                    $id_tess = $tesserato->getAtleta();
                    $atleta = new AtletaRanking("tesserati", $id_tess);
                    $atleta->carica();
                    $cognome = $atleta->getCognome();
                    $nome = $atleta->getNome();
                    if (empty($nome))
                        continue;
                    if (empty($cognome))
                        continue;

                    $soc = new Societa($atleta->getSocieta());
                    $nome_societa = $soc->getNome();
                    $sesso_numerico = $atleta->getSesso();
                    $sesso = ($sesso_numerico == 2) ? "F" : "M";
                    $data_nascita = $atleta->getDataNascita();
                    $array_dati = array('id_atleta' => $id_tess, 'id_societa' => $atleta->getSocieta(), 'kategoria' => $idc);
                    $Json_array_dati = htmlspecialchars(json_encode($array_dati));

                    $kat = new Categoria($idc);
                    $nome_categoria = $kat->getNome();
                    $ranking = new Ranking();
                    $ranking_risultato = $ranking->getRankingRis($this->id_gara, $id_tess, $idc);
                    $classificato_row = 0;
                    if (!empty($this->id_gara)) {
                        $classificato_row = $ranking_risultato['classificato'];
                    }

                    print '<tr class=' . "$classe" . '>';
                    // print '<td class="riepilogo_center">' . ($count + 1) . '</td>';
                    print '<td class="riepilogo_center">' . $cognome . " " . $nome . '</td>';
                    print '<td class="riepilogo_center"> </td>';
                    print '<td class="riepilogo_center">' . $nome_societa . '</td>';
                    print '<td class="riepilogo_center">' . $sesso . '</td>';
                    print '<td class="riepilogo_center">' . $data_nascita . '</td>';
                    print '<td class="riepilogo_center">' . $nome_categoria . '</td>';
                    print '<td class="riepilogo_center">' . $classificato_row . '</td>';
                    print '<td class="riepilogo_center"> <input  type="checkbox"  name="classificato[' . $id_tess . '][' . $idc . ']"  style="width: 80px;" id="nome" value="' . $classificato_row . '"> ' . '</td>';
                    print '<input type="hidden"  name="array_dati_eventi[]" value="' . $Json_array_dati . '">';
                    print '</tr>';
                    $count++;
                }
            }
        }
        print '</table>';
        print '<br>';
        print '<input name="processa" type="submit" value="Elimina">';
        print '</form>';
    }

    public function stampaInizioCategoria($c, $pool) {
        $ancora = 'cat' . $c->getChiave();
        $nome = $c->getNome();
        if ($pool > 0) {
            $ancora .= "p$pool";
            $nome .= " - " . str_replace('<NUM>', $pool, Lingua::getParola("pool"));
        }
        echo "<a name=\"$ancora\"></a><h1>$nome</h1>\n";
    }

}
