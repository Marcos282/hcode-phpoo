<?php


$cep = "24736020";

$link = "https://viacep.com.br/ws/".$cep."/json/";

$ch = curl_init($link);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$response = curl_exec($ch);

echo $response;

?>