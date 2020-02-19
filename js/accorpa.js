/*
Status:
	0	categoria pura
	1	accorpata eliminata
	2	accorpata principale
	3	separata 
	4	in accorpamento
*/
var inAccorpamento = -1;
var trAccorpa;

function Categoria(id, nome, num, status) {
	this.id = id;
	this.nome = nome;
	this.num = num;
	this.tot = num;
	this.status = status;
	this.accorpate = new Array();
	this.getNum = getNum;
}

function setStatus(idc, status) {
	var c = cats[idc];
	if (c.status == status) return;
	var r = document.getElementById("cat_"+idc);
	c.status = status;
	//cambia stile
	r.className=r.className.replace(/status\d/,"status"+status);
}

function getNum() {
	if (this.status == 2)
		return this.num + " (" + this.tot + ")";
	else
		return this.num;
}

function logoClick(idc) {
	var c = cats[idc];
		switch (c.status) {
		case 1: //acc eliminata
			txt = testoSpostata() + "<br>\n" + c.incat.nome;
			break;
		case 2: //acc principale
			txt = testoRaggruppate() + ":<br>\n";
			for(i in c.accorpate)
				txt += c.accorpate[i].nome+"<br>\n";
			break;
		case 3:
			numcat = Math.ceil(c.num/16);
			txt = testoSepara(numcat, c.num, Math.round(c.num/numcat));
			break;
		default:
			return;
	}
	showPopup(txt);
}

function mostraAccorpa(idc) {
	//solo le categorie pure possono accorparsi
	if (cats[idc].status != 0) return;
	nascondiAccorpamento(true);
	
	//cerca la riga
	var trcat = document.getElementById("cat_"+idc);
	var next = trcat.nextSibling;
	//genera le righe 	
	trAccorpa = new Array();
	inAccorpamento = idc;
	for (i in cats[idc].vicine) {
		var ac = cats[idc].vicine[i];
		var catok = (ac.status == 0 || ac.status == 2);
		var tr = document.createElement("tr");
		tr.className = trcat.className;
		var td = tr.insertCell(-1);
		td.innerHTML = '<a href="riepilogosq.php'+location.search+'&orig#cat'+ac.id+'" target="_blank">'
			+ ac.nome + '</a>';
		//if (!catok)
		//	td.style.textDecoration = "line-through";
		td = tr.insertCell(-1);
		td.className = "logocat logost" + ac.status;
		td.setAttribute("onclick", "logoClick('"+ac.id+"')");
		td = tr.insertCell(-1);
		td.innerHTML = ac.getNum();
		td = tr.insertCell(-1);
		if (catok)
			td.innerHTML = "<a class=\"smallBut\" href=\"javascript:accorpa('"+idc+"','"+ac.id+"');\">"+testoSeleziona()+"</a>";
		//inserisce la riga nella tabella
		trAccorpa.push(tr);
		if (next == null)
			trcat.parentNode.appendChild(tr);
		else
			next.parentNode.insertBefore(tr, next);
	}
	setStatus(idc, 4);
}

function nascondiAccorpamento(cambiaStatus) {
	if (inAccorpamento == -1) return;
	if (cambiaStatus)
		setStatus(inAccorpamento,0);
	inAccorpamento = -1;
	for(i in trAccorpa) {
		trAccorpa[i].parentNode.removeChild(trAccorpa[i]);
	}
}

/**
 * Inserisce la categoria ids nella categoria idd
 */
function accorpa(ids, idd) {
	var cs = cats[ids];
	var cd = cats[idd];
	cs.incat = cd;
	cd.accorpate.push(cs);
	nascondiAccorpamento(false);
	setStatus(ids, 1);
	setStatus(idd, 2);
	cd.tot += cs.num;
	//cambia il totale
	var n = document.getElementById("numcat_"+idd);
	n.innerHTML = cd.getNum();
	//imposta il valore
	var f = document.getElementById("campoacc_"+ids);
	f.value = idd;
}

function separa(idc) {
	var c = cats[idc];
	//solo le categorie pure possono accorparsi
	if (c.status != 0) return;
	setStatus(idc, 3);
	//imposta il valore
	var f = document.getElementById("camposep_"+idc);
	f.value = 1;
	nascondiAccorpamento(true);
}

function annulla(idc) {
	var c = cats[idc];
	switch(c.status) {
		case 1:
			var acc = c.incat.accorpate;
			if (acc.length == 1) {
				c.incat.accorpate = new Array();
				setStatus(c.incat.id,0);
			} else {
				acc.splice(acc.indexOf(c),1);
			}
			c.incat.tot -= c.num;
			var n = document.getElementById("numcat_"+c.incat.id);
			n.innerHTML = c.incat.getNum();
			c.incat = null;
			var f = document.getElementById("campoacc_"+idc);
			f.value = "";
			break;
		case 3:
			var f = document.getElementById("camposep_"+idc);
			f.value = 0;
			nascondiAccorpamento(true);
			break;
		case 4:
			nascondiAccorpamento(false);
			break;
		default:
			return;
	}
	setStatus(idc, 0);
}