<?php
//STAMPA
session_start();

require_once("config.inc.php");
include_controller("login");
include_view("Header", "Template");
$lang = Lingua::getParole();

$ctrl = new Login();
$head = Header::titolo("");
$head->setIndietro("", "Home");
$head->setLogout(false);
$templ = new Template($head);
$templ->setBodyDiv(false);

$templ->stampaTagHead(false);
?>
<style type="text/css">
#errore {
	color: #F00;
}
</style>
</head>

<?php 
$templ->apriBody();
?>

<div id="boxLogin">


<h1><?php echo ucfirst($lang["login"]); ?>
</h1>
<form accept-charset="UTF-8" method="post" action="" id="form_login" style="width:60%">
  
    <label for="username"><?php echo ucfirst($lang["username"]); ?>:</label>
    <input type="text" name="username" id="username" value="<?php echo $ctrl->getUsername(); ?>" />
  <br>
    <label for="password"><?php echo ucfirst($lang["password"]); ?>:</label>
    <input type="password" name="password" id="password" /><br>
 <br>
    <input type="submit" name="button" id="button" value="<?php echo ucfirst($lang["login_button"]); ?>" />
  
  <p id="errore">
<?php
if ($ctrl->campoVuoto())
   echo ucfirst($lang["login_vuoto"]);
else if ($ctrl->loginErrato()) 
   echo ucfirst($lang["login_errore"]);
?></p>
</form>
<!-- <p><?php echo ucfirst($lang["registrati"]); ?></p> -->
</div>
<?php 
$templ->chiudiBody();
?>