<?php


//use Hcode\DB\Sql;
//use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
//use Hcode\Model\Category;
use Hcode\Model\Product;

$app->get('/admin/products(/)', function(){
   
    User::verifyLogin();        
    
    $product = new Product();
    
    $products = $product->listAll();
    
    $page = new PageAdmin();
    
    $page->setTpl("products", array(
        "products"=>$products
    ));
    
});


$app->get('/admin/products/create(/)', function(){
   
    User::verifyLogin();
       
    $page = new PageAdmin();
    
    $page->setTpl("products-create");
    
});




$app->post('/admin/products/create(/)', function(){
   
//    User::verifyLogin();
    
    $product = new Product;
        
    $product->setData($_POST);
    
        
    $product->create();
    
    header("Location: /admin/products");
    exit;       
    
});


$app->get('/admin/products/:idproduct', function($idproduct){
   
    User::verifyLogin();
    
    $product = new Product;
        
    $product->get((int)$idproduct);
        
    $page = new PageAdmin();
    
    $page->setTpl("products-update",[
        "product"=>$product->getValues()
    ]);
    
});

$app->post('/admin/products/:idproduct', function($idproduct){
   
//    User::verifyLogin();
    
    $product = new Product;
        
    $product->get((int)$idproduct);
        
    $product->setData($_POST);
    
    $product->save();
    
    $product->addPhoto($_FILES["file"]);
    
    header("Location: /admin/products");
    exit;
    
});

$app->get('/admin/products/:idproduct/delete', function($idproduct){
   
//    User::verifyLogin();
    
    $product = new Product;
        
    $product->get((int)$idproduct);        
    
    $product->delete();
    
    header("Location: /admin/products");
    exit;    
    
});

?>