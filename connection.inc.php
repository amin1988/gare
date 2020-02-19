<?php
if (!defined("_BASEDIR_")) exit();
include_class("Connessione");

//database interno
$ci = new Connessione();
$ci->host = "localhost";
$ci->user = "root";
$ci->psw = "";
$ci->dbname = "Sql198280_5";
$ci->port = ini_get("mysqli.default_port");

//database esterno
$ce = new Connessione();
$ce->host = "localhost";
$ce->user = "root";
$ce->psw = "";
$ce->dbname = "Sql198280_3";
$ce->port = ini_get("mysqli.default_port");

//connessione al db interno
$GLOBALS["connint"] = $ci;
//connessione al db esterno
$GLOBALS["connest"] = $ce;

/* * ** RANKING SW GARE *** */
$sw = new Connessione();
$sw->host = "localhost";
$sw->user = "root";
$sw->psw = "";
$sw->dbname = "tmp_db";
$sw->port = ini_get("mysqli.default_port");
 $GLOBALS["connsw"] = $sw;
