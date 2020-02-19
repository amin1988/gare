/**
 * Esegue una chiamata AJAX e chiama una funzione di callback
 * @param url l'url da caricare con la chiamata
 * @param args gli argomenti da passare alal funzione di callback
 * @param callback la funzione di callback con argomenti (responseText, args)
 */
function ajaxCall(url, args, callback) {
	var xmlhttp = createAjaxObject(args, callback);
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}

function ajaxPOSTCall(url, body, args, callback) {
	var xmlhttp = createAjaxObject(args, callback);
	xmlhttp.open("POST",url,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", body.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(body);
} 

function createAjaxObject(args, callback) {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {
		// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function() {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		  callback(xmlhttp.responseText, args);
	};
	return xmlhttp;
}