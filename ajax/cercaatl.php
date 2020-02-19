<?php
if (!isset($_GET["ricerca"])) exit("[]");
if (!isset($_GET["idg"])) exit("[]");
if (!isset($_GET["ids"])) exit("[]");

if (!is_numeric($_GET["idg"])) exit("[]");
if (!is_numeric($_GET["ids"])) exit("[]");
if (trim($_GET["ricerca"]) == "") exit("[]");

$idg = intval($_GET["idg"]);
$ids = intval($_GET["ids"]);
$tipo = intval($_GET["tipo"]);
if (isset($_GET["idsq"]))
	$idsq = intval($_GET["idsq"]);
else 
	$idsq = NULL;

session_start();
require_once '../config.inc.php';
include_model("UtSocieta");
include_esterni("AtletaAffiliato");

$ut = UtSocieta::crea();
if (is_null($ut)) exit("[]");

echo "[";
$primo = true;
foreach (AtletaAffiliato::ricerca($_GET["ricerca"], $ids) as $as) {
	try {
		/* @var $a Atleta */
		$a = $as["atl"];
		$ida = $a->getChiave();
		$av["nome"] = $a->getNome();
		$av["cognome"] = $a->getCognome();
		$av["sesso"] = Sesso::toStringBreve($a->getSesso());
		$av["nascita"] = $a->getDataNascita()->format('d\/m\/Y');
		$cin = Cintura::getCintura($a->getCintura());
		if (is_null($cin)) continue;
		$av["cintura"] = $cin->getNome();
		$av["ids"] = $a->getSocieta();
		if (!$a->isVerificato())
			$av["stato"] = 0;
		else {
			//verifica che l'atleta non sia in un'altra squadra
			/* @var $conn Connessione */
			$conn = $GLOBALS["connint"];
			$conn->connetti();
			
			$where = "idgara = '$idg' AND idatleta = '$ida' AND tipogara = '$tipo'";
			if ($idsq !== NULL)
				$where .= " AND s.idsquadra != '$idsq'";
			
			//TODO ottimizzare mettendo tipo gara in squadre?
			$mr = $conn->select("squadre s INNER JOIN componentisquadre c USING(idsquadra)".
					"INNER JOIN categorie USING(idcategoria)", 
					"$where LIMIT 1",
					 "idsocieta");
			if ($mr->fetch_row())
				$av["stato"] = 0; //già iscritto
			else
				$av["stato"] = 1;
		}
			
		//$res[] = $av;
	
		if ($primo) $primo=false;
		else echo ",";
		echo "{\"id\":\"$ida\",\"cognome\":\"$av[cognome]\",\"nome\":\"$av[nome]\",\"sesso\":\"$av[sesso]\",";
		echo "\"nascita\":\"$av[nascita]\",\"cintura\":\"$av[cintura]\",";
		echo "\"idsoc\":\"$av[ids]\",\"societa\":\"$as[soc]\",\"stato\":\"$av[stato]\"}";
	} catch (Exception $e) {}
}
echo "]";

// echo json_encode($res);
exit();

?>