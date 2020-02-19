var confirmCallback=null;

function writePopup(ok, annulla, okconf, annlist) {
	if (ok == undefined) ok = "OK";
	if (annulla == undefined) annulla = "Annulla";
	if (okconf == undefined) okconf = ok;
	if (annlist == undefined) annlist = annulla;
	document.write('<div id="popup"><div><p id="popup_content"></p>');
	document.write('<span class="button" id="popup-button_alert">');
	document.write('<a href="javascript:closePopup()">'+ok+'</a></span>');
	document.write('<span class="button" id="popup-button_list">');
	document.write('<a href="javascript:closePopup()">'+annlist+'</a></span>');
	document.write('<span class="button" id="popup-button_confirm">');
	document.write('<a href="javascript:closeConfirm(1)">'+okconf+'</a> ');
	document.write('<a href="javascript:closeConfirm(0)">'+annulla+'</a>');
	document.write('</span></div></div>');


}

function setPopupContent(content) {
	el = document.getElementById("popup_content");
	el.innerHTML = content;
}

function showPopupBut(show) {
	but = ["alert", "confirm", "list"];
	for (x in but) {
		el = document.getElementById("popup-button_"+but[x]);
		if (but[x] == show)
			el.style.display = "inline";
		else
			el.style.display = "none";
	}
}

//##########################       FINESTRA ALERT       ############################################

function showPopup(content) {
	if (content != undefined)
		setPopupContent(content);
	showPopupBut("alert");
	el = document.getElementById("popup");
	el.style.visibility = "visible";
}

function closePopup() {
	el = document.getElementById("popup");
	el.style.visibility = "hidden";
	confirmCallback = null;
}

//##########################       FINESTRA CONFIRM      ############################################

function showConfirm(content, callback) {
	if (content != undefined)
		setPopupContent(content);
	confirmCallback = callback;
	showPopupBut("confirm");
	el = document.getElementById("popup");
	el.style.visibility = "visible";
}

function closeConfirm(val) {
	var cbk = confirmCallback;
	closePopup();
	if (cbk != null)
		cbk(val);
}

//##########################       FINESTRA LISTA       ############################################

function showList(text, labels, links) {
	cont = text+"<br><span class=\"button\">";
	for (i in labels) {
		cont += "<a href=\""+links[i]+"\" onclick=\"closePopup()\">";
		cont += labels[i] + "</a><br>";
	}
	cont += "</span>";
	setPopupContent(cont);
	showPopupBut("list");
	el = document.getElementById("popup");
	el.style.visibility = "visible";	
}