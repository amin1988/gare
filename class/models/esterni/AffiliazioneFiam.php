<?php 

class AffiliazioneFiam {
	const PATH = "../tesseramento/";
	
	public static function getUrlAtleta($id) {
		return _PATH_ROOT_.self::PATH."soc/tess.php?id=$id";
	}
	
	public static function getUrlCambioCintura($id) {
		return _PATH_ROOT_.self::PATH."soc/tess-mod.php?id=$id";
	}
}
