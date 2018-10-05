<?php

$name = "images";

//echo "<pre>";

$images = scandir($name);

$data = array();

foreach ($images  as $img) {

    if(!in_array($img, ['.','..'])){
        
        $filename = 'images'.DIRECTORY_SEPARATOR.$img;
        
        $info = pathinfo($filename);
        
        $info["size"] = filesize($filename);
        $info["modified"] = date("Y-m-d h:m:i",filemtime($filename));
        $info["url"] = 'http:\/\/177.11.168.185/hcode-phpoo/DIR/exemplo-02.php'.$filename;
//        var_dump($info);
        
        
        
        array_push($data, $info);
        
    }
    
  
}

  echo json_encode($data);

?>
