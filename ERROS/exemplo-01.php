<?php

function error_handler($code,$menssage,$file,$line){
    
    echo json_encode(array(
        "Code"=>$code,
        "Mesage"=>$menssage,
        "File"=>$file,
        "Line"=>$line
    ));
    
}

set_error_handler("error_handler");

$result = 100/0;

?>
