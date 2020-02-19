<?php
require_once 'config.inc.php';
include_model ('Stile');
include_controller("registrazione");
	$array_form=array(
	0=>"societa",
	1=>"abbrevia",
	2=>"paese",
	3=>"contatto",
	4=>"tel",
	5=>"email",
	6=>"conf_email",
	7=>"stile_iscrizioni"
	
	);


$ctrl=new Registra();





$lang=Lingua::getParole();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="css/form/style.css" type="text/css" media="all" />

<?php 
require_once (_BASEDIR_."/css/font.inc");
?>
<title>Gestione gare</title>
</head>

<body>
<div align="center">
<div id="box">
<div id="head" style='height:100px'>
<div id="logo" style="margin-right:100px"><img src="img/logo.png" height="96%"></img></div>
	<h1 style="top:30px"><?php echo ucfirst($lang['form_reg']) ?></h1>

		<?php 
		echo "<div style='position:absolute;right:4px;top:6px;width:40px;'>";
		$lingue = Lingua::getLingue();
foreach ($lingue as $key_lingua => $lingua) {
    switch ($key_lingua) {
        case "it":
            $flag = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/italy.png\" width='40px'>";
            $flag_active="<img src=\"" . _PATH_ROOT_ . "css/img/flags/italy_active.png\" width='40px'>";
            break;
        case "en":
            $flag = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/england.png\" width='40px'>";
            $flag_active = "<img src=\"" . _PATH_ROOT_ . "css/img/flags/england_active.png\" width='40px'>";
            
            break;
        default:
            $flag = "";
            $flag_active = "";
            
            break;
    }
    if ($key_lingua == Lingua::getLinguaDefault()) {
        echo $flag_active;
    } else {
        echo "<a href=\"?lang=$key_lingua\">$flag</a>";
    }
   echo "<br>";
}
			echo "</div>";
		?>
	
		<!-- 	<div id="Right"  style="width: 78%; float: right; right: 2px; top: 4px;"> -->



	
		<?php 
		
		
		?>
		<!-- </div> -->
		
	</div>
<div style="clear: both"><bR>
</div>
	
		<?php 
		echo "<div id='form_login'>";
		echo "<form accept-charset=\"UTF-8\" method='post'>";
		
		foreach ($array_form as $key=>$value) {
			
			echo "<label style='width:200px' class='iconic '>";
			echo ucfirst($lang[$value]);
			if (isset($_POST[$value]) && $_POST[$value]!='') {
				$valore=$_POST[$value];
				echo "<span class='required-ok'>*</span>";
			}else {
				$valore="";
				echo "<span class='required'>*</span>";
			}
			echo "</label>";
			if ($key=='7') {
				
				echo "<select name='$value'>";
				echo "<option></optiono>";
				foreach (Stile::listaStili() as $option=> $value_op) {
					if ($valore==$option) {
						echo "<option value='$option' selected>".$value_op->getNome()."</option>";			
						
					}else {
						echo "<option value='$option'>".$value_op->getNome()."</option>";			
						
					}
					}
				
				echo "</select>";
			}else {
				
			echo "<input type='text' style='float:left' name='$value' value=\"$valore\">";
			}
			//$title="<span class=\"tooltip\">";
			$title="";
			$title .="<span></span>";// — this is for the triangle, only cosmetic
			$title .="$value"."<br>";
			$title .="$value"."<br>";
			$title .="$value"."<br>";
			//$title .="</span>";// — end of the tooltip
			//echo "<a href=\"#\"><img src=\"" . _PATH_ROOT_ . "img/icone/001_50.png\" style='position:relative;float:left;top:4px;'>$title</a>";

			echo "<div style='clear:both'></div>";
			//echo "<br>";
		}
		echo "<br>";
		echo "<input type='submit' name='reg' value='salva' id='form_login-submit' style='left:100px;float:left'>";
		echo "<br>";
		echo "</form>";
				echo "<br>";
				echo "<br>";
				
		echo "</div>";
		
		?>
		


</br>
</br>
</div>
</div>
</body>
</html>
