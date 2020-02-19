<?php
if (!defined("_BASEDIR_")) exit();

function get_anno_sportivo() {
	return AnnoSportivoFiam::get();
}

class AnnoSportivoFiam {
	private static $anno = 0;
	
	/**
	 * @return int
	 */
	public static function get() {
		if (self::$anno != 0) return self::$anno;
		if (isset($GLOBALS["ultima_gara"])) {
			$g = $GLOBALS["ultima_gara"];
			/* @var $g Gara */
			self::$anno = $g->getDataGara()->getAnno();
			return self::$anno;
		}
		return self::$anno = date('Y'); //TODO restituisce l'anno attuale, modificare?
	}
}
