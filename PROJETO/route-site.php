<?php
use Hcode\Page;
use Hcode\Model\Category;
use Hcode\Model\Product;
use Hcode\Model\Cart;
use Hcode\Model\Address;
use Hcode\Model\User;
use Hcode\Model\Order;
use Hcode\Model\OrderStatus;

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




##################################### ROTAS FORGOT


$app->get('/forgot', function () {
    
    $page = new Page();
          
    $page->setTpl("forgot");
    
});

$app->post('/forgot', function () {
    
    $user= User::getForgot($_POST['email'],false);
     
    header("Location: /forgot/sent");
    exit;
    
});

$app->get('/forgot/sent', function () {
       
    $page = new Page();
    
    $page->setTpl("forgot-sent");
        
});


$app->get('/forgot/reset', function () {

    $user = User::validForgotDecript($_GET['code']);
    
    $page = new Page();
          
    $page->setTpl("forgot-reset",array(
        "name"=>$user['desperson'],
        "code"=>$_GET['code']
    ));
    
});

$app->post('/forgot/reset', function () {
    
    $forgot = User::validForgotDecript($_POST['code']);
    
    User::setForgotUsed($forgot["idrecovery"]);
    
    $user = new User();
    
    $user->get((int)$forgot["iduser"]);
    
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT, array(
        "cost" => 12
    ));
    
    $page = new Page();
          
    $page->setTpl("forgot-reset-success");
    
});

$app->get("/profile", function (){
        
    User::verifyLogin(false);
    
    $user = User::getFromSession();
    
    $page = new Page();            
            
    $page->setTpl("profile", [
        "user"=>$user->getValues(),
        "profileMsg"=> User::getSucessMsg(),
        "profileError"=> User::getError()    
    ]);
    
});

$app->post("/profile", function () {
    
    User::verifyLogin(false);
    
    $user = User::getFromSession();
    
    if(!isset($_POST["desperson"])||($_POST["desperson"]) === ""){
        
        User::setError("Preencha o seu nome.");
        header("Location: /profile");
        exit;
        
    }
    
    if(!isset($_POST["desemail"])||($_POST["desemail"]) === ""){
        
        User::setError("Preencha o seu login corretamente.");
        header("Location: /profile");
        exit;
        
    }
    
    if($_POST["desemail"] != $user->getdesemail()){
    
        if(User::checkLoginExist($_POST["desemail"])){
            
            User::setError("Esse endereço de email já está cadastrado.");
            header("Location: /profile");
            exit;
            
        }
    }
    
    
    $_POST["inadmin"] = $user->getinadmin();
    $_POST["despassword"] = $user->getdespassword();
    $_POST["deslogin"] = $_POST["desemail"];
    
    $user->setData($_POST);
    
    $user->save();
    User::setSucessMsg("Dados alterados como sucesso!");
    
    header("Location:  /profile");
    exit;
        
});

$app->get("/checkout", function() {
    
    User::verifyLogin(false);
    
    $cart = Cart::getFromSession();
    
    $address = new Address();  
    
    
    if(!isset($_GET["zipcode"])){ 
        
        $_GET["zipcode"] = $cart->getzipcode();
    }

    if(isset($_GET["zipcode"])){                
        
        $address->loadFromCEP($_GET["zipcode"]);                
        
        $cart->setdeszipcode($_GET["zipcode"]);
        
        $address->save();
        
        $cart->getCalculatorTotal(); 
        
    }
    
//    dd($address->getValues());
    
    
    $page = new Page();
    
    $page->setTpl("checkout", [
        "cart"=>$cart->getValues(),
        "address"=>$address->getValues(),
        "products"=>$cart->getProducts(),
        "error"=>Cart::getMsgError()
    ]); 
    
});

$app->post("/checkout", function () {
    
    User::verifyLogin(false);
    
    if(!isset($_POST["zipcode"]) || $_POST["zipcode"] === ""){
        
        Cart::setMsgError("O CEP informado é nulo ou inválido.");
        header("Location: /checkout");
        exit;
        
    }
    if(!isset($_POST["desaddress"]) || $_POST["desaddress"] === ""){
        
        Cart::setMsgError("O endereço informado é inválido");
        header("Location: /checkout");
        exit;
        
    }
    if(!isset($_POST["desdistrict"]) || $_POST["desdistrict"] === ""){
        
        Cart::setMsgError("Informe um destrito válido.");
        header("Location: /checkout");
        exit;
        
    }
    
    $user = User::getFromSession();
        
    $address = new Address();
    
    $_POST["deszipcode"] = $_POST["zipcode"];
    $_POST["idperson"] = $user->getidperson();
    
    $address->setData($_POST);
    
    $address->save();
    
    $cart = Cart::getFromSession();
    
    $totals = $cart->getCalculatorTotal();
    
    $order = new Order();
    
    $order->setData([
        "idcart"=>$cart->getidcart(),
        "idaddress"=>$address->getidaddress(),
        "iduser"=>$user->getiduser(),
        "idstatus"=> OrderStatus::ST_EMABERTO,
        "vltotal"=>$totals["vlprice"] + $cart->getvlfreight()
    ]);
    
//    dd($order);
  
    $order->save();
    
    header("Location: /order/".$order->getidorder());
    exit;
    
    
});

$app->get("/order/:idorder", function ($idorder){
    
    User::verifyLogin(false);
    
    $order = new Order();
    
    $order->get($idorder);
    
    $page = new Page();
    
    $page->setTpl("payment", [
        "order"=>$order->getValues()
        
    ]);
    
});

$app->get("/boleto/:idorder", function ($idorder) {

    User::verifyLogin(false);
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
//    dd($order);
    
    // DADOS DO BOLETO PARA O SEU CLIENTE
    $dias_de_prazo_para_pagamento = 10;
    $taxa_boleto = 5.00;
    $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
    $valor_cobrado = formatPrice($order->getvltotal()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
    $valor_cobrado = str_replace(",", ".",$valor_cobrado);
    $valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

    $dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!
    $dadosboleto["numero_documento"] = '0123';	// Num do pedido ou nosso numero
    $dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
    $dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
    $dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
    $dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

    // DADOS DO SEU CLIENTE
    $dadosboleto["sacado"] = "João Rangel";
    $dadosboleto["endereco1"] = "Av. Paulista, 500";
    $dadosboleto["endereco2"] = "Cidade - Estado -  CEP: 00000-000";

    // INFORMACOES PARA O CLIENTE
    $dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
    $dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
    $dadosboleto["demonstrativo3"] = "";
    $dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
    $dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
    $dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
    $dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

    // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
    $dadosboleto["quantidade"] = "";
    $dadosboleto["valor_unitario"] = "";
    $dadosboleto["aceite"] = "";		
    $dadosboleto["especie"] = "R$";
    $dadosboleto["especie_doc"] = "";


    // ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


    // DADOS DA SUA CONTA - ITAÚ
    $dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
    $dadosboleto["conta"] = "48781";	// Num da conta, sem digito
    $dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

    // DADOS PERSONALIZADOS - ITAÚ
    $dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

    // SEUS DADOS
    $dadosboleto["identificacao"] = "Hcode Treinamentos";
    $dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
    $dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
    $dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
    $dadosboleto["cedente"] = "HCODE TREINAMENTOS LTDA - ME";

    // NÃO ALTERAR!
    
    $path = BASEDIR . DIRECTORY_SEPARATOR . "resorces" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR ;
    
//    dd($path);
    
    include($path."funcoes_itau.php"); 
    include($path."layout_itau.php");
        
});


$app->get("/profile/orders", function () {
    
    User::verifyLogin(false);
    
    $user = User::getFromSession();
    
    $page = new Page();        
    
//    dd($user->getOrders());
        
    $page->setTpl("profile-orders", [
        "orders"=>$user->getOrders()
    ]);
    
});

$app->get("/profile/orders/:idorders", function ($idorders){
    
    User::verifyLogin(false);
    
    $order = new Order();
    
    $order->get((int)$idorders);
    
//    dd($order);
    
    $cart = new Cart();
    
    $cart->get((int)($order->getidcart()));
    
//    dd($cart->getValues());
    
    $cart->getCalculatorTotal();
    
    
    
    $page = new Page();        
    
    $page->setTpl("profile-orders-detail", [
        "order"=>$order->getValues(),
        "cart"=>$cart->getValues(),
        "products"=>$cart->getProducts()
    ]);
    
});


$app->get("/profile/change-password", function () {
    
    User::verifyLogin(false);
    
    $page = new Page();
    
    $page->setTpl("profile-change-password", [
        "changePassError"=> User::getError(),
        "changePassSuccess"=> User::getSucessMsg()
    ]);
    
});


$app->post("/profile/change-password", function (){
    
    User::verifyLogin(false);
            
    if(!isset($_POST['current_pass']) || empty($_POST['current_pass'])){
        
        User::setError("Digite sua senha atual.");
        header("Location: /profile/change-password");
        exit;
        
    }    
            
    if(!isset($_POST['new_pass']) || empty($_POST['new_pass'])){
        
        User::setError("Digite a nova senha.");
        header("Location: /profile/change-password");
        exit;
        
    }    
            
    if(!isset($_POST['new_pass_confirm']) || empty($_POST['new_pass_confirm'])){
        
        User::setError("Digite a nova senha para confirmar.");
        header("Location: /profile/change-password");
        exit;
        
    }

    if($_POST["new_pass"] === $_POST["current_pass"]){
        
        User::setError("A nova senha precisa ser diferente da senha atual.");
        header("Location: /profile/change-password");
        exit;
        
    }
     
    $user = User::getFromSession();
    
    if(!password_verify($_POST["current_pass"], $user->getdespassword())){
        
        User::setError("A senha não confere.");
        header("Location: /profile/change-password");
        exit;        
    }
    
    $user->setdespassword($_POST["new_pass"]);
    
//    dd($_POST["new_pass"]);
        
    $user->update();
    
    User::setSucessMsg("Senha alterada com sucesso!");
    header("Location: /profile/change-password");
    exit;     
    
    
    
});
?>