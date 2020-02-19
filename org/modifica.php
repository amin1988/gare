<?php
session_start();
require_once("../config.inc.php");
include_controller("org/modifica_gara");
include_view("Header","Template","gestione_gara");
include_menu();
$lang = Lingua::getParole();

$ctrl = new ModificaGara();
$view = new GestioneGaraView($ctrl);
//TODO eliminare vecchio header
// $head = Header::titolo($lang["modifica_gara"]);
// $head->setIndietro("org",$lang["lista_gare"]);
// $templ = new Template($head);
$templ = new Template();
$templ->includeJs(Template::CHECKBOX, Template::CALENDAR);
$templ->stampaTagHead(false);
?>
<!-- INIZIO CALENDARIO -->
<script type="text/javascript">
function setCalendar(txt){
	cal = new CalendarEightysix(txt, {
		 'startMonday': true, 
		 'defaultView': 'month',
		 'alignX': 'right', 
		 'alignY': 'ceiling', 
		 'format': '%d/%m/%Y',
		 'defaultDate':'today',
		 'prefill':false
		 });
}

document.addEvent('domready', function() {
<?php 
	$dl = Lingua::getLinguaDefault();
	if ($dl != "en")
		echo "MooTools.lang.setLanguage('$dl');";
?>

	setCalendar(document.getElementById("datafine"));
	setCalendar(document.getElementById("chiusura"));
});	
</script>
<!-- FINE CALENDARIO -->
<?php $view->stampaJavascript(); ?>
<style>
input[type="file"] {
	padding:8px;
	
}

</style>
</head>

<?php 
$templ->apriBody();
?>
<form accept-charset="UTF-8" action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
<table class='tr' width='98%' >
    <tr >
    <th width='40%'><div class="thAtleti thAtletiDx"><?php echo $lang["nome_gara"]; ?>:</div>
   </th>
    <td><?php echo $view->stampaNome(); ?></td>
    </tr><tr>
    <th>
    <div class="thAtleti thAtletiDx"></div>
    </th>
    <td>
    <div class="inputGestGara" style='height:20px;width:100px;text-align:center;float:left;line-height:20px'>
	<?php echo $view->stampaPubblica(); ?>
	</div>  
    <div class="inputGestGara" style='height:20px;width:100px;text-align:center;float:left;line-height:20px'>
	<?php echo $view->stampaPrivata(); ?>
    </div>
    </td>
    </tr><tr>
    <th>
    <div class="thAtleti thAtletiDx"></div>
    </th>
    <td>
    <div class="inputGestGara" style='height:20px;width:100px;text-align:center;float:left;line-height:20px'>
	<?php echo $view->stampaPeso(); ?>
	</div>  
    <div class="inputGestGara" style='height:20px;width:100px;text-align:center;float:left;line-height:20px'>
	<?php echo $view->stampaAltezza(); ?>
    </div>
    </td>
    </tr><tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["data_gara"]; ?>:</div>
    
    </th>
    <td><?php echo $ctrl->getDataGara(); //$view->stampaData(); // non pu� cambiare data gara ?></td> 
    </tr>
    
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["data_fine_gara"]; ?>:</div></th>
    <td><?php echo $view->stampaFineGara(); ?></td>
    </tr>
    
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["chiusura_iscrizioni"]; ?>:</div></th>
    <td><?php echo $view->stampaChiusura(); ?></td>
    </tr>
        
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["num_coach"]; ?>:</div></th>
    <td><?php echo $view->stampaNumCoach(); ?></td>
    </tr>
    
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["coach"]; ?>:</div>
    </th>
    <td>
    <div class="inputGestGara" style='height:20px;width:200px;text-align:center;float:left;line-height:20px'>
    <?php $view->stampaPagamentoCoach(); ?>
    </div>
    <div class="inputGestGara" style='height:20px;width:200px;text-align:center;float:left;line-height:20px'>
    <?php $view->stampaFotoCoach(true); ?>
	</div>  
    </td>
    </tr>         
            
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["prezzo_coach"]; ?>:</div></th>
    <td><?php echo $view->stampaPrezzoCoach(); ?></td>
    </tr>
    
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["prezzo_indiv"]; ?>:</div></th>
    <td><?php echo $view->stampaPrezzoIndiv(); ?></td>
    </tr>
                
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["prezzo_sq"]; ?>:</div></th>
    <td><?php echo $view->stampaPrezzoSquadre(); ?></td>
    </tr>
    
    <tr>
    <th>
    <div class="thAtleti thAtletiDx"><?php echo $lang["rimb_arb"]; ?>:</div></th>
    <td><?php echo $view->stampaRimborosoArbitro(); ?></td>
    </tr>
            
    <tr>
    <th style="vertical-align: top;"><div class="thAtleti thAtletiDx"><?php echo $lang["locandina_gara"]; ?>:</div></th>
    
    <td>
<?php if ($ctrl->haLocandina()) { ?>
    
    <input type="radio" name="azioneloc" value="tieni" onchange="enable('modloc','locandina');" checked="checked" /> <?php echo $lang["tieni_locandina"]; ?>
    <!-- //TODO mostra locandina --><br />
    
    <input type="radio" name="azioneloc" value="elimina" onchange="enable('modloc','locandina');" /> <?php echo $lang["elimina_locandina"]; ?><br />
    
    <input type="radio" name="azioneloc" id="modloc" value="modifica" onchange="enable('modloc','locandina');" /> <?php echo $lang["modifica_locandina"]; ?>
    <input type="file" name="locandina" id="locandina" required="required" disabled="disabled" accept="image/*" class='inputGestGara'/>
    
<?php } else {
	$view->stampaLocandina();
} //else haLocandina ?>
    </td>
    </tr>
    
    <tr>
        <th colspan="2"><div class="thAtleti thAtletiDx" style="text-align:center"><?php echo $lang["descrizione_gara"]; ?>:</div></th>
    
    
    </tr>
    <tr>
    <td colspan="2" id="descrizione"><?php $view->stampaDescrizione(); ?></td>
    </tr>
<tr>
<th colspan="2">
<?php $view->stampaZone(); ?>
</th>
</tr>
<tr>
<th colspan="2"><fieldset class="inputGestGara">
<legend><?php echo $lang["documenti"]; ?>:</legend>
<div style='position:relative;left:50px;'>
<?php
foreach ($ctrl->getAllegati() as $a) {
	/* @var $a Allegato */
	$id = $a->getChiave();
	echo "<div>";
	$view->stampaDocNoFile($id, $ctrl->getNomeDocCaricato($a), $ctrl->isDocCaricatoSelezionato($a));
	echo "<a href=\""._PATH_ROOT_."download_allegato.php?id=$id\" target=\"_blank\">$lang[apri_file]</a></div><div style=\"clear:both\"></div>\n";
}

for($i=0; $i<5; $i++) {
	$view->stampaDoc($i);
}
?>
</div>
</fieldset>
</th>
</tr>
<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">
  <input type="submit" value="<?php echo $lang["modifica_gara"]; ?>" id="inputGestGara"/>
  </th></tr>
  </table>
</form>
<?php 
$templ->chiudiBody();
?>