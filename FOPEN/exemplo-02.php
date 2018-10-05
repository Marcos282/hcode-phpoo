<?php

require_once 'config.php';

$sql = new Sql();

$usuarios = $sql->select("SELECT * from tb_usuario");

echo "<pre>";

//var_dump($usuarios);

$headers = array();

foreach ($usuarios[0] as $key => $value){
        
    array_push($headers, $key);
    
}

$file = fopen("arquivo.csv", "w+");

fwrite($file, implode(",", $headers)."\r\n");

foreach ($usuarios as $row) {
    
    $data = array();
    
    foreach ($row as  $value) {
        
        array_push($data, $value);
        
    }
    
    fwrite($file, implode(",", $data)."\r\n");
    
}

fclose($file);


?>