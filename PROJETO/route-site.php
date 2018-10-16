<?php


use Hcode\Page;
use Hcode\Model\Category;
use Hcode\Model\Product;

$app->get('/', function() {

//    $products = new Product;
//    
//    $AllProducts = $products->listAll();
    
    $products = Product::listAll();
    
    $page = new Page();
    
    $page->setTpl("index", array(
        "products"=> Product::checkList($products)
    ));
    
    
//    $sql = new Sql(CONFIG_DB_ECOMERCE);
//    
//    $results = $sql->select("SELECT * FROM tb_users");
//
//    echo json_encode($results);
});


$app->get('/categories/:idcategory', function ($idcategory) {
        
    $category = new Category;
    
    $category->get((int)$idcategory);
                
    $page = new Page();
    
    $page->setTpl("category", array(
        "category"=>$category->getValues()
    ));    
    
});

?>