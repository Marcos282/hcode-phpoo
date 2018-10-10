<?php 

require_once("vendor/autoload.php");
require_once("./config.php");


use Hcode\DB\Sql;
use Hcode\Page;
use Hcode\PageAdmin;

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {

    $page = new Page();
    
    $page->setTpl("index");
    
    
//    $sql = new Sql(CONFIG_DB_ECOMERCE);
//    
//    $results = $sql->select("SELECT * FROM tb_users");
//
//    echo json_encode($results);
});

$app->get('/admin', function() {

    $page = new PageAdmin();
    
    $page->setTpl("index");
    
});

$app->run();




 ?>