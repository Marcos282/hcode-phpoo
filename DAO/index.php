<?php

ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

require_once "config.php";


$sql = new Sql();

$result = $sql->select("SELECT * from faqfaquser where user_id = '6'");

//echo json_encode($result); 
foreach ($result as $key => $value) {
      
    foreach ($value as $key => $valor) {
        echo $key . ": ";
        
        echo $valor;
        echo "<br>";
    }
    
echo "==========================<br>";
    
}