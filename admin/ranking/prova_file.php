<?php

$fp = fopen("prova_cron.txt","w");
fwrite($fp, "cron success!");
fclose($fp);