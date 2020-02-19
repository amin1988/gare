<?php
if (!defined("_BASEDIR_")) exit();

class Header {
	/**
	 * Qualunque classe con i metodi di TitoloHead
	 * @var TitoloHead
	 */
	private $centro;
	private $logout = true;
	private $stampa = false;
	private $iscrest = false;

	private $indietroUrl = NULL;
	private $indietro = NULL;
	
	/**
	 * @param string $t1
	 * @param string $t2
	 * @return Header
	 */
	public static function titolo($t1, $t2=NULL) {
		if ($t1 == NULL && Menu::caricato())
			$t1 = Lingua::getParola(Menu::getTitolo());
		return new Header(new TitoloHead($t1, $t2));
	}
	
	public function __construct($centro=NULL) {
		if ($centro == NULL)
			$this->centro = new TitoloHead(Lingua::getParola(Menu::getTitolo()));
		else
			$this->centro = $centro;
	}
	
	public function getTitolo() {
		return $this->centro->getTitolo();
	}
	
	public function setLogout($valore) {
		$this->logout = $valore;
	}
	
	public function setIscrEst($valore) {
		$this->iscrest = $valore;
	}
	
	public function getSegnalazione() {
		return $this->logout;
	}
	
	public function getStampa() {
		return $this->stampa;
	}
	
	public function setStampa($valore) {
		$this->stampa = $valore;
	}
	
	public function setIndietro($url, $testo=NULL, $addroot=true) {
		if ($addroot)
			$this->indietroUrl = _PATH_ROOT_ . $url;
		else
			$this->indietroUrl = $url;
		if (is_null($testo)) $testo = Lingua::getParola("indietro");
		$this->indietro = $testo;
	}
	
	public function addIndietro($url, $testo=NULL, $addroot=true) {
		if ($addroot)
			$this->indietroUrl[] = _PATH_ROOT_ . $url;
		else
			$this->indietroUrl[] = $url;
		if (is_null($testo)) $testo = Lingua::getParola("indietro");
		$this->indietro[] = $testo;
	}
	
	/**
	 * @param string $testo
	 * @return boolean false se non c'€ referer
	 */
	public function setIndietroReferer($testo=NULL) {
		if (isset($_SERVER["HTTP_REFERER"]))
			$ref = $_SERVER["HTTP_REFERER"];
		else 
			$ref = "";
		if ($ref == "" || parse_url($ref, PHP_URL_HOST) != $_SERVER["HTTP_HOST"]) {
			$_SESSION["LAST_REFERER"] = "";
			return false;
		}
		if (parse_url($ref, PHP_URL_PATH) == $_SERVER["PHP_SELF"]) {
			if (!isset($_SESSION["LAST_REFERER"]) || $_SESSION["LAST_REFERER"] == "")
				return false;
			$ref = $_SESSION["LAST_REFERER"];
		}
		
		if ($ref == "") return false;
		$_SESSION["LAST_REFERER"] = $ref;
		$this->setIndietro($ref, $testo, false);
		return true;
	}
	
	public function setIndietroHome($utente, $testo=NULL) {
		$this->setIndietro(homeutente($utente,true),$testo);
	}
	
	public function stampaHeader() {

		echo '<div id="head"><div id="logo" style="margin-right:50px">';
		echo '<img src="'._PATH_ROOT_.'img/logo.png" height="96%"></img></div>';

		$this->centro->stampaCentroHead();

		echo '<div class="flags">';

		$lingue = Lingua::getLingue();
		$query = preg_replace('/&?lang=[^&]+/i', '', $_SERVER["QUERY_STRING"]);
		if ($query == "")
			$activeTag = "<a href=\"?lang=";
		else
			$activeTag = "<a href=\"?$query&lang=";
			
		foreach ($lingue as $key_lingua => $lingua) {
		    switch ($key_lingua) {
		        case "it":
		            $flag = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/italy.png\" width='40px'>";
		            $flag_active="<img src=\"" . _PATH_ROOT_ . "css/img/flags/italy_active.png\" width='40px'>";
		            break;
		        case "en":
		            $flag = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/england.png\" width='40px'>";
		            $flag_active = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/england_active.png\" width='40px'>";
		            
		            break;
		        default:
		            $flag = "";
		            $flag_active = "";
		            
		            break;
		    }
		    if ($key_lingua == Lingua::getLinguaDefault()) {
		        echo $flag_active;
		    } else {
		        echo "{$activeTag}$key_lingua\">$flag</a>";
		    }
		   echo "<br>";
		}
		
		echo "</div></div>";
		
		$lang = Lingua::getParole();
		
		echo "<ul id=\"breadcrumb\">";
		if($this->iscrest)
		{
			if(_WKC_MODE_)
				$str = $lang["reg_est_wkc"];
			else 
				$str = $lang["reg_est"];
			
			echo "<li><a href=\""._PATH_ROOT_."regest.php\" title=\"$str\">".$str."</a></li>";
		}
		if ($this->logout) {
			echo "<li><a href=\""._PATH_ROOT_."logout.php\" title=\"Logout\"><img src=\""._PATH_ROOT_."img/exit.png\" alt=\"Logout\" class=\"home\" width='16px'/></a></li>";
			//echo '<a href="'. _PATH_ROOT_.'logout.php">Logout</a><br /><br />';
		}
		
		//Navigazione tra pagine
		$lang = Lingua::getParole();
		//TODO fare solo se indietro è null
		if (Menu::caricato()) { // generazione automatica
				$pag = Menu::getPagina();
				$menu = array();
				while ($pag->getPadre() !== NULL) {
					$pag = Menu::getPagina($pag->getPadre());
					array_unshift($menu, $pag);
				}
				$this->indietro = array();
				$this->indietroUrl = array();
				$path = dirname($_SERVER['PHP_SELF']);
				foreach ($menu as $k => $pag) {
					/* @var $pag PaginaMenu */
					$this->indietro[$k] = $lang[$pag->getTitolo()];
					$url=$pag->getUrl();
					if ($pag->usaQuery())
						$url .= "?$_SERVER[QUERY_STRING]";
					if ($url[0] == '/')
						$this->indietroUrl[$k] = $url;
					else
						$this->indietroUrl[$k] = "$path/$url";
				}
		}
		if (!is_null($this->indietro)) { // impostazione manuale
			if (is_array($this->indietro)) {
				foreach ($this->indietro as $k => $nome) {
					echo "<li><a href=\"{$this->indietroUrl[$k]}\">$nome</a></li> ";
				}
			} else {
				echo "<li><a href=\"$this->indietroUrl\">$this->indietro</a></li> ";
			}
		}
		echo "<li>".$this->centro->getTitolo()."</li>";

		if ($this->getSegnalazione()) {
			//segnalazione errori
			echo "<div style='position:absolute;right:40px;top:3px' class=\"nostampa\">";
			echo "<a href=\"#\" onclick=\"mostraSegnalazione(); return false;\">$lang[segnala_errori]</a>";
			echo "</div>";
		}
		
		if ($this->stampa) {
			echo "<div style='position:absolute;right:10px;top:3px'>";
			echo "<a href=\"$_SERVER[REQUEST_URI]&print\" target='_blank'><img src=\""._PATH_ROOT_."img/icone/printmgr.png \"  width='24px' ></a>";
			echo "</div>";
		}
		echo "</ul>";
		
		if ($this->getSegnalazione()) {
			?>
		<div id="segnalazione" class="nostampa" style="display:none">
		<form id="form_segnalazione" accept-charset="UTF-8" style="display:none" onsubmit="inviaSegnalazione('<?php echo _PATH_ROOT_; ?>'); return false;">
			<?php echo $lang["email"]; ?>: 
			<input type="text" id="email_segnala" value="<?php if (isset($_SESSION["email_segnala"])) echo $_SESSION["email_segnala"]; ?>"><br>
			<?php echo $lang["segnala_descrizione"]; ?>:<br>
			<textarea id="descrizione_segnalazione"></textarea><br>
			<input type="submit" id="invia_segnalazione" value="<?php echo $lang["pulsante_segnala"]; ?>"> 
			<img src="<?php echo _PATH_ROOT_; ?>img/wait_small.gif" id="wait_segnalazione" style="display:none">
		</form>
		<div id="segnalazione_ok"><?php echo $lang["segnala_ok"]; ?></div>
		<div id="segnalazione_no"><?php echo $lang["segnala_no"]; ?></div>
		</div>
			<?php 
		} //if segnalazione
		
		//stampa schede
		$schede = Menu::getSchede();
		if ($schede != NULL && count($schede) > 1) {
			echo '<ul class="schede-pagina">';
			foreach ($schede as $p) {
				/* @var $p PaginaMenu */
				$tit = $lang[$p->getTitolo()];
				if ($p->isAttiva())
					echo "<li class=\"attiva\">$tit</li>"; 
				else {
					$url = $p->getUrl().'?'.$_SERVER["QUERY_STRING"]; 
					echo "<a href=\"$url\"><li>$tit</li></a>";
				}
			}
			echo '</ul>';
		}
	} //function stampaHeader	
}

class TitoloHead {
	private $t1, $t2;
	
	public function __construct($t1, $t2=NULL) {
		$this->t1 = $t1;
		$this->t2 = $t2;
	}
	
	public function stampaCentroHead() {
		echo "<h1>$this->t1</h1>\n";
		if (!is_null($this->t2))
			echo "<h2>$this->t2</h2>\n";
	}
	
	public function getTitolo() {
		return $this->t1;
	}
}
?>