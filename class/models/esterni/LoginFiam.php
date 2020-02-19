<?php
if (!defined("_BASEDIR_")) exit();

class LoginFiam {
	
	public static function getIdSocieta() {
		if (!isset($_SESSION['tess_idsoc'])) 
			return NULL; 
		return $_SESSION['tess_idsoc'];
	}
}
?>