<?php
session_start();
require_once("../../config.inc.php");
include_controller("admin/nuovo_utente");
include_view("Header", "Template", "gestione_utente");
$lang = Lingua::getParole();

$ctrl = new NuovoUtente();
$view = new GestioneUtenteView($ctrl);
$head = Header::titolo($lang["nuovo_utente"]);
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/ut",$lang["gestione_utenti"]);

$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX);

$templ->stampaTagHead(false);
$view->stampaJavascript();
$view->stampaStile();
echo '</head>';
$templ->apriBody();
?>

<?php $view->stampaInizioForm(); ?>
<table class='tr' width='98%' >
<?php 

$view->stampaUsername();
$view->stampaNome();
$view->stampaPassword();
$view->stampaConfPassword();
$view->stampaEmail();
$view->stampaTipo();

$view->stampaSocieta();
$view->stampaZone();
?>
<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">
<?php $view->stampaPulsante($lang["crea_gara"]); ?>
  </th></tr>
  </table>
</form>
<?php 
$templ->chiudiBody();
?>