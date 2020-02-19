<?php
Menu::addPagina("index.php", "lista_gare", NULL, false);
Menu::addScheda("scegli.php", "iscrizioni_titolo", "index.php", "riepilogo_titolo", -1);
Menu::addPagina("iscrivi.php", "iscrizioni_individuali", "scegli.php", true);
Menu::addPagina("iscrivi_stage.php", "iscrizioni_stage", "index.php", true); // aggiunta per stage
Menu::addPagina("iscrivisq.php", "iscrizioni_squadre", "scegli.php", true);
Menu::addPagina("modsq.php", "modifica_squadra_titolo", "iscrivisq.php", false);
Menu::addScheda("riepilogo.php", "riepilogo_individuali", "index.php", "riepilogo_titolo", 0); //individuali
Menu::addScheda("riepilogosq.php", "riepilogo_squadre", "index.php", "riepilogo_titolo", 1); //squadre
Menu::addScheda("riepilogo_completo.php", "riepilogo_individuali", "index.php", "riepilogo_titolo", 10); //ind completo
Menu::addScheda("riepilogosq_completo.php", "riepilogo_squadre", "index.php", "riepilogo_titolo", 11); //sq completo
Menu::addPagina("newcoach.php", "nuovo_coach", "index.php", false);
Menu::addPagina("newatleta.php", "nuovo_atleta", "index.php", false);
Menu::addPagina("newarbitro.php", "nuovo_arbitro", "index.php", false);
Menu::addPagina("newofficial.php", "nuovo_official", "index.php", false);
Menu::addPagina("elecoach.php", "elenco_coach", "index.php", false);
Menu::addPagina("eleatleti.php", "elenco_atleti", "index.php", false);
Menu::addPagina("elearbitro.php", "elenco_arbitri", "index.php", false);
Menu::addPagina("eleofficial.php", "elenco_official", "index.php", false);