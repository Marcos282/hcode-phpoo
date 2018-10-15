<?php

use Hcode\DB\Sql;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;


$app->get('/admin/categories', function () {
    
    User::verifyLogin();
    
    $categories = Category::listAll();
    
    $page = new PageAdmin();
          
    $page->setTpl("categories",array(
        "categories"=>$categories
    ));
    
});

$app->get('/admin/categories/create', function(){
    
    User::verifyLogin();
    
    $page = new PageAdmin();
          
    $page->setTpl("categories-create");
    
});

$app->post('/admin/categories/create', function(){
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->setData($_POST);
    
    $category->save();
    
    header("Location: /admin/categories");
    exit;
    
    
});

$app->get('/admin/categories/:category/delete', function($idcategory) {
    
    User::verifyLogin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
    
    $category->delete();
    
    header("Location: /admin/categories");
    exit;
    
});

$app->get('/admin/categories/:category', function($idcategory) {
    
    User::verifyLogin();
    
    $page = new PageAdmin();
    
    $category = new Category();
    
    $category->get((int)$idcategory);
              
    $page->setTpl("categories-update", array(
        "category"=>$category->getValues()
    ));
    
});
$app->post('/admin/categories/:category', function($idcategory) {
    
    User::verifyLogin();
    
    $page = new PageAdmin();
    
    $category = new Category();
    
    $category->setData($_POST);
    
//    dd($category);
    
    $category->save();
    
    header("Location: /admin/categories");
    exit;    
    
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