<?php
session_start();

require_once("../../config.inc.php");
include_model("Utente");
include_view("riepilogo/indiv");
RiepilogoCompletoIndividualeView::stampaPagina(Utente::VISUALIZZA);
?>