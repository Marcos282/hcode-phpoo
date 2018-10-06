<?php



try {
    
    throw new Exception("Houve um erro no sistema.",400);
    
} catch (Exception $e) {
    
echo    json_encode(array(
        "Message:"=>$e->getMessage(),
        "Line:" =>$e->getLine(),
        "File:" =>$e->getFile(),
        "Cod:" =>$e->getCode()
    ));
    
}

?>