<?php
if (!defined("_BASEDIR_")) exit();
include_model("Societa","UtSocieta");

class Registra{
	
	
	function _array_campi(){
		$array_form=array(
	0=>"societa",
	1=>"abbrevia",
	2=>"paese",
	3=>"contatto",
	4=>"tel",
	5=>"email",
	6=>"conf_email",
	7=>"stile_iscrizioni"
	
	);
		
		return $array_form;
		
	}
	
	function _array_campi_db(){
		
		$array=array(
	0=>"nome",
	1=>"nomebreve",
	2=>"paese",
	3=>"contatto",
	4=>"tel",
	5=>"email",
	6=>"conf_email",
	7=>"stile"
	
	);
		
		return $array;
		
	}
	
	function _array_db(){
		$array = array(
		1=>"nome",
		2=>"nomebreve",
		3=>"idstile",
		4=>"idzona",
		5=>"idaffiliata"
				);
		
		return $array;
	}
	
	
	
	function __construct(){
	
if (isset($_POST['reg'])) {
            //$array_form = $this->_array_campi();
            $array_db = $this->_array_campi_db();
            $array_form= $this->_array_db();
            $count = count($array_form);
            $i = 0;
            foreach ($array_form as $key => $value) {
                if ($_POST[$value] != '')
                    $i ++;
            }
            if ($i == $count) {
                //$societa->
                $array = array();
                foreach ($array_form as $key_1 => $value_1) {
                    $campo = $array_db[$key_1];
                    $array[$campo] = $_POST[$value_1];
                }
                //print_r($array);
                $societa = Societa::nuovo($array);
                $societa->salva();
                $utente = UtSocieta::nuovo($societa->getChiave(), 
                $_POST['nomebreve'], array("nome" => $_POST['nomebreve']));
                $utente->salva();
                header("location:login.php");
            } else {}
        }
		
	}
	
	
	
	
	
}

?>