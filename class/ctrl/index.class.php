<?php
if (!defined("_BASEDIR_")) exit();
include_model("Gara");

class Index {
	
	/**
	 * @return Gara[]
	 */
	public function getGarePubbliche() {
		return Gara::getGarePubblicheNonTerminate();
	}
	
}
?>