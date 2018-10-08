<?php 

require_once("vendor/autoload.php");
require_once("./config.php");


$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	echo "OK";

});

$app->run();

 ?>