<?php

if (!defined("_BASEDIR_"))
    exit();

require_once("../../config.inc.php");

include_controller("admin/gare_esterne_addris");

class GareEsterneAddRisView {

    private $ctrl;
    private $fin;

    public function __construct() {

        $this->ctrl = new GareEsterneAddRisCtrl();
    }

    public function stampaStage() {
        $lang = Lingua::getParole();
        print '<div id="Right" style="width:90%;">';
        print'<div class="Gare_soc_right">';
        print '<h1>' . "Stage" . '</h1>';

        foreach ($this->ctrl->getEventi() as $chiave => $evento) {
            $f = $chiave;
            $tipo_evento = $evento['tipo_evento'];
            if ( $tipo_evento <8)
                continue;
            
            $this->stampaRigaStage($evento);
        }

        print'</div>';
        print'</div>';
    }

    public function stampa() {
        $lang = Lingua::getParole();
        print '<div id="Right" style="width:90%;">';
        print'<div class="Gare_soc_right">';
        print '<h1>' . $lang['lista_gare'] . '</h1>';

        foreach ($this->ctrl->getEventi() as $chiave => $evento) {
            $f = $chiave;
            $tipo_evento = $evento['tipo_evento'];
            if ( $tipo_evento ==8  || $tipo_evento ==9 )
                continue;
            $this->stampaRigaGara($evento);
        }

        print'</div>';
        print'</div>';
    }

    function stampaRigaGara($evento) {
        global $lang;
        $convalidato = (isset($evento['convalidato']) && $evento['convalidato'] > 0) ? $evento['convalidato'] : 0;

        $id = $evento['id_gara'];
        $gara = new Gara($id);
        $data = $gara->getDataGara()->format("d/m/Y");
        $nome_gara = $gara->getNome();
        echo '<li class="hilight"><table width="100%"><tr><td>';
        echo "<span class='tDescr'>$data - $nome_gara</span></td><td class=\"liButton\">";
        if ($convalidato == 0) {
            echo "<a href=\"insert_result_ranking.php?id=$id\">$lang[inserisci_risultati]</a>";
            echo "<a href=\"convalida_result_ranking.php?id=$id\">$lang[convalida]</a>";
        }
        echo "<a href=\"riepilogo_result_ranking.php?id=$id\">$lang[gara_riepilogo]</a>";
        echo "<a  href=\"" . _PATH_ROOT_ . "dettagli.php?id=$id\">$lang[gara_dettagli]</a> ";
        echo "</td></tr></table></li>";
    }

    
       function stampaRigaStage($evento) {
        global $lang;
        $convalidato = (isset($evento['convalidato']) && $evento['convalidato'] > 0) ? $evento['convalidato'] : 0;

        $id = $evento['id_gara'];
        $gara = new Gara($id);
        $data = $gara->getDataGara()->format("d/m/Y");
        $nome_gara = $gara->getNome();
        echo '<li class="hilight"><table width="100%"><tr><td>';
        echo "<span class='tDescr'>$data - $nome_gara</span></td><td class=\"liButton\">";
        if ($convalidato == 0) {
            
            echo "<a href=\"convalida_result_ranking.php?modo=convalida&tipo_gara=stage&id=$id\">$lang[convalida]</a>";
        }
        echo "<a href=\"riepilogo_result_ranking.php?modo=vista&id=$id\">$lang[gara_riepilogo]</a>";
        echo "<a  href=\"" . _PATH_ROOT_ . "dettagli.php?id=$id\">$lang[gara_dettagli]</a> ";
        echo "</td></tr></table></li>";
    }
    

}
