<?php
session_start();

require_once("../config.inc.php");
include_model("Utente");
include_view("riepilogo/soc");
RiepilogoSocietaView::stampaPagina(Utente::RESPONSABILE);

?>