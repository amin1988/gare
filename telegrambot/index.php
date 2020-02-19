<?php

$api = "339198313:AAH6zjFZIMNqH-4uV2IZ4XoeXoxYhXH8gFo";
$input = file_get_contents("php://input");
$update = json_decode($input, true);
 
$message = $update['message']['text'];
$fd = fopen("prova.txt","w");
$str = "ciao ".$message;
fwrite($fd, $message);
fclose($fd);
print $str;
$chatid = $update['message']['chat']['id'];
 
function sendMessage($chatid, $text)
{
    global $api;
    $url = "https://api.telegram.org/$api/sendMessage?chat_id=".$chatid."&text=".urlencode($text);
    $get = file_get_contents($url);
    print $get;
}
 
if($message == "/start")
{
    sendMessage($chatid, "Bot avviato");
}