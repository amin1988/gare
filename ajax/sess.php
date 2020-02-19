<?php
session_start();
if (isset($_SESSION["idutente"]))
	echo 1;
else if (count($_SESSION) > 0)
	echo -1;
else
	echo 0;