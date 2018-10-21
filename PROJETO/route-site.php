<?php


use Hcode\Page;
use Hcode\Model\Category;
use Hcode\Model\Product;
use Hcode\Model\Cart;
use Hcode\Model\Address;
use Hcode\Model\User;

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
        
    $page = (isset($_GET["page"])) ? (int)$_GET["page"] : 1;
    
    $category = new Category;
    
    $category->get((int)$idcategory);
                
    $pagination = $category->getProductsPage($page);
    
    $pages = array();
    
    for ($i=1; $i <= $pagination["pages"]; $i++ ){
        
        array_push($pages,array(
           "link"=>"/categories/".$category->getidcategory()."?page=".$i,
            "page"=>$i
        ));
        
    }
    
    $page = new Page();
    
    $page->setTpl("category", array(
        "category"=>$category->getValues(),
        "products"=>$pagination["data"],
        "pages"=>$pages
    ));    
    
});


$app->get('/products/:desurl', function ($desurl) {
    
    $product = new Product();
    
    $product->getFromDesURL($desurl);
    
    $page = new Page();
    
    
    $page->setTpl("product-detail", array(
        "product"=>$product->getValues(),
        "categories"=>$product->getCategories()
    ));
    
    
});

$app->get("/cart", function (){
    
    $cart = Cart::getFromSession();
                  
    $page = new Page();    
    
    $page->setTpl("cart",[
        "cart"=>$cart->getValues(),
        "products"=>$cart->getProducts(),
        "error"=> Cart::getMsgError()
    ]);
    
    
    
});


$app->get("/cart/:idproduct/add", function ($idproduct) {
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $qtd = (isset($_GET["qtd"])) ? (int)$_GET["qtd"] : 1;
    
    for ($i =1; $i<= $qtd; $i++){
        
        $cart->addProduct($product);

    }
        
    header("Location: /cart");
    exit;
    
});

$app->get("/cart/:idproduct/minus", function ($idproduct) {
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product);
    
    header("Location: /cart");
    exit;
    
});

$app->get("/cart/:idproduct/remove", function ($idproduct) {
    
    $product = new Product();
    
    $product->get((int)$idproduct);
    
    $cart = Cart::getFromSession();
    
    $cart->removeProduct($product, true);
    
    header("Location: /cart");
    exit;
    
});


$app->post("/cart/freight", function () {
    
    $cart = Cart::getFromSession();
    
//    $cart->getProductsTotals();
    
    $cart->setFreight($_POST["zipcode"]);            
    
    header("Location: /cart");
    exit;
    
    
});

$app->get("/checkout", function() {
    
    User::verifyLogin(false);
    
    
    
    $cart = Cart::getFromSession();
    
    $address = new Address();
    
    $page = new Page();
    
    $page->setTpl("checkout", [
        "cart"=>$cart->getValues(),
        "address"=>$address->getValues()
    ]); 
    
});


$app->get("/login", function() {       
    
    $page = new Page();
    
    $user = User::getFromSession();   
            
    
    $page->setTpl("login",[
    
        "error"=>User::getError(),
        "errorRegister"=>User::getErrorRegister(),
        "registerValues"=>(isset($_SESSION["registerValues"]))? $_SESSION["registerValues"] : ["name"=>"","email"=>"","phone"=>""]
        
    ]); 
    
});

$app->post("/login", function() {       
    
    try{
        
        User::login($_POST["login"], $_POST["password"]);
        
    } catch (Exception $ex) {

        User::setError($ex->getMessage());
        
    }

    header("Location: /checkout");
    exit;
    
});


$app->get("/logout", function() {       
    
    User::logout();

    header("Location: /login");
    exit;
    
});

$app->post("/register", function (){
    
    $_SESSION["registerValues"] = $_POST;
    
    if(!isset($_POST["name"]) || $_POST["name"] == ""){
        
        User::setRegisterError("Preencha o seu nome corretamente");    
        header("Location: /login");                
        exit;
        
    }
    if(!isset($_POST["email"]) || $_POST["email"] == ""){
        
        User::setRegisterError("Preencha o seu email corretamente");    
        header("Location: /login");                
        exit;
        
    }

    
    if(User::checkLoginExist($_POST["email"])=== true){
        
        User::setRegisterError("O login informado já está em uso.");    
        header("Location: /login");                
        exit;
        
        
    }
    
    $user = new User();
    
    $user->setData([
        "desperson"=>$_POST["name"],
        "deslogin"=>$_POST["email"],        
        "nrphone"=>$_POST["phone"],
        "desemail"=>$_POST["email"],
        "despassword"=>$_POST["password"],     
        "inadmin"=>0
    ]);
    
    
//    dd($user);
                      
    $user->save();
    
    User::login($_POST["email"], $_POST["password"]);
    
    header("Location: /checkout");
    
    
    
});


?>