<?php
$base = _PATH_ROOT_;

Menu::addPagina("{$base}admin/index.php", "admin_titolo", NULL, false);
Menu::addPagina("index.php", "gestione_utenti", "{$base}admin/index.php", false);
Menu::addPagina("nuovo.php", "nuovo_utente", "index.php", false);
Menu::addPagina("modifica.php", "modifica_utente_titolo", "index.php", false);
