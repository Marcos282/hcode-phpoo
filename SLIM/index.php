<?php

require_once ("./vendor/autoload.php");


$app = new \Slim\Slim();


$app->get('/', function () {

    echo json_encode(array(
        "data"=>date("Y-m-d")
        
    ));
    
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, " . $name;
});
$app->run();


?>