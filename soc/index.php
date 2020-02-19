<?php

session_start();



/**

 * @param Gara $gara

 */

function urlIscrivi($gara) {

	if ($gara->isIndividuale()) {

		if ($gara->isSquadre()) 

			$pag = "scegli";

		else

			$pag = "iscrivi";

	} else $pag = "iscrivisq";

	return _PATH_ROOT_."soc/$pag.php?id=".$gara->getChiave();

}
/*
 * link Iscrivi l'atleta ad uno stage
 */

function urlIscriviStage($stage)
{
    $pag = "iscrivi_stage";
    return _PATH_ROOT_ . "soc/$pag.php?id=" . $stage->getChiave();
}


function urlRiepilogo($gara) {

	global $ctrl;

	$idg = $gara->getChiave();

	if ($ctrl->getTipiPartecipazione($idg) == 2)

		$pag = "riepilogosq";

	else 

		$pag = "riepilogo";

	return _PATH_ROOT_."soc/$pag.php?id=$idg";

}



/**

 * @param Gara $gara

 * @return string

 */

function urlRiepilogoCompleto($gara) {

	$idg = $gara->getChiave();

	if ($gara->isIndividuale())

		$pag = "riepilogo";

	else

		$pag = "riepilogosq";

	return _PATH_ROOT_."soc/{$pag}_completo.php?id=$idg";

}



require_once("../config.inc.php");

include_controller("soc/listagare_soc");

include_view("Template");

$lang = Lingua::getParole();



$ctrl = new ListaGareSoc();

$templ = Template::titolo($lang["lista_gare"]);

$templ->setBodyDiv(false);



$templ->stampaTagHead();

$templ->apriBody();

?>



<div id="Left" style="width:50%;float:left;left:2px;">

<h1><?php echo $lang["gare_part"] ?></h1>









<?php

$list = $ctrl->getGarePartecipate();

if (count($list) == 0) {

	echo "<div class=\"Gare_soc_right\"><li><span class='tDescr'>$lang[no_gare]</span></li></div>";

} else {

	$count = count($list);

	foreach ($list as $gara) {

		/* @var $gara Gara */

		

		$wkc = Gara::fromId($gara->getChiave())->getWkc();

		

		if(_WKC_MODE_)

		{

			if($wkc == 0)

			{

				$count--;

				continue;

			}

		}

		else

		{

			if($wkc == 1)

			{

				$count--;

				continue;

			}

		}

		

		$id = $gara->getChiave();

		$desc = $gara->getNome();

			

		echo "<div id='Gare'>"; //TODO convertire id in class

		

		echo '<img src="'._PATH_ROOT_.$gara->getLocandina();

		echo '" style="'.$ctrl->getLocandinaSize($gara).':100px" class="locandina" >';

			//echo "<li>";

		//echo "<div style='float:left'>";

		echo "<h1>$desc</h1>";

		//echo "</div>";

		//echo "<div style=\"float:right\">";

		echo "<ul class='Gare_soc'>";

		if (!$gara->iscrizioniChiuse()) {

			echo "<a href=\"".urlIscrivi($gara)."\">$lang[modifica_iscrizioni]</a>";

		} else {

			echo "<a href=\"".urlRiepilogoCompleto($gara)."\">$lang[elenco_partecipanti]</a>";

		}

		echo " <a href=\"".urlRiepilogo($gara)."\">$lang[gara_riepilogo]</a> ";

		echo "<a href=\""._PATH_ROOT_."dettagli.php?id=$id\">$lang[gara_dettagli]</a>";

		//echo "</li>";

		echo "</ul>";

			echo "<div style='clear:both'></div>";

		

		echo "</div>";

		echo "<div style='clear:both'></div>";

		//echo "</div>";

	}

	if ($count == 0)

		echo "<div class=\"Gare_soc_right\"><li><span class='tDescr'>$lang[no_gare]</span></li></div>";

}

?>



<div class="pulsante tr" style="text-align:center">

<a href="<?php echo _PATH_ROOT_; ?>storico.php" class="pulsante_noInput"><?php echo $lang["storico"]; ?></a>

</div>

<br>
<br>
<br>

<div  style="text-align:center">

<a href="<?php echo _PATH_ROOT_."admin/ranking/"; ?>classifica_result_ranking.php" class="pulsante_noInput"><?php echo "Classifica Ranking"; ?></a>

</div>
<br>

</div>

<div id="Right" style="width:48%;float:left;left:5px;">

<div class='Gare_soc_right'>

<h1><?php echo $lang["gare_attive"]; ?></h1>

<?php

//echo "<a href=\"#\"><img src=\""._PATH_ROOT_."img/icone/printmgr.png \" style='position:absolute;right:3px;top:1px;border:none' width='30px'></a>";



$list = $ctrl->getGareAttive();

if (count($list) == 0)

	echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";

else {

	$count = count($list);

	foreach ($list as $gara) {

		$wkc = Gara::fromId($gara->getChiave())->getWkc();

		if(_WKC_MODE_)

		{

			if($wkc == 0)

			{

				$count--;

				continue;

			}

		}

		else

		{

			if($wkc == 1)

			{

				$count--;

				continue;

			}

		}

		$tipo_evento = Gara::fromId($gara->getChiave())->getTipoGara();
                if ( $tipo_evento != "gara")
                    continue;
                

		$id = $gara->getChiave();

		$desc = $gara->getNome();

		$locandina= $gara->getLocandina();

		echo '<li><table width="100%"><tr><td>';

		

		//echo "<li>";

		

		//echo "</li>";

		echo "<span class='tDescr'>$desc</span></td><td class=\"liButton\">";

		echo "<a href=\"".urlIscrivi($gara)."\">$lang[gara_iscrizioni]</a>";

		echo "<a  href=\""._PATH_ROOT_."dettagli.php?id=$id\">$lang[gara_dettagli]</a>";

		//echo "</li>";

		echo "</td></tr></table></li>";

	}

	if($count == 0)

		echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";

}

?>



<h1><?php echo $lang["iscrizioni_chiuse"]; ?></h1>

<?php

//echo "<a href=\"#\"><img src=\""._PATH_ROOT_."img/icone/printmgr.png \" style='position:absolute;right:3px;top:1px;border:none' width='30px'></a>";



$list = $ctrl->getGareChiuse();

if (count($list) == 0) 

	echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";

else {

	$count = count($list);

	foreach ($list as $gara) {

		$wkc = Gara::fromId($gara->getChiave())->getWkc();

		if(_WKC_MODE_)

		{

			if($wkc == 0)

			{

				$count--;

				continue;

			}

		}

		else

		{

			if($wkc == 1)

			{

				$count--;

				continue;

			}

		}

		

		$id = $gara->getChiave();

		$desc = $gara->getNome();

		$locandina= $gara->getLocandina();

		echo '<li><table width="100%"><tr><td>';

		

		echo "<span class='tDescr'>$desc</span></td><td class=\"liButton\">";

		echo "<a href=\"".urlRiepilogoCompleto($gara)."\">$lang[elenco_partecipanti]</a>";

		echo "<a  href=\""._PATH_ROOT_."dettagli.php?id=$id\">$lang[gara_dettagli]</a>";

		echo "</td></tr></table></li>";

	}

	

	if($count == 0)

		echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";

}

?>



<!-- <h1><?php echo $lang["tesserati"]; ?></h1> -->

<?php 

	$ut = $ctrl->getUtSocieta();

	$str_add = $lang["aggiungi_affiliata"];

	$str_ele = $lang["elenco"];

	if(!$ut->getSocieta()->isAffiliata())

	{

		echo "<h1>".$lang["tesserati"]."</h1>";

		

		//nuovo atleta

		$str_nat = $lang["atleta"];

		echo '<li><table width="100%"><tr><td>';

		echo "<span class='tDescr'>$str_nat</span></td><td class=\"liButton\">";

		echo "<a href=\"newatleta.php\">$str_add</a>";

		echo "<a href=\"eleatleti.php\">$str_ele</a>";

		echo "</td></tr></table></li>";

		

		//nuovo coach

		$str_nco = $lang["coach"];

		echo '<li><table width="100%"><tr><td>';

		echo "<span class='tDescr'>$str_nco</span></td><td class=\"liButton\">";

		echo "<a href=\"newcoach.php\">$str_add</a>";

		echo "<a href=\"elecoach.php\">$str_ele</a>";

		echo "</td></tr></table></li>";

		

		echo "<h1>".$lang["reg_ref_off"]."</h1>";

		if(_WKC_MODE_)

		{

			echo '<li><table width="100%"><tr><td>';

			echo "<span class='tDescr'>".$lang["auto_call"]."</span></td></tr></table></li>";

		}

		

		

		//nuovo arbitro

		$str_nar = $lang["arbitri"];

		echo '<li><table width="100%"><tr><td>';

		echo "<span class='tDescr'>$str_nar</span></td><td class=\"liButton\">";

		echo "<a href=\"newarbitro.php\">$str_add</a>";

		echo "<a href=\"elearbitro.php\">$str_ele</a>";

		echo "</td></tr></table></li>";

	}

	

	if($ut->getSocieta()->isAffiliata())

	{

		echo "<h1>".$lang["reg_ref_off"]."</h1>";

	}

	

	//nuovo official

	$str_nof = $lang["official"];

	echo '<li><table width="100%"><tr><td>';

	echo "<span class='tDescr'>$str_nof</span></td><td class=\"liButton\">";

	echo "<a href=\"newofficial.php\">$str_add</a>";

	echo "<a href=\"eleofficial.php\">$str_ele</a>";

	echo "</td></tr></table></li>";

?>
</div>
  
  
  <div class='Gare_soc_right'>

    <h1><?php echo $lang["stage"]; ?></h1>

    <?php
    $list_stage = $ctrl->getAllStageAttive();
    if (count($list_stage) == 0)
        echo "<li><span class='tDescr'>$lang[no_gare]</span></li>";

    else
    {
        $count = count($list_stage);

        foreach ($list_stage as $stage)
        {
            $id = $stage->getChiave();

            $desc = $stage->getNome();

            $locandina = $stage->getLocandina();
            echo '<li><table width="100%"><tr><td>';



            echo "<span class='tDescr'>$desc</span></td><td class=\"liButton\">";

            echo "<a href=\"" . urlIscriviStage($stage) . "\">$lang[gara_iscrizioni]</a>";

            echo "<a  href=\"" . _PATH_ROOT_ . "dettagli.php?id=$id\">$lang[gara_dettagli]</a>";

            echo "</td></tr></table></li>";
        }
    }
    ?>


  </div>

</div>

<div style="clear:both"></div>

<?php 

$templ->chiudiBody();

?>