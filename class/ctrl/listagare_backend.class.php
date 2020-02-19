<?php
if (!defined("_BASEDIR_")) exit();
include_controller("listagare");
include_model("Gara");

class ListaGareBackend extends ListaGare {
	
	/**
	 * @param boolean $resp true se la pagina è per responsabili,
	 * false se è per organizzatori, altrimenti il tipo utente
	 */
	public function __construct($resp) {
		parent::__construct($resp);
	}
	
	protected function creaUtente($tipout=NULL) {
		if ($tipout === true) {
			include_model("Responsabile");
			return Responsabile::crea();
		} else if ($tipout === false) {
			include_model("Organizzatore");
			return Organizzatore::crea();
		} else {
			include_model("Utente");
			return Utente::crea(NULL, true);
		}
	}
	
	public function getGareChiuse() {
		return Gara::getGareChiuse($this->getZone());
	}
	
	protected function getZone() {
		if (isset($_GET["all"])) return NULL;
		return $this->ut->getZone();
	}
	
	/**
	 * Restituisce true se organizzatore e gara hanno una zona in comune, altrimenti ritorna false
	 * @param int[] $zone_gara
	 * @param int[] $zone_org
	 * @return boolean
	 */
	public function checkZone($zone_gara, $zone_org)
	{
		$ret = false;
		
		foreach($zone_gara as $id_g=>$id_z_g)
		{
			if($ret == true)
				continue;
			
			foreach($zone_org as $id_o=>$id_z_o)
			{
				if($ret == true)
					continue;
				
				if($id_z_g == $id_z_o)
					$ret = true;
			}
		}
		
		return $ret;
	}
}
?>