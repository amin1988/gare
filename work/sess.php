<?php 
session_start();

if (isset($_GET["dump"])) {
	var_dump($_SESSION);
	exit();
}
?>
<html>
<head>
<script type="text/javascript" src="../js/ajax.js"></script>
<script type="text/javascript">
function check() {
	ajaxCall("../ajax/sess.php", null, write);
}

function dump() {
	ajaxCall("?dump", "dump", write);
}

function write(responseText, res) {
	var table=document.getElementById("times");
	var row=table.insertRow(1);
	var cell=row.insertCell(0);
	if (res != null)
		cell.innerHTML=responseText; //sess
	else
		cell.innerHTML="&nbsp;";
	cell=row.insertCell(0);
	if (res == null) {
		cell.innerHTML=responseText; //res
		if (responseText == "-1") dump();
	} else
		cell.innerHTML=res;
	cell=row.insertCell(0);
	cell.innerHTML=new Date().toString(); //ora
}
setInterval(check,300000);
</script>
</head>
<body>
<table id="times" border="1">
<tr><th>Ora</th><th>Risultato</th><th>session</th></tr>
</table>
<script type="text/javascript">
dump();
check();
</script>
</body></html>