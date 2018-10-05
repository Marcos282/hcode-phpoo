<?php

if(isset($_COOKIE["DADOS_CLIENTE"])){
    
//    $obj = json_decode($_COOKIE["DADOS_CLIENTE"]);
//    
//    var_dump($obj);
//    
//    echo $obj->CP;
//    
    
    $obj = json_decode($_COOKIE["DADOS_CLIENTE"],true);
    
    var_dump($obj);
}

?>