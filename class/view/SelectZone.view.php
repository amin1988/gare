<?php
if (!defined("_BASEDIR_")) exit();
include_model("Zona","LivelloZona");

class SelectZone {
	/**
	 * @var LivelloZona
	 */
	private $livtop;
	/**
	 * @var Zona[]
	 */
	private $zonetop;
	
	/**
	 * @var int[]
	 */
	private $zonesel = NULL;
	/**
	 * @var LivelloZona[]
	 */
	private $livsel = NULL;
	/**
	 * @vat int
	 */
	private $prof = 0;
	
	/**
	 * @var boolean
	 */
	private $required;
	
	/**
	 * @param boolean $required true se è un campo obbligatorio
	 * @param boolean $topSingolo true per mostrare il primo elemento anche se 
	 * è formato da un solo elemento
	 */
	public function __construct($required=false, $topSingolo=false){
		$this->required = $required;
		$this->livtop = LivelloZona::getPrimoLivello();
		$this->zonetop = Zona::listaLivello($this->livtop->getChiave());
		
		if (!$topSingolo && count($this->zonetop) == 1) {
			/* @var $zt Zona */
			$zt = array_pop($this->zonetop);
			$this->zonetop = Zona::getSottozone($zt->getChiave());
			foreach ($this->zonetop as $z){
				$this->livtop = new LivelloZona($z->getLivello());
				break;
			}
		}
	}
	
	/**
	 * Imposta una zona preselezionata
	 * @param int $idzona
	 * @param int $prof profondità del select rispetto al contenitore 
	 * (1 se il select è figlio diretto)
	 */
	public function setZona($idzona,$prof) {
		$zona = Zona::getZona($idzona);
		while ($zona->getLivello() != $this->livtop->getChiave()) {
			$tmp[] = $zona;
			$zona = Zona::getZona($zona->getPadre());
		}
		$tmp[] = $zona;
		$i = 0;
		while(count($tmp) > 0) {
			/* @var $zona Zona */
			$zona = array_pop($tmp);
			$this->zonesel[$i] = $zona->getChiave();
			if ($i > 0)
				$this->livsel[$i] = new LivelloZona($zona->getLivello());
			$i++; 
		}
		//controlla se ci sono zone sotto quella selezionata
		$subsel = Zona::getSottozone($idzona);
		if (count($subsel) > 0){
			$this->zonesel[$i] = 0;
			$this->livsel[$i] = new LivelloZona(array_pop($subsel)->getLivello());
		}
		$this->prof = $prof;
	}
	
	/**
	 * Richiede una fuzione javascript mostraZone(nome, sel, el, val);<br>
	 * nome: nome del livello<br>
	 * sel: oggetto select con le zone<br>
	 * el: il padre del select modificato<br>
	 * val: il valore selezionato<br>
	 * liv: il livello della nuova lista
	 */
	public function stampaJavascript() { ?>
<script type="text/javascript">
var zonecont = new Array();
var lastreq;

function cambiaZona(liv, el) {
	el.disabled=true;
	var wait = document.createElement("img");
	wait.src = "<?php echo _PATH_ROOT_."img/wait_small.gif"; ?>";
	wait.className = "wait";
	el.parentNode.insertBefore(wait,el.nextSibling);
	lastreq = el.value;
	while(zonecont.length > liv) {
		var del = zonecont.pop();
		del.parentNode.removeChild(del);
	}
	if (el.value == "") {
		wait.parentNode.removeChild(wait);
		el.disabled=false;
		return;
	}
	var xmlhttp;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		  caricaZone(JSON.parse(xmlhttp.responseText), el, liv, wait);
	};
	
	xmlhttp.open("GET","<?php echo _PATH_ROOT_; ?>ajax/sottozone.php?id="+el.value,true);
	xmlhttp.send();
}

function caricaZone(res, el, liv, wait) {
	if (res != null) {
		//genera il select
		var sel = document.createElement("select");
		<?php 
		if ($this->required) echo 'sel.setAttribute("required","required");';
		?>
		sel.id="subzone_"+el.value;
		sel.setAttribute("onchange","cambiaZona("+(zonecont.length+1)+", this)");
		try { // for IE earlier than version 8
			sel.add(document.createElement("option"),sel.options[null]);
		} catch (e) {
		  sel.add(document.createElement("option"),null);
		}
		for (z in res.zone) {
			opt = document.createElement("option");
			opt.value = res.zone[z].id;
			opt.text = res.zone[z].nome;
			try { // for IE earlier than version 8
				sel.add(opt,sel.options[null]);
			} catch (e) {
			  sel.add(opt,null);
			}
		}
		
		var container = mostraZone(res.liv, sel, el.parentNode, el.value, liv+1);
		zonecont.push(container);
	}
	wait.parentNode.removeChild(wait);
	el.disabled=false;
}
<?php
if (!is_null($this->zonesel)) { 
	echo "function preloadZone() {\n";
	$max = count($this->zonesel)-1;
	$parent = "";
	for($i=0; $i<$this->prof; $i++) $parent .= ".parentNode";
		
	for($i = 0; $i < $max; $i++) {
		$idp = $this->zonesel[$i];
		echo "zonecont.push(document.getElementById('subzone_$idp')$parent);\n";
	}
	echo "}\n";
?>

if(window.onload) {
    var curronload = window.onload;
    var newonload = function() {
        curronload();
        preloadZone();
    };
    window.onload = newonload;
} else {
    window.onload = preloadZone;
}
<?php } //if zonesel != null ?>
</script>	
	<?php 	
	} //function stampaJavascript
	
	public function getNumSubzone() {
		if (is_null($this->zonesel)) return 0;
		return count($this->zonesel)-1;
	}
	
	public function getNomeLivelloTop() {
		return $this->livtop->getNome();
	}
	
	public function getNomeLivelloSub($livello) {
		if (!isset($this->livsel[$livello])) return "";
		return $this->livsel[$livello]->getNome();
	}
	
	public function stampaSelectTop($name=NULL) {
		if (isset($this->zonesel[0])) $sel = $this->zonesel[0];
		else $sel = -1;
		$this->stampaSelect("top", 0, $this->zonetop, $sel, $name);
	}
	
	public function stampaSelectSub($livello, $name=NULL) {
		if ($livello == 0 || !isset($this->zonesel[$livello])) return;
		$ids = $this->zonesel[$livello];
		$idp = $this->zonesel[$livello-1];
		$this->stampaSelect($idp, $livello, Zona::getSottozone($idp), $ids, $name);
	}
	
	private function stampaSelect($id, $lv, $lista, $sel, $name) {
		echo "<select id=\"subzone_$id\" ";
		if ($this->required) echo 'required="required" ';
		if (!is_null($name)) echo "name=\"$name\" ";
		echo "onchange=\"cambiaZona($lv, this)\">";
		echo '<option value=""></option>';
		foreach ($lista as $idz => $z) {
			echo "<option value=\"$idz\"";
			if ($idz == $sel) echo ' selected="selected"';
			echo ">".$z->getNome()."</option>\n";
		}
		echo "</select>\n";
	}
}

?>