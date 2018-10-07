<?php

#https://www.google.com/recaptcha

$email = $_POST['inputEmail'];

//var_dump($_POST);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    "secret"=>"6Leg1HMUAAAAAOui5ZvLmtTeJ4WvJJO1ovqPunls",
    "response"=>$_POST['g-recaptcha-response'],
    "remoteip"=>$_SERVER['REMOTE_ADDR']
)));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$recaptcha = json_decode(curl_exec($ch), true);

curl_close($ch);

echo "<pre>";
var_dump($recaptcha);

if($recaptcha['success']=== true ){
    
    echo "Nesse passo cadastraríamos os dados, pois já estaria validado.<br>";
    echo $_POST['inputEmail'];
}else{
    header("Location: exemplo-02.php");
}
        
?>