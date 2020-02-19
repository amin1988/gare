var timerCinture = null;

function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++){
	x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	x=x.replace(/^\s+|\s+$/g,"");
	if (x==c_name)
		return unescape(y);
	}
}

function cambioCintura(id,start) {
	t = getCookie("cintura");
	if (start <= 0) {
		//prima chiamata
		if (t == null) t = 1;
		if (timerCinture == null)
			timerCinture = setInterval("cambioCintura("+id+","+t+")",10000);
	} else if (t != null && t > start) {
		clearInterval(timerCinture);
		timerCinture = setInterval("cambioCintura("+id+","+t+")",10000);
		ajaxCall(baseUrl()+"ajax/cinture.php", null, setCinture);
	}
}

function setCinture(json, args) {
	if (json == "null") return;
	var res = JSON.parse(json);
	for(var i=0; i < res.length; i++) {
		var el = document.getElementById("cintura_"+res[i].id);
		if (el != null) el.innerHTML = res[i].cintura;
	}
}