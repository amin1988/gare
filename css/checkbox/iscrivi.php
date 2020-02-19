<?php
//STAMPA
session_start();

require_once("../../config.inc.php");
include_controller("iscrivi");
include_view("iscrivi");

$ctrl = new Iscrivi();
$view = new IscriviView($ctrl);

$lang = Lingua::getParole();

$nuoviCampi = $ctrl->nuoviCampi();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<!-- CHECKBOX -->
<link rel="stylesheet" type="text/css" href="demo.css" />
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="customInput.jquery.js"></script>

<script type="text/javascript">
	// Run the script on DOM ready:
	$(function(){
		$('input').customInput();
	});
	</script>	
<!-- FINE CHECKBOX -->
<title><?php echo $lang["iscrizioni_titolo"]; ?></title>
<?php if ($ctrl->puoAggiungereCampi()) { ?>
<script type="text/javascript">
<!--
var newid = <?php echo $nuoviCampi; ?>;
var numriga = <?php echo $ctrl->numRigaJs(); ?>;

function aggiungiCampi() {
	var t = document.getElementById('atleti');
	for(i=0; i<10; i++){
		var node=document.createElement("tr");
		node.innerHTML=	creaRiga(newid);
		numriga++;
		node.className="riga"+numriga;
		newid++;
		numriga = numriga % 2;
		t.appendChild(node);
	}
}

function creaRiga(id) {
	return '<?php $view->stampaRigaAtleta(new JavascriptFieldFiller($ctrl)) ?>';
}
-->
</script>
<?php } //if puoAggiungereCampi ?>
<style>
tr.err {
background-color: yellow;
}

.err {
background-color: red;
}

</style>
</head>

<body>

<div align="center">
<div id="box">
<div id="head">
<div id="logo" style="margin-right:100px"> <img src="/fiam_gare/img/logo.png" height="96%" ></img> </div>

<h1>Iscrizioni gara</h1>

<h2>Prima gara</h2>

</div>

<div id="form_login">
<form accept-charset="UTF-8" action="/fiam_gare/css/checkbox/iscrivi.php?id=1" method="post">
	<input type="hidden" name="pageid" value="bc620af53fc41307782e3b6b4e44fae5" />
	
	<div class="pulsante">
	<input type="submit" />
</div>
<br></br>
<table  id="atleti">
<tr style="background:#990000;">
<th></th>
<th><div class='thAtleti'>Nome</div></th>
<th><div class='thAtleti'>Cognome</div></th>
<th><div class="thAtleti" >Sesso</div></th>
<th><div class='thAtleti'>Data di Nascita</div></th>
<th><div class='thAtleti'>Cintura</div></th>
<th><div class="thAtleti" >Kata</div></th>
<th><div class="thAtleti">Sanbon</div></th>
<th><div class="thAtleti">Ippon</div></th>
<th><div class='thAtleti'>Stile</div></th>
<th><div class='thAtleti'>Peso</div></th>


</tr>



<tr class="riga1">


<td class="iscr_check">

<input id="prova" type="checkbox" name="check[13]" value="13" checked="checked" />
<label for="prova"></label>
</td>


<td class="cognome">Atkinson</td><td class="nome">Sharon</td><td class="sesso">F</td><td class="nascita">14/04/1996</td><td class="cinture"><select name="cintura[13]"><option value="1" selected="selected">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td>

<td class="tipo"><input id="tipo_13_0" type="checkbox" name="tipo[13][0]" value="0" checked="checked" alt="Kata" /><label for="tipo_13_0"></label></td>
<td class="tipo"><input id="tipo_13_1" type="checkbox" name="tipo[13][1]" value="1" disabled="disabled" alt="Sanbon" /><label for="tipo_13_1"></label></td>
<td class="tipo"><input id="tipo_13_2" type="checkbox" name="tipo[13][2]" value="2" disabled="disabled" alt="Ippon" /><label for="tipo_13_2"></label></td>


<td class="stile"><select name="stile[13]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[13]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="check[20]" value="20" checked="checked" /></td><td class="cognome">Bell</td><td class="nome">Desirae</td><td class="sesso">F</td><td class="nascita">14/06/1993</td><td class="cinture"><select name="cintura[20]"><option value="1" selected="selected">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[20][0]" value="0" checked="checked" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[20][1]" value="1" disabled="disabled" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[20][2]" value="2" disabled="disabled" alt="Ippon" /></td><td class="stile"><select name="stile[20]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[20]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="check[36]" value="36" /></td><td class="cognome">d</td><td class="nome">gr</td><td class="sesso">M</td><td class="nascita">04/02/1998</td><td class="cinture"><select name="cintura[36]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[36][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[36][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[36][2]" value="2" disabled="disabled" alt="Ippon" /></td><td class="stile"><select name="stile[36]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[36]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="check[5]" value="5" checked="checked" /></td><td class="cognome">Gomez</td><td class="nome">Henry</td><td class="sesso">M</td><td class="nascita">12/10/1979</td><td class="cinture"><select name="cintura[5]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[5][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[5][1]" value="1" checked="checked" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[5][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="stile[5]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[5]" value="65"/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="check[39]" value="39" checked="checked" /></td><td class="cognome">new</td><td class="nome">atl</td><td class="sesso">F</td><td class="nascita">04/02/1998</td><td class="cinture"><select name="cintura[39]"><option value="1" selected="selected">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[39][0]" value="0" checked="checked" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[39][1]" value="1" disabled="disabled" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[39][2]" value="2" disabled="disabled" alt="Ippon" /></td><td class="stile"><select name="stile[39]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[39]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="check[3]" value="3" /></td><td class="cognome">Newman</td><td class="nome">Adam</td><td class="sesso">M</td><td class="nascita">17/06/1978</td><td class="cinture"><select name="cintura[3]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[3][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[3][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[3][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="stile[3]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[3]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="check[6]" value="6" /></td><td class="cognome">Porter</td><td class="nome">Preston</td><td class="sesso">M</td><td class="nascita">26/02/1997</td><td class="cinture"><select name="cintura[6]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[6][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[6][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[6][2]" value="2" disabled="disabled" alt="Ippon" /></td><td class="stile"><select name="stile[6]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[6]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="check[37]" value="37" checked="checked" /></td><td class="cognome">rossi</td><td class="nome">mario</td><td class="sesso">M</td><td class="nascita">12/10/1979</td><td class="cinture"><select name="cintura[37]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[37][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[37][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[37][2]" value="2" checked="checked" alt="Ippon" /></td><td class="stile"><select name="stile[37]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[37]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="check[38]" value="38" checked="checked" /></td><td class="cognome">verdi</td><td class="nome">luigi</td><td class="sesso">M</td><td class="nascita">04/08/1980</td><td class="cinture"><select name="cintura[38]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5" selected="selected">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[38][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[38][1]" value="1" checked="checked" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[38][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="stile[38]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[38]" value="60"/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="check[2]" value="2" /></td><td class="cognome">Weeks</td><td class="nome">Connor</td><td class="sesso">M</td><td class="nascita">04/02/1998</td><td class="cinture"><select name="cintura[2]"><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6" selected="selected">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="tipo[2][0]" value="0" disabled="disabled" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="tipo[2][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="tipo[2][2]" value="2" disabled="disabled" alt="Ippon" /></td><td class="stile"><select name="stile[2]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" size='3' name="peso[2]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="newcheck[0]" value="0" /></td><td class="cognome"><input type="text" name="cognome[0]" value=""/></td><td class="nome"><input type="text" name="nome[0]" value=""/></td><td class="sesso"><select name="sesso[0]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[0]" value=""/></td><td class="cinture"><select name="newcintura[0]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[0][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[0][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[0][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[0]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[0]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="newcheck[1]" value="1" /></td><td class="cognome"><input type="text" name="cognome[1]" value=""/></td><td class="nome"><input type="text" name="nome[1]" value=""/></td><td class="sesso"><select name="sesso[1]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[1]" value=""/></td><td class="cinture"><select name="newcintura[1]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[1][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[1][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[1][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[1]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[1]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="newcheck[2]" value="2" /></td><td class="cognome"><input type="text" name="cognome[2]" value=""/></td><td class="nome"><input type="text" name="nome[2]" value=""/></td><td class="sesso"><select name="sesso[2]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[2]" value=""/></td><td class="cinture"><select name="newcintura[2]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[2][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[2][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[2][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[2]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[2]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="newcheck[3]" value="3" /></td><td class="cognome"><input type="text" name="cognome[3]" value=""/></td><td class="nome"><input type="text" name="nome[3]" value=""/></td><td class="sesso"><select name="sesso[3]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[3]" value=""/></td><td class="cinture"><select name="newcintura[3]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[3][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[3][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[3][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[3]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[3]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="newcheck[4]" value="4" /></td><td class="cognome"><input type="text" name="cognome[4]" value=""/></td><td class="nome"><input type="text" name="nome[4]" value=""/></td><td class="sesso"><select name="sesso[4]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[4]" value=""/></td><td class="cinture"><select name="newcintura[4]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[4][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[4][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[4][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[4]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[4]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="newcheck[5]" value="5" /></td><td class="cognome"><input type="text" name="cognome[5]" value=""/></td><td class="nome"><input type="text" name="nome[5]" value=""/></td><td class="sesso"><select name="sesso[5]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[5]" value=""/></td><td class="cinture"><select name="newcintura[5]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[5][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[5][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[5][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[5]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[5]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="newcheck[6]" value="6" /></td><td class="cognome"><input type="text" name="cognome[6]" value=""/></td><td class="nome"><input type="text" name="nome[6]" value=""/></td><td class="sesso"><select name="sesso[6]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[6]" value=""/></td><td class="cinture"><select name="newcintura[6]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[6][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[6][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[6][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[6]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[6]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="newcheck[7]" value="7" /></td><td class="cognome"><input type="text" name="cognome[7]" value=""/></td><td class="nome"><input type="text" name="nome[7]" value=""/></td><td class="sesso"><select name="sesso[7]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[7]" value=""/></td><td class="cinture"><select name="newcintura[7]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[7][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[7][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[7][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[7]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[7]" value=""/></td></tr>
<tr class="riga1"><td class="iscr_check"><input type="checkbox" name="newcheck[8]" value="8" /></td><td class="cognome"><input type="text" name="cognome[8]" value=""/></td><td class="nome"><input type="text" name="nome[8]" value=""/></td><td class="sesso"><select name="sesso[8]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[8]" value=""/></td><td class="cinture"><select name="newcintura[8]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[8][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[8][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[8][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[8]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[8]" value=""/></td></tr>
<tr class="riga2"><td class="iscr_check"><input type="checkbox" name="newcheck[9]" value="9" /></td><td class="cognome"><input type="text" name="cognome[9]" value=""/></td><td class="nome"><input type="text" name="nome[9]" value=""/></td><td class="sesso"><select name="sesso[9]"><option value="0"></option><option value="1">M</option><option value="2">F</option></select></td><td class="nascita"><input type="text" name="nascita[9]" value=""/></td><td class="cinture"><select name="newcintura[9]"><option value="-1"></option><option value="1">Bianca</option><option value="2">Gialla</option><option value="3">Arancio</option><option value="4">Verde</option><option value="5">Blu</option><option value="6">Marrone</option><option value="7">Nera</option></select></td><td class="tipo"><input type="checkbox" name="newtipo[9][0]" value="0" alt="Kata" /></td><td class="tipo"><input type="checkbox" name="newtipo[9][1]" value="1" alt="Sanbon" /></td><td class="tipo"><input type="checkbox" name="newtipo[9][2]" value="2" alt="Ippon" /></td><td class="stile"><select name="newstile[9]"><option value="1">Gojuryu</option><option value="2" selected="selected">Shitoryu</option><option value="3">Shotokan</option><option value="4">Wadoryu</option></select></td><td class="peso"><input type="text" name="newpeso[9]" value=""/></td></tr>
</table>
<div class="pulsante" style="text-align:left">
<input type="button" onclick="aggiungiCampi();" value="Aggiungi nuovi campi" />
<br /><br>
</div>
<div class="pulsante" style="text-align:center">

	<input type="submit" />
	</div>
	
		<fieldset>
		<legend>Which genres do you like?</legend>
		<input type="checkbox" name="genre" id="check-1" value="action" />
		<label for="check-1">Action / Adventure</label>
		
		<input type="checkbox" name="genre" id="check-2" value="comedy" />

		<label for="check-2">Comedy</label>
		
		<input type="checkbox" name="genre" id="check-3" value="epic" />
		<label for="check-3">Epic / Historical</label>
		
		<input type="checkbox" name="genre" id="check-4" value="science" />
		<label for="check-4">Science Fiction</label>
		
		<input type="checkbox" name="genre" id="check-5" value="romance" />
		<label for="check-5">Romance</label>

		
		<input type="checkbox" name="genre" id="check-6" value="western" />
		<label for="check-6">Western</label>
	</fieldset>
		
	<fieldset>
		<legend>Caddyshack is the greatest movie of all time, right?</legend>
		<input type="radio" name="opinions" id="radio-1" value="1" />
		<label for="radio-1">Totally</label>

		
		<input type="radio" name="opinions" id="radio-2" value="1" />
		<label for="radio-2">You must be kidding</label>
		
		<input type="radio" name="opinions" id="radio-3" value="1" />
		<label for="radio-3">What's Caddyshack?</label>
	</fieldset>
</form>
<br></br>
</div>
</div>
</div>
</body>
</html>