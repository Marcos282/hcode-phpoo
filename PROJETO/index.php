<?php 

session_start();

require_once("vendor/autoload.php");
require_once("./config.php");
require_once("./helper.php");


use Hcode\DB\Sql;
use Hcode\Page;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Category;



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

    User::verifyLogin();
    
    $page = new PageAdmin();
    
    $page->setTpl("index");
    
});

$app->get('/admin/login', function (){
   
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
          
    $page->setTpl("login");
            
    
});

$app->post('/admin/login', function (){
    
    User::login($_POST['login'],$_POST['password']);
    
    header("Location: /admin");
    exit;


});

$app->get('/admin/logout', function (){
   
    User::logout();
    
    header("Location: /admin/login");
    exit;
    
});

$app->get('/admin/users', function (){
   
    User::verifyLogin();
    
    $users = User::listAll();
    
//    dd($users);
    
    $page = new PageAdmin();
          
    $page->setTpl("users", array(
        "users"=>$users
    ));
            
});

$app->get('/admin/users/create', function (){
   
    User::verifyLogin();
    
    $page = new PageAdmin();
          
    $page->setTpl("users-create");
            
});


$app->get('/admin/users/:iduser/delete', function ($iduser){
   
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$iduser);
    
    $user->delete();
    
    header("Location: /admin/users");
    exit;
            
});

$app->get('/admin/users/:iduser', function ($iduser){
   
    User::verifyLogin();
    
    $user = new User();
    
    $user->get((int)$iduser);
    
    $page = new PageAdmin();
          
    $page->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
            
});

$app->post("/admin/users/create", function() {
   
    User::verifyLogin();
    
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;
    
    $user->setData($_POST);
    
    //dd($user);
    
    $user->save();
    
    header("Location: /admin/users");
    
    
    exit();
    
});

$app->post('/admin/users/:iduser', function ($iduser){
   
    User::verifyLogin();
    
    $user = new User();
    
    $_POST['inadmin'] = (isset($_POST['inadmin']))?1:0;
    
    $user->get((int)$iduser);
    
//    dd($user);
    
    $user->setData($_POST);
    
    $user->update();
    
    header("Location: /admin/users");
    exit;

            
});

$app->get('/admin/forgot', function () {
    
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
          
    $page->setTpl("forgot");
    
    
});

$app->post('/admin/forgot', function () {
    
    $user= User::getForgot($_POST['email']);
    
    
    header("Location: /admin/forgot/sent");
    exit;
    
});

$app->get('/admin/forgot/sent', function () {
   
    
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
          
    $page->setTpl("forgot-sent");
    
    
    
});


$app->get('/admin/forgot/reset', function () {

    $user = User::validForgotDecript($_GET['code']);
    
    $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
          
    $page->setTpl("forgot-reset",array(
        "name"=>$user['desperson'],
        "code"=>$_GET['code']
    ));
    
});

$app->post('/admin/forgot/reset', function () {
    
    $forgot = User::validForgotDecript($_POST['code']);
    
    User::setForgotUsed($forgot["idrecovery"]);
    
    $user = new User();
    
    $user->get((int)$forgot["iduser"]);
    
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, array(
        "cost" => 12
    ));
    
    $user->setPassword($password);
    
      $page = new PageAdmin([
        "header"=>false,
        "footer"=>false
    ]);
          
    $page->setTpl("forgot-reset-success");
    
});

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
    
    $category->save();
    
    header("Location: /admin/categories");
    exit;    
    
});

$app->run();




 ?>