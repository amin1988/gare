<?php
Menu::addPagina("index.php", "lista_gare", NULL, false);
Menu::addPagina("modifica.php", "modifica_gara", "index.php", false);
Menu::addPagina("nuova.php", "nuova_gara", "index.php", false);
Menu::addPagina("convocazioni.php", "convocazioni", "index.php", false);
Menu::addScheda("riepilogo_soc.php", "riepilogo_societa", "index.php", "riepilogo");
Menu::addScheda("riepilogo.php", "riepilogo_individuali", "index.php", "riepilogo", 0); //individuali
Menu::addScheda("riepilogosq.php", "riepilogo_squadre", "index.php", "riepilogo", 1); //squadre
Menu::addScheda("stat.php", "statistiche_titolo", "index.php", "riepilogo");

Menu::addPagina("convalida_presenze.php", "convalida_presenze", "index.php", false);
Menu::addPagina("lista_presenze_stage.php", "convalida_presenze", "index.php", false);
