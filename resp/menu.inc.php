<?php
Menu::addPagina("index.php", "lista_gare", NULL, false);
Menu::addPagina("accorpa.php", "accorpa_titolo", "index.php", false);
Menu::addScheda("download_iscrizioni.php", "scarica_iscrizioni", "index.php", "riepilogo");
Menu::addScheda("riepilogo_soc.php", "riepilogo_societa", "index.php", "riepilogo");
Menu::addScheda("riepilogo.php", "riepilogo_individuali", "index.php", "riepilogo", 0); //individuali
Menu::addScheda("riepilogosq.php", "riepilogo_squadre", "index.php", "riepilogo", 1); //squadre
Menu::addScheda("stat.php", "statistiche_titolo", "index.php", "riepilogo");
