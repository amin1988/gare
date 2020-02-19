<?php
//TODO pagina temporanea
session_start();
require_once("../config.inc.php");
if (!_LOCALHOST_) exit();
include_model("Organizzatore", "GruppoCat", "Categoria");

function leggiData($valore) {
	//formato dd/mm/yyyy
	if (!preg_match('/^(\d\d?)\/(\d\d?)\/(\d{4})$/', trim($valore), $m))
		return NULL;
	return new Data("$m[3]-$m[2]-$m[1]");
}

$ut = Organizzatore::crea();
if (is_null($ut)) nologin();

if (isset($_POST["nome"])) {
	
	$g = new GruppoCat();
	$g->setNome($_POST["nome"]);
	$g->setIndividuale(isset($_POST["indiv"]));
	$g->salva();
	$gid = $g->getChiave();
	
	$cat = array();
	$f = fopen($_FILES['cat']['tmp_name'],'r');
	while ( ($line = fgets($f)) !== false) {
		$cod = explode(",", $line);
		echo "$cod[0],$cod[1]<br>";
		$c = Categoria::parse($cod[0]);
		if (strlen($cod[1])>0)
			$c->setNomeEta($cod[1]);
		$c->setGruppo($gid);
		$c->salva();
		$cat[] = $c;
	}
	fclose($f);
		
// 	$gara = new Gara();
// 	$gara->setNome($_POST["nome"]);
// 	$gara->setDataGara(leggiData($_POST["data"]));
// 	$gara->setChiusura(leggiData($_POST["chiusura"]));
// 	$gara->setCategorieIndiv($cat);
// 	$gara->salva();
	echo "salvato";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<?php 
require_once (_BASEDIR_."/css/font.inc");
?>
<title>%Nuova gara%</title>
</head>

<body>
<h1>%Nuova gara%</h1>
<form accept-charset="UTF-8" action="" method="post" enctype="multipart/form-data" name="form1" id="form1">
    <label for="nome">Nome: </label>
    <input type="text" name="nome" id="nome" />
    <br />
  <input type="checkbox" checked="checked" name="indiv" /><label for="indiv">Individuali</label><br />
  <label for="cat">Categorie:</label>
  <input type="file" name="cat" id="cat" />
  <br />
  <input type="submit" value="Crea" />
</form>
</body>
</html>