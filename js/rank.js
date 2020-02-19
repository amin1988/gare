
function hidePoints() {
	var ALL = document.getElementsByClassName("punti_ALL");
	for (var i=0; i<ALL.length; i++)
	{
		ALL[i].style.display = "none";
	}
}

function showKA(id) {
	var P = document.getElementsByClassName("puntiKA_"+id);
	for (var i=0; i<P.length; i++)
		{
			var str = P[i].style.display;
			if(str == "")
				{
					P[i].style.display = "none";
				}
			else
				{
					P[i].style.display = "";
				}
		}
}

function showKU(id) {
	var P = document.getElementsByClassName("puntiKU_"+id);
	for (var i=0; i<P.length; i++)
		{
			var str = P[i].style.display;
			if(str == "")
				{
					P[i].style.display = "none";
				}
			else
				{
					P[i].style.display = "";
				}
		}
}

function hideDoc() {
	if(document.getElementById("stage").checked)
		{
			document.getElementById("divDoc").style.visibility= "hidden";
		}
	else
		{
			document.getElementById("divDoc").style.visibility= "visible";
		}
}