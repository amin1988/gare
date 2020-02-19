function mostraSegnalazione() {
	//nasconde messaggio ok e no
	document.getElementById("segnalazione_ok").style.display = "none";
	document.getElementById("segnalazione_no").style.display = "none";
	//abilita pulsante
	document.getElementById("wait_segnalazione").style.display = "none";
	document.getElementById("invia_segnalazione").disabled = false;
	//svuota form
	var f = document.getElementById("form_segnalazione");
	//f.reset();
	document.getElementById("descrizione_segnalazione").value = "";
	//mostra form
	f.style.display = "block";
	//mostra div
	document.getElementById("segnalazione").style.display = "block";
}

function inviaSegnalazione(path) {
	var desc = document.getElementById("descrizione_segnalazione").value;
	desc = desc.replace(/^\s*/, "").replace(/\s*$/, "");
	if (desc == "") return;
	var email = document.getElementById("email_segnala").value;
	document.getElementById("wait_segnalazione").style.display = "inline";
	document.getElementById("invia_segnalazione").disabled = true;
	var param = "pagina="+encodeURIComponent(location.href);
	param += "&email_segnala="+encodeURIComponent(email);
	param += "&descrizione="+encodeURIComponent(desc);
	ajaxPOSTCall(path+"ajax/segnala.php", param, null, callbackSegnala);
}

function callbackSegnala(responseText, args) {
	if (responseText == 1) {
		//nasconde form
		document.getElementById("form_segnalazione").style.display = "none";
		//nasconde messaggio errore
		document.getElementById("segnalazione_no").style.display = "none";	
		//mostra messaggio ok
		document.getElementById("segnalazione_ok").style.display = "block";
	} else {
		//mostra messaggio ok
		document.getElementById("segnalazione_ok").style.display = "none";
		//mostra messaggio errore
		document.getElementById("segnalazione_no").style.display = "block";		
		//abilita pulsante
		document.getElementById("wait_segnalazione").style.display = "none";
		document.getElementById("invia_segnalazione").disabled = false;
	}
}