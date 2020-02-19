<?php
if (!defined("_BASEDIR_")) exit();

class DownloadIscrizioniView {
	/**
	 * @var DownloadIscrizioni
	 */
	private $ctrl;
	
	/**
	 * @param DownloadIscrizioni $ctrl
	 */
	public function __construct($ctrl) {
		$this->ctrl = $ctrl;
	}
	
	public function stampa() {
		foreach ($this->ctrl->getInfoModuli() as $cod => $info) {
			$this->stampaInfo($info, $cod);
		}
	}
	
	/** 
	 * @param Info $info
	 */
	private function stampaInfo($info, $cod) {
		echo '<br><div id="Right" style="width:90%;"><div class="Gare_soc_right">';
		echo '<h1>'.$info->getNome()."</h1>\n";
		echo '<form method="get" accept-charset="UTF-8" target="download_iscr">';
		echo "\n<input type=\"hidden\" name=\"mod\" value=\"$cod\">\n";
		echo "<input type=\"hidden\" name=\"id\" value=\"".$this->ctrl->getIdGara()."\">\n";
		
		foreach ($info->getParams() as $param => $tipo) {
			echo '<li>';
			switch ($tipo) {
				case Info::BOOL:
					$this->paramBool($info, $param, $cod);
					break;
				case Info::SELECT:
					$this->paramSelect($info, $param);
					break;
				default:
					$this->paramText($info, $param);
					break;
			}
			echo "</li>\n";
		}
		echo '<div class="pulsante"><input type="submit" value="'.Lingua::getParola("scarica_iscrizioni").'"></div><br>';
		echo "</form></div></div>\n\n";
	}

	/**
	 * @param Info $info
	 * @param string $param
	 * @param string $cod
	 */
	private function paramBool($info, $param, $cod) {
		$id = "{$cod}_{$param}";
		echo '<input type="checkbox" ';
		if ($info->getDefault($param))
			echo 'checked="checked" ';
		echo "id=\"$id\" name=\"$param\" value=\"1\"> ";
// 		echo "<label for=\"$id\">";
		echo $info->getLabel($param);
// 		echo "</label>";
	}
	
	/**
	 * @param Info $info
	 * @param string $param
	 */
	private function paramSelect($info, $param) {
		echo $info->getLabel($param);
		echo " <select name=\"$param\">";
		$def = $info->getDefault($param);
		foreach ($info->getValori($param) as $val) {
			echo "<option value=\"$val\"";
			if ($val == $def) echo 'selected="selected" ';
			echo ">";
			echo $info->getLabelValore($param, $val);
			echo "</option>\n";
		}
		echo '</select>';
	}
	
	private function paramText($info, $param) {
		echo $info->getLabel($param); //TODO
	}
}