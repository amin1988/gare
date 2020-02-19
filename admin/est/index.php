<?php
session_start();

require_once("../../config.inc.php");
include_controller("admin/listaesterne");
include_view("Header", "Template", "listaesterne");
$lang = Lingua::getParole();

$ctrl = new ListaEsterne();
$head = Header::titolo($lang["gestione_esterne"]);
$head->setIndietro("admin",$lang["admin_titolo"]);
$templ = new Template($head);

$view = new ListaEsterneView($ctrl);

$templ->stampaTagHead();
$templ->apriBody();
?>
    <div id="Right" style="width:90%;">
<?php 
//$view->stampaNonInserite();
$view->stampaInserite();
?>    
<br />
	</div>
<?php 
$templ->chiudiBody();
?>