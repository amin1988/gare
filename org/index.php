<?php
//STAMPA

session_start();



require_once("../config.inc.php");

include_controller("listagare_backend");

include_view("Template");

include_menu();

$lang = Lingua::getParole();

/**

 * @param Gara $gara

 */
function stampaRigaGara($gara, $desc, $ctrl)
{

    global $lang;



    $id = $gara->getChiave();

    $data = $gara->getDataGara()->format("d/m/Y");



    if ($gara->isIndividuale())
    {

        if ($gara->isSquadre())
        {

            $js = "riepilogoDoppio($id)";
        } else
        {

            $js = "riepilogoSingolo($id,true)";
        }
    } else
    {

        $js = "riepilogoSingolo($id,false)";
    }



    $id_u = Utente::getIdAccesso();

    $org = Organizzatore::crea($id_u);



    $zone_g = $gara->getZone();

    $zone_o = $org->getZone();
    
    $ut = Organizzatore::crea();
    $tipo_utente = $ut->getTipo();
    $nome = $ut->getNome();
    $fed_esterno = false;
    if ( $tipo_utente ==2 )
    {
        if  ($nome == "fki")
        {
            $fed_esterno = true;
        }
        if  ($nome == "wuka")
        {
            $fed_esterno = true;
        }
        if  ($nome == "tka")
        {
            $fed_esterno = true;
        }
    }
    



    echo '<li class="hilight"><table width="100%"><tr><td>';

    echo "<span class='tDescr'>$data - $desc</span></td><td class=\"liButton\">";

    if($fed_esterno == false)
    {
    echo " <a href=\"javascript:$js\">$lang[gara_riepilogo]</a> ";

    echo " <a href=\"modifica.php?id=$id\">$lang[modifica_gara]</a> ";

    if ($ctrl->checkZone($zone_g, $zone_o) && $gara->getDataGara()->futura())//se l'organizzatore e la gara hanno zone in comune e gara � nel futuro allora si possono fare le convocazioni
        echo "<a href=\"convocazioni.php?id=$id\">Convocazioni</a>";

    echo "<a href=\"" . _PATH_ROOT_ . "dettagli.php?id=$id\">$lang[gara_dettagli]</a>";
    }
    else{
        //federazione esterna
        
        echo " <a href=\"riepilogo_soc.php?id=$id\">Riepilogo Società</a> ";
    }
    echo "</td></tr></table></li>";
}

function stampaRigaStage($gara, $desc, $ctrl)
{
    global $lang;

    $id = $gara->getChiave();

    $data = $gara->getDataGara()->format("d/m/Y");

    $id_u = Utente::getIdAccesso();

    $org = Organizzatore::crea($id_u);

    echo '<li class="hilight"><table width="100%"><tr><td>';

    echo "<span class='tDescr'>$data - $desc</span></td><td class=\"liButton\">";

    echo " <a href=\"convalida_presenze.php?id=$id\">$lang[convalida_presenze]</a> ";

    echo "<a href=\""  . "lista_presenze_stage.php?id=$id\">$lang[elenco_partecipanti]</a>";

    echo "</td></tr></table></li>";
}

$ctrl = new ListaGareBackend(false);

//TODO eliminare $templ = Template::titolo($lang["lista_gare"]);

$templ = new Template();

$templ->includeJs("popup");



$templ->stampaTagHead(false);
?>

<script type="text/javascript">

    function riepilogoSingolo(id, indiv) {

        if (indiv)
            url = 'riepilogo.php?id=' + id;

        else
            url = 'riepilogosq.php?id=' + id;

        showList('', ['<?php echo $lang["riepilogo_societa"] ?>',
            '<?php echo $lang["riepilogo_categorie"] ?>',
            '<?php echo $lang["statistiche_titolo"] ?>'],
                ['riepilogo_soc.php?id=' + id, url, 'stat.php?id=' + id]);

    }

    function riepilogoDoppio(id) {

        showList('', ['<?php echo $lang["riepilogo_societa"] ?>',
            '<?php echo $lang["riepilogo_individuali"] ?>',
            '<?php echo $lang["riepilogo_squadre"] ?>',
            '<?php echo $lang["statistiche_titolo"] ?>'],
                ['riepilogo_soc.php?id=' + id, 'riepilogo.php?id=' + id,
                    'riepilogosq.php?id=' + id, 'stat.php?id=' + id]);

    }

</script>

</head>

<?php
$templ->apriBody();
?>



<div class="pulsante tr" style="text-align:center">

  <a href="nuova.php" class='pulsante_noInput'><?php echo $lang["nuova_gara"]; ?></a><br>

</div>

<br><br>



<div id="Right" style="width:90%;">



  <div class='Gare_soc_right'>

<?php
$chiuse = $ctrl->getGareChiuse();

if (count($chiuse) > 0)
{

    echo "<h1>$lang[iscrizioni_chiuse]</h1>";

    foreach ($ctrl->getGareChiuse() as $gara)
    {

        stampaRigaGara($gara, $gara->getNome(), $ctrl);
    }
}



$attive = $ctrl->getGareAttive();

if (count($attive) > 0)
{

    echo "<h1>$lang[gare_attive]</h1>";

    $chlow = strtolower($lang["chiusura_iscrizioni"]);

    foreach ($ctrl->getGareAttive() as $gara)
    {

        $c = $gara->getChiusura()->format("d/m");
        $id_gara_stage = $gara->getChiave();
        $gara_stage = new Gara($id_gara_stage);
        $tipo_gara = $gara_stage->getTipoGara();
        print $tipo_gara;
        if ($tipo_gara == "gara")
            stampaRigaGara($gara, $gara->getNome() . " ($chlow $c)", $ctrl);
        else
        {
            stampaRigaStage($gara, $gara->getNome() . " ($chlow $c)", $ctrl);
        }
    }
}
?>

  </div>

</div>

<div class="pulsante tr" style="text-align:center">

  <a href="<?php echo _PATH_ROOT_; ?>storico.php" class="pulsante_noInput"><?php echo $lang["storico"]; ?></a>

</div>



    <?php
    $templ->chiudiBody();
    ?>