<?php
session_start();
require_once("../../config.inc.php");
include_controller("admin/aggiungi_affiliata");
include_view("Header", "Template", "gestione_affiliata");
$lang = Lingua::getParole();

$ctrl = new AggiungiAffiliata();
$view = new GestioneAffiliataView($ctrl);
$head = Header::titolo($lang["aggiungi_affiliata_titolo"]);
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/aff",$lang["gestione_affiliate"]);

$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX);

$templ->stampaTagHead(false);
$view->stampaJavascript();
echo "</head>";
$templ->apriBody();
?>

<?php $view->stampaInizioForm(); ?>
<table id="tab_societa" class='tr' width='98%' >
<?php 
$view->stampaNome();
$view->stampaNomeBreve();
$view->stampaStile();
$view->stampaUtente();
$view->stampaZone();
?>
<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">
<?php $view->stampaPulsante($lang["aggiungi_affiliata"]); ?>
  </th></tr>
  </table>
</form>

<?php 
$templ->chiudiBody();
?>