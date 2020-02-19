<?php
if (!defined("_BASEDIR_")) exit();

class Data {
	private static $_oggi = NULL;
	
	/**
	 * @var int[] 0: anno, 1: mese, 2: giorno
	 */	
	private $dt;

	public static function oggi() {
		if (is_null(self::$_oggi)) self::$_oggi = new Data();
		return self::$_oggi;
	}
	
	/**
	 * Legge una data in formato dd/mm/yyyy
	 * @param string $valore
	 */
	public static function parseDMY($valore) {
		if (!preg_match('/^(\d\d?)\/(\d\d?)\/(\d{4})$/', trim($valore), $m))
			return NULL;
		return new Data("$m[3]-$m[2]-$m[1]");
		
	}
	/**
	 * @param string $data formato yyyy-mm-dd
	 */
	public function __construct($data = NULL) {
		if (is_null($data)) $data = date("Y-m-d");
		$e = preg_split('/\D/', $data);
		$this->dt[0] = intval($e[0]); 
		$this->dt[1]  = intval($e[1]);
		$this->dt[2]  = intval($e[2]);
	}
	
	/**
	 * @param Data $data se NULL confronta con data attuale
	 * @return int 0 se la data è uguale, -1 se questa data è precedente, 1 se è successiva
	 */
	public function confronta($data) {
		for ($i=0; $i < 3; $i++) { 
			if ($this->dt[$i] < $data->dt[$i])
				return -1;
			if ($this->dt[$i] > $data->dt[$i])
				return 1;
		}
		return 0;
	}
	
	/**
	 * @return boolean
	 */
	public function valida() {
		return checkdate($this->dt[1], $this->dt[2], $this->dt[0]);
	}
	
	/**
	 * Indica se questa data è una data futura (oggi escluso)
	 * @return boolean
	 */
	public function futura() {
		return $this->confronta(self::oggi()) > 0;
	}
	
	/**
	 * Indica se questa data è una data passata (oggi escluso)
	 * @return boolean
	 */
	public function passata() {
		return $this->confronta(self::oggi()) < 0;
	}
	
	public function format($format) {
// 		return date($format, mktime(0,0,0,$this->dt[1], $this->dt[2], $this->dt[0]));
    	$spf = str_replace(array("d","m","Y"),array("%3$02d","%2$02d","%1$04d"),$format);
   		return sprintf($spf, $this->dt[0], $this->dt[1], $this->dt[2]);
	}
	
	/**
	 * @param Data $data
	 * @param boolean $millesimo
	 * @return int
	 */
	public function anniDa($data,$millesimo=true) {
		if ($millesimo || $this->dt[1] < $data->dt[1])
			return $data->dt[0] - $this->dt[0]; 
    	//controllo preciso se $millesimo==false
    	$eta = $data->dt[0] - $this->dt[0];
    	if ($this->dt[1] > $data->dt[1])
    		return $eta - 1;
    	//stesso mese
    	if ($this->dt[2] > $data->dt[2])
    		return $eta - 1;
    	 else
    	 	return $eta;
	}
	
	/**
	 * @return int
	 */
	public function getGiorno() {
		return $this->dt[2];
	}
	
	/**
	 * @return int
	 */
	public function getMese() {
		return $this->dt[1];
	}
	
	/**
	 * @return int
	 */
	public function getAnno() {
		return $this->dt[0];
	}
	
	/**
	 * @return string data formato yyyy-mm-dd
	 */
	public function toString() {
		return implode('-', $this->dt);
	}
}
