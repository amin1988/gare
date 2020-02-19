<?php
$base = _PATH_ROOT_;

Menu::addPagina("{$base}vis/index.php", "lista_gare", NULL, false);
Menu::addPagina("index.php", "storico", "{$base}vis/index.php", true);
Menu::addScheda("riepilogo_soc.php", "riepilogo_societa", "index.php", "riepilogo");
Menu::addScheda("riepilogo.php", "riepilogo_individuali", "index.php", "riepilogo", 0); //individuali
Menu::addScheda("stat.php", "statistiche_titolo", "index.php", "riepilogo");
