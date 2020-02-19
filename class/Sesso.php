<?php
if (!defined("_BASEDIR_")) exit();

class Sesso {
	const M = 1;
	const F = 2;
	const MISTO = 3;
	
	public static function toStringBreve($idsesso) {
		//TODO usare lingua
		switch ($idsesso) {
			case self::M:
				return "M";
			case self::F:
				return "F";
			case self::MISTO:
				return "M-F";
		}
	}
	
	public static function toStringLungo($idsesso) {
		if($idsesso == 3)
			return Lingua::getParola("#sesso3mix");
		return Lingua::getParola("#sesso$idsesso");
	} 
}
?>