<?php

use Hcode\DB\Sql;
//use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;
use Hcode\Model\Product;


$app->get('/admin/products', function(){
   
//    User::verifyLogin();
    
    $products = new Product();
    
    $products->listAll();
    
    dd($products);
    
    $page = new PageAdmin();
    
    $page->setTpl("products", array(
        "products"=>$products
    ));
    
});

?>