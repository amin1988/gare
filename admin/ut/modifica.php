<?php
session_start();
require_once("../../config.inc.php");
include_controller("admin/modifica_utente");
include_view("Header", "Template", "gestione_utente");
$lang = Lingua::getParole();

$ctrl = new ModificaUtente();
$view = new GestioneUtenteView($ctrl);
$head = Header::titolo($lang["modifica_utente_titolo"]);
$head->addIndietro("admin",$lang["admin_titolo"]);
$head->addIndietro("admin/ut",$lang["gestione_utenti"]);

$templ = new Template($head);
$templ->includeJs(Template::CHECKBOX);

$templ->stampaTagHead(false);
$view->stampaJavascript($ctrl->getTipo()); 
echo '</head>';

$templ->apriBody();
$view->stampaInizioForm();
echo '<table class="tr" width="98%" >';

$view->stampaUsernameFisso();
$view->stampaNome();
$view->stampaPassword(false);
$view->stampaConfPassword(false);
$view->stampaEmail();
$view->stampaTipoFisso();

if ($ctrl->isSocietaSelezionata())
	$view->stampaSocieta();
else if ($ctrl->isTipoZonaSelezionato())
	$view->stampaZone();
?>
<tr><th colspan="2" class="thAtleti thAtletiDx" style="text-align:center">
  <input type="submit" value="<?php echo $lang["modifica_utente"]; ?>" id="inputGestGara"/>
  </th></tr>
  </table>
</form>
<?php 
$templ->chiudiBody();
?>