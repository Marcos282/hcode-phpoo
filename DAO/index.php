<?php


require_once "config.php";

$marcos = new Usuario;

$marcos->loadById(1);
        
echo $marcos;
        
//
//$sql = new Sql();
//
//$result = $sql->select("SELECT * from cadastro");
//
////echo json_encode($result); 
//foreach ($result as $key => $value) {
//      
//    foreach ($value as $key => $valor) {
//        echo $key . ": ";
//        
//        echo $valor;
//        echo "<br>";
//    }
//    
//echo "==========================<br>";
//
//




    
//}