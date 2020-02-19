<?php
session_start();

require_once("../../config.inc.php");
include_controller("admin/listautenti");
include_view("Header", "Template", "listautenti");
$lang = Lingua::getParole();

$ctrl = new ListaUtenti();
$view = new ListaUtentiView($ctrl);
$head = Header::titolo($lang["gestione_utenti"]);
$head->setIndietro("admin",$lang["admin_titolo"]);

$templ = new Template($head);
$templ->includeJs("ajax","popup");

$templ->stampaTagHead(false);
?>
<script type="text/javascript">

function elimina(id,nome) {
	msg = "<?php echo $lang["elimina_utente_domanda"]; ?>";
	/*var r=confirm(msg.replace("<NAME>",nome));
	if (!r) return;
	ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/delut.php?id="+id, id, cancRiga);*/
	showConfirm(msg.replace("<NAME>",nome), function(val){
		if (val == 0) return;
		ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/delut.php?id="+id, id, cancRiga);
	});
}

function cancRiga(resp, id) {
	if (resp != "1") return;
	var u = document.getElementById("utente_"+id);
	u.style.display="none";
	showPopup("<?php echo $lang["utente_eliminato"]; ?>");
}
</script>
</head>

<?php 
$templ->apriBody();
?>
    
	<div class="pulsante tr" style="text-align:center">
	<a href="nuovo.php" class='pulsante_noInput'><?php echo $lang["nuovo_utente"]; ?></a><br>
	</div>
	<br><br>
    
<?php 
$view->stampaAdmin();
$view->stampaOrganizzatori();
$view->stampaResponsabili();
$view->stampaVisualizzatori();
$view->stampaSocieta();

$templ->chiudiBody();
?>