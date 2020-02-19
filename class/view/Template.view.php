<?php
if (!defined("_BASEDIR_")) exit();
include_view("Header");
include_menu();

class Template {
	const CHECKBOX = "checkbox";
	const CALENDAR = "calendar";
	
	/**
	 * @var Header
	 */
	private $header;
	
	/**
	 * @var string[]
	 */
	private $js;
	/**
	 * @var boolean
	 */
	private $checkbox = false;
	/**
	 * @var boolean
	 */
	private $calendar = false;
	/**
	 * @var boolean
	 */
	private $popup = false;
	/**
	 * @var boolean
	 */
	private $bodyDiv = true;
	
	/**
	 * @param string $t1
	 * @param string $t2
	 * @return Template
	 */
	public static function titolo($t1, $t2=NULL) {
		return new Template(Header::titolo($t1, $t2));
	}
	
	public function __construct($header=NULL) {
		if ($header == NULL)
			$this->header = new Header();
		else
			$this->header = $header;
		$this->js=array();
	}
	
	/**
	 * @param boolean $value
	 */
	public function setBodyDiv($value) {
		$this->bodyDiv = $value;
	}
	
	public function includeJs() {
		foreach(func_get_args() as $ijs) {
			if (strcmp($ijs, self::CHECKBOX) == 0)
				$this->checkbox = true;
			elseif (strcmp($ijs, self::CALENDAR) == 0)
				$this->calendar = true;
			else {
				if (strcmp($ijs,"popup") == 0)
					$this->popup = true;
				$this->js[$ijs] = $ijs;
			}
		}
	}
	
	/**
	 * Stampa il contenuto del tag head
	 * @param boolean $chiudi false per lasciare il tag head aperto 
	 */
	public function stampaTagHead($chiudi=true) {
	
	$ver = file_get_contents(_BASEDIR_ . "webver.txt");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="<?php echo _PATH_ROOT_; ?>css/errori.css?v=<?php echo $ver;?>" rel="stylesheet" type="text/css" />
<link href="<?php echo _PATH_ROOT_; ?>css/common.css?v=<?php echo $ver;?>" rel="stylesheet" type="text/css" />
<link rel="icon" href="<?php echo _PATH_ROOT_; ?>img/logo.png" type="image/png"/>
<?php 

if ($this->header->getStampa()) {
	echo '<link  href="'. _PATH_ROOT_."css/print.css?v=$ver\" rel=\"stylesheet\" type=\"text/css\" media=\"print\" />";
}
if ($this->header->getStampa() && isset($_GET["print"])) {
	echo '<link  href="'. _PATH_ROOT_."css/print.css?v=$ver\" rel=\"stylesheet\" type=\"text/css\" />";
	echo '<script type="text/javascript">window.print()</script>';
} else {
	echo '<link  href="'. _PATH_ROOT_."css/screen.css?v=$ver\" rel=\"stylesheet\" type=\"text/css\" />";
}

require_once (_BASEDIR_."/css/font.inc");

$this->js["ajax"] = "ajax";
if ($this->header->getSegnalazione()) {
	$this->js["segnala"] = "segnala";
}

foreach ($this->js as $script) {
	echo "<script type=\"text/javascript\" src=\""._PATH_ROOT_."js/$script.js?v=$ver\"></script>\n";
}


if ($this->checkbox) {
	echo '<script type="text/javascript" src="'._PATH_ROOT_.'css/check_radio/custom-form-elements.js"></script>';
}
if ($this->calendar) {
	echo '<link rel="stylesheet" type="text/css" href="'._PATH_ROOT_.'calendario/css/calendar-eightysix-v1.1-default.css" media="screen" />';
	$files = array("mootools-1.2.4-core","mootools-1.2.4.4-more","calendar-eightysix-v1.1","moolang");
	foreach ($files as $f) {
		echo '<script type="text/javascript" src="'._PATH_ROOT_.'calendario/js/'.$f.'.js"></script>';
	}
}
?>
<script type="text/javascript">
var sessione=true;
//mantiene attiva la sessione
function tieniSessione() {
	ajaxCall("<?php echo _PATH_ROOT_; ?>ajax/sess.php", null, checkSessione);
}

function checkSessione(valore, args) {
	if (valore != 1) sessione = false;
}
setInterval(tieniSessione,300000);
</script>
<title><?php echo $this->header->getTitolo(); ?></title>

<?php 
		if ($chiudi) echo "</head>\n\n";
	} //function stampaTagHead
	
	/**
	 * Stampa la parte iniziale della pagina
	 * @param $prop eventuali proprietà del tag body
	 */
	public function apriBody($prop=NULL) {
?>
<body <?php if (!is_null($prop)) echo $prop; ?>>
<?php if ($this->popup) {?>
<script type="text/javascript">writePopup();</script>
<?php } //if popup ?>
<div align="center">
<div id="box">
<?php 
		$this->header->stampaHeader();
		if ($this->bodyDiv)
			echo '<div id="form_login">';
	} //function apriBody
	
	/**
	 * Stampa la parte finale della pagina
	 */
	public function chiudiBody() {
?>
<br /><br />
<?php 
if ($this->bodyDiv) echo '</div>';
?>
</div>
</div>
</body>
</html>
<?php 
	} //function chiudiBody
}

?>