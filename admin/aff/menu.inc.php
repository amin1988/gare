<?php
$base = _PATH_ROOT_;

Menu::addPagina("{$base}admin/index.php", "admin_titolo", NULL, false);
Menu::addPagina("index.php", "gestione_affiliate", "{$base}admin/index.php", false);
Menu::addPagina("aggiungi.php", "aggiungi_affiliata_titolo", "index.php", false);
Menu::addPagina("modifica.php", "modifica_affiliata_titolo", "index.php", false);
Menu::addPagina("nuovo_utente.php", "nuovo_utente", "index.php", false);
