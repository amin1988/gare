<?php
require_once 'config.inc.php';
include_controller("index");
include_view("Header","index");
include_class("Menu"); //TODO usare template

$lang=Lingua::getParole();

$ctrl = new Index();
$head = new Header(new IndexView(false));
$head->setLogout(false);
$head->setIscrEst(true);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/screen.css" rel="stylesheet" type="text/css" />
<link rel="icon" href="<?php echo _PATH_ROOT_; ?>img/logo.png" type="image/png"/>

		<!-- Style of the component -->
		<link rel="stylesheet" type="text/css" href="VerticalSlidingAccordion/css/style.css" />
		<noscript>
			<link rel="stylesheet" type="text/css" href="VerticalSlidingAccordion/css/styleNoJS.css" />
		</noscript>
		<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow&v1' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300&v2' rel='stylesheet' type='text/css' />

<?php 
require_once (_BASEDIR_."/css/font.inc");
?>
<title>Gestione gare</title>
</head>

<body>
<div align="center">
<div id="box">
<?php $head->stampaHeader(); ?>

<div id="Left" style="float: left; width: 98%; top: 4px; left: 4px;">

<div id="va-accordion" class="va-container">

<div class='va-nav'>
<span class="va-nav-prev">Previous</span>
<span class="va-nav-next">Next</span>

</div>
<div class="va-wrapper">
<?php 

$gare=$ctrl->getGarePubbliche();

foreach ($gare as $id=> $value) {
	/* @var $value Gara */
	$g = Gara::fromId($id);
	$wkc = $g->getWkc();
	
	if(_WKC_MODE_)
	{
		if($wkc == 0)
			continue;
	}
	else
	{
		if($wkc == 1)
			continue;
	}
			
	$nome= $value->getNome();
	$id=$value->getChiave();
	if ($value->haLocandina()) 
		$locandina=_PATH_ROOT_.$value->getLocandina();
	else
		$locandina=_PATH_ROOT_._LOCANDINA_SUBDIR_."locandina.jpg";
	
// 	if ($locandina) $bg="style ='background:#0f1b5f url($locandina) no-repeat right center;'";
// 	else $bg="style ='background:#000 url(locandine/locandin.jpg) no-repeat right center;'";
	
	echo "<div class=\"va-slice va-slice-1\" style ='background:#0f1b5f url($locandina) no-repeat right center;'>";
	echo "<h3 class=\"va-title\">$nome</h3>";
	echo "<div class='bordo'></div>";
	echo "<div class=\"va-content\">";
	//echo "<p>Henry Watson</p>";
	echo "<ul>";
	echo "<li ><a href=\"dettagli.php?id=$id\" >$lang[gara_dettagli]</a></li>";
	if(!$value->iscrizioniChiuse())
		echo "<li><a href=\"soc/iscrivi.php?id=$id\">$lang[gara_iscrizioni]</a></li>";
// 	echo "<li><a href=\"#\">Contact</a></li>";
	echo "</ul>";
	echo "</div>";
	echo "</div>";
	
}
?>	
</div>			
			<!--  	
				<div class="va-nav">
				
				
					<span class="va-nav-prev">Previous</span>
					<span class="va-nav-next">Next</span>
				</div>
				
				<div class="va-wrapper">
					<div class="va-slice va-slice-1">
						<h3 class="va-title">Marketing</h3>
						<div class="va-content">
							<p>Henry Watson</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>
					</div>
					<div class="va-slice va-slice-2">
						<h3 class="va-title">Management</h3>
						<div class="va-content">
							<p>Keith Johnson</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
					<div class="va-slice va-slice-3">
						<h3 class="va-title">Visual Design</h3>
						<div class="va-content">
							<p>Andrew Alaniz</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
					<div class="va-slice va-slice-4">
						<h3 class="va-title">Quality Control</h3>
						<div class="va-content">
							<p>Ben Freeman</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
					<div class="va-slice va-slice-5">
						<h3 class="va-title">Web development</h3>
						<div class="va-content">
							<p>Alex Schuman</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
					<div class="va-slice va-slice-6">
						<h3 class="va-title">Customer Support</h3>
						<div class="va-content">
							<p>Maria Wales</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
					<div class="va-slice va-slice-7">
						<h3 class="va-title">Server Administration</h3>
						<div class="va-content">
							<p>Paul White</p>
							<ul>
								<li><a href="#">About</a></li>
								<li><a href="#">Portfolio</a></li>
								<li><a href="#">Contact</a></li>
							</ul>
						</div>	
					</div>
				</div>
				-->
				<br></br>
</div>

				<br></br>
<br></br>










<!-- 
<a	href="#"> <img src="img_prova/ricardo-campello-8.jpg" width="280px"></img></a>
 -->
</div>


<div style="clear:both"><br></br></div>
</div>

</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="VerticalSlidingAccordion/js/jquery.easing.1.3.js"></script>
		<script type="text/javascript" src="VerticalSlidingAccordion/js/jquery.mousewheel.js"></script>
		<script type="text/javascript" src="VerticalSlidingAccordion/js/jquery.vaccordion.js"></script>
		<script type="text/javascript">
			$(function() {
				$('#va-accordion').vaccordion({
					expandedHeight	: 450,
					animSpeed		: 500,
					animEasing		: 'easeInOutBack',
					animOpacity		: 0.4
				});
			});
		</script>
</body>
</html>
