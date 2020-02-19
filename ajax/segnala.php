<?php
if (!isset($_POST["pagina"]) || !isset($_POST["descrizione"])) exit("0");

session_start();
require_once("../config.inc.php");
include_model("Segnalazione");

if (isset($_POST["email_segnala"])) {
	$email = trim($_POST["email_segnala"]);
	$_SESSION["email_segnala"] = $email;
} else
	$email = NULL;

$s = Segnalazione::crea($_POST["pagina"], $_POST["descrizione"], $email);
$s->salva();

if (_LOCALHOST_) exit("1");

// path: /affiliazione 3.0/PHPMailer/class.phpmailer.php
if(_WKC_MODE_)
	require_once(_BASEDIR_."phpmailer/class.phpmailer.php");
else
	require_once(_BASEDIR_."../affiliazione 3.0/PHPMailer/class.phpmailer.php");
$ut = Utente::crea(NULL,true);
$nomefull = "";
if (is_null($ut)) {
	$nome = "Segnalazione";
	if (is_null($email))
		$email = "gare@fiamsport.it";
	$bodyemail = "";
	$idu = "nessuno";
	$tipo = "";
	$user = "";
} else {
	if ($ut->getTipo() == Utente::SOCIETA) {
		$nome = $ut->getSocieta()->getNomeBreve();
		$nl = $ut->getSocieta()->getNome();
		$nomefull = "($nl)";
	} else
		$nome = $ut->getContatto();
	$bodyemail = $ut->getEmail();;
	if (is_null($email)) {
		$email = $ut->getEmail();
	} else if ($bodyemail != $email) {
		$bodyemail = "$email $bodyemail";
	}
	$idu = $ut->getChiave();
	$tipo = $ut->getNomeTipo();
	$user = $ut->getNome();
}

$body = "ID Utente: $idu\r\nUsername: $user\r\nNome: $nome $nomefull\r\nEmail: $bodyemail\r\nTipo: $tipo";
$body .= "\r\n\r\nPagina: $_POST[pagina]\r\nBrowser: $_SERVER[HTTP_USER_AGENT]\r\n\r\n$_POST[descrizione]";

$mail = new PHPMailer();
//$mail->IsSMTP();                    // attiva l'invio tramiteSMTP
//$mail->Host= "$SMTP"; // indirizzo smtp
$mail->CharSet = "UTF-8";
$mail->From = $email;
$mail->FromName = $nome;
$mail->AddAddress("gare@fiamsport.it");
if(_WKC_MODE_)
	$mail->addBCC("wkcrimini2016@gmail.com","WKC Rimini 2016");
$mail->IsHTML(false);
$mail->Subject  =  "SEGNALAZIONE ".$s->getChiave();
$mail->Body     =  $body;

$mail->Send();

exit("1");

?>