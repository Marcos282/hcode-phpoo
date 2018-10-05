<form method="POST" enctype="multipart/form-data">
    
    <input type="file" name="fileUpload">
    
    <button type="submit">Send</button>
    
</form>

<?php

if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    $file = $_FILES["fileUpload"];
    
    if($file["error"]){
        
        throw new Exception("Error: ".$file["error"]);
        
    }
    
    $dir = "uploads";
    
    if(!is_dir($dir)){
        mkdir($dir);
    }
    
    if(move_uploaded_file($file["tmp_name"], $dir .DIRECTORY_SEPARATOR.$file["name"] )){
        
        echo "Arquivo enviado com sucesso!";
    
    }else{
    
        throw new Exception("Erro ao realizar o upload!");
        
    }
    
    
}
    
    



?>