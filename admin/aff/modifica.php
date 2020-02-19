<?php
session_start();
require_once("../../config.inc.php");
include_controller("admin/modifica_affiliata");
include_view("Header", "Template", "gestione_affiliata");
$lang = Lingua::getParole();

$ctrl = new ModificaAffiliata();
$view = new GestioneAffiliataView($ctrl);
$head = Header::titolo($lang["modifica_affiliata_titolo"]);
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/aff",$lang["gestione_affiliate"]);

$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX);

$templ->stampaTagHead(false);
$view->stampaJavascript();
echo '</head>';
$templ->apriBody(); 

$view->stampaInizioForm();
echo '<table id="tab_societa" class="tr" width="98%" >';

$view->stampaNome();
$view->stampaNomeBreve();
$view->stampaStile();
$view->stampaZone();
?>
<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">
<?php $view->stampaPulsante($lang["modifica_affiliata"]); ?>
  </th></tr>
  </table>
</form>
<?php 
$templ->chiudiBody();
?>