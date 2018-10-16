<?php


function dd($param) {
    echo "<pre>";
    var_dump($param);
    exit;
}

function formatPrice($vlprice){
    
    return number_format((float)$vlprice, 2 , ",",".");
    
}

?>