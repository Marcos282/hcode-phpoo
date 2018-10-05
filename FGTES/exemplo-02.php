<?php
$filename = "arquivo.csv";

if(file_exists($filename)){
    
    $file = fopen($filename, "r");
    
    $headers = explode(",", fgets($file));
    
    $data = array();
    
    while ($row = fgetc($file) ){
        
//        $rowData = explode(",", $row);
//        
//        $linha = array();
//        
//        for ($i = 0; $i < count($rowData); $i++){
//            
//            $linha[$headers[$i]] = $rowData[$i];
//            
//        }
//        
//        array_push($data, $linha);
       echo $row; 
    }
    
    fclose($file);
    
    echo json_encode($data);
    
}

?>