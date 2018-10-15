<?php


use Hcode\Page;


$app->get('/', function() {

    $page = new Page();
    
    $page->setTpl("index");
    
    
//    $sql = new Sql(CONFIG_DB_ECOMERCE);
//    
//    $results = $sql->select("SELECT * FROM tb_users");
//
//    echo json_encode($results);
});




?>