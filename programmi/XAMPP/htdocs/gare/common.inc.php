<?php 
function include_model() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."models/$file.php"); 
	}
}

function include_esterni() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."models/esterni/$file.php");
	}
}

function include_view() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."view/$file.view.php");
	}
}

function include_controller() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."ctrl/$file.class.php");
	}
}

function include_class() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."$file.php");
	}
}

function include_errori() {
	foreach(func_get_args() as $file) {
		require_once(_CLASSDIR_."err/$file.php");
	}
}

function include_menu($base=NULL) {
	require_once(_CLASSDIR_."Menu.php");
	if ($base != NULL)
		$file = _BASEDIR_."$base/menu.inc.php";
	else
		$file = 'menu.inc.php';
	if (file_exists($file))
		include_once $file;
}

function redirect($pagina, $prot='http'){
	header("Location: $prot://$_SERVER[HTTP_HOST]"._PATH_ROOT_.$pagina);
}

function check_get($key) {
	if(!isset($_GET[$key])) {homeutente(NULL);}
}

function nologin($autored = true){
	if ($autored) {
		$ref = urlencode($_SERVER["REQUEST_URI"]); //TODO controllo host
		redirect("login.php?red=$ref");
	} else
		redirect("login.php");
	exit();
}

/**
 * @param Utente $ut
 */
function homeutente($ut,$return=false) {
	$tipo = NULL;
	if (!is_null($ut)) $tipo = $ut->getTipo();
	switch ($tipo) {
		case Utente::SOCIETA:
			$p = "soc/";
			break;
		case Utente::ORGANIZZATORE:
			$p = "org/";
			break;
		case Utente::RESPONSABILE:
			$p = "resp/";
			break;
		case Utente::ADMIN:
			$p = "admin/";
			break;
		case Utente::VISUALIZZA:
			$p = "vis/";
			break;
		default:
			$p = "/";
			break;
	}
	if ($return) return $p;
	else redirect($p);
}

header('Content-type: text/html; charset=utf-8');

/**
 * percorso reale della cartella base
 */
define("_BASEDIR_",$_SERVER["DOCUMENT_ROOT"]._PATH_ROOT_);
/**
 * percorso reale della cartella delle classi
 */
define("_CLASSDIR_",_BASEDIR_."class/");

require_once(_CLASSDIR_."Lingua.php");

?>