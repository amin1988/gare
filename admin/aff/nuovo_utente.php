<?php
session_start();
require_once("../../config.inc.php");
include_controller("admin/nuovo_utente");
include_view("Header", "Template", "gestione_utente");
$lang = Lingua::getParole();

$ctrl = new NuovoUtente(true);
$view = new GestioneUtenteView($ctrl);
$head = Header::titolo($lang["nuovo_utente"]);
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/aff",$lang["gestione_affiliate"]);
$templ = new Template($head);

$templ->stampaTagHead(false);
$view->stampaJavascript();
$view->stampaStile();
echo '</head>';
$templ->apriBody();

$view->stampaInizioForm(); 
echo '<table class="tr" width="98%" >';
$view->stampaApertura($lang["societa"], "societa");
echo $ctrl->getNomeSocieta();
$view->stampaChiusura();

$view->stampaUsername();
$view->stampaNome();
$view->stampaPassword();
$view->stampaConfPassword();
$view->stampaEmail();

echo '<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">';
$view->stampaSocietaNascosta();
$view->stampaPulsante($lang["crea_gara"]); 
?>
  </th></tr>
  </table>
</form>
<?php 
$templ->chiudiBody();
?>