<?php
session_start();

require_once("../../config.inc.php");
include_controller("admin/listaaffiliate");
include_view("Header", "Template", "listaaffiliate");
$lang = Lingua::getParole();

$ctrl = new ListaAffiliate();
$head = Header::titolo($lang["gestione_affiliate"]);
$head->setIndietro("admin",$lang["admin_titolo"]);
$templ = new Template($head);

$view = new ListaAffiliateView($ctrl);

$templ->stampaTagHead();
$templ->apriBody();
?>
    <div id="Right" style="width:90%;">
<?php 
$view->stampaNonInserite();
$view->stampaInserite();
?>    
<br />
	</div>
<?php 
$templ->chiudiBody();
?>