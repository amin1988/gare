<?php

class IndexView {
	private $registra;
	
	public function __construct($registra) {
		$this->registra = $registra;
	}
	
	public function stampaCentroHead() {
?>
<div id="boxLogin" style="width:30%;float:left;top:4px;">


<form accept-charset="UTF-8" method="post" action="<?php echo _PATH_ROOT_; ?>login.php" id="form_login">
<span style="width:100%;text-align:left"><b ><?php echo ucfirst(Lingua::getParola("login")); ?></b></span>

<div><label for="username"><?php  echo ucfirst(Lingua::getParola("username")); ?>:</label> 
<input type="text" name="username" id="username" /></div>
<div><label for="password"><?php echo ucfirst(Lingua::getParola("password")); ?>:</label> 
<input type="password" id="password" name="password"></input></div>
	
<input type="submit" id="form_login-submit" style='left:55px;' value="<?php echo ucfirst(Lingua::getParola("login_button")); ?>"><br>
</form>
<?php 
echo "<div id='boxLogin' style='border:solid 1px red;padding:2px;width:96%;top:4px;margin-bottom:5px;'>";
	echo "<a href=\"#\">".ucfirst(Lingua::getParola("ricorda"))."</a>";
	echo "</div>";
	
?>

</div>
<?php if ($this->registra) { ?>
<div id="boxLogin" style="width:30%;float:left;top:4px;">

<h1><?php echo ucfirst(Lingua::getParola('registrati')); ?></h1>
	<br>
	<br>
	<?php 
	echo "<div id='registrazione'>";
	echo "<a href=\"registra.php\" >".ucfirst(Lingua::getParola('registrati'))."</a>";
	echo "</div>";
	?>
		
		<br> 
		<br>
</div>
<?php } //if registra ?>

<?php 
	} //stampaCentroHead
	
	public function getTitolo() {
		return "Home";
	}
}


?>