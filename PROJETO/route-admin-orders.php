<?php

use Hcode\DB\Sql;
use Hcode\PageAdmin;
use Hcode\Model\User;
use Hcode\Model\Order;
use Hcode\Model\OrderStatus;


$app->get("/admin/orders/:idorder/status", function ($idorder){
    
    User::verifyLogin();
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
    $page = new PageAdmin();
    
    $page->setTpl("order-status", [
       "order"=>$order->getValues(),
       "status"=> OrderStatus::listAll(),
       "msgError"=> Order::getError(),
       "msgSuccess"=>Order::getSucessMsg()
    ]);
    
});


$app->post("/admin/orders/:idorder/status", function ($idorder){
    
    User::verifyLogin();
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
    $order->setidstatus($_POST["idstatus"]);
    
    $order->save();
    
    Order::setSucessMsg("Status alterado com sucesso!");
    
    header("Location: /admin/orders/$idorder/status");
    exit;
    
    
    
});

$app->get("/admin/orders/:idorder/delete", function ($idorder){
    
    User::verifyLogin();
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
    $order->delete();
    
    header("Location: /admin/orders");
    exit;
    
    
});


$app->get("/admin/orders/:idorder", function ($idorder) {
    
    User::verifyLogin();
    
    $order = new Order();
    
    $order->get((int)$idorder);
    
    $cart = $order->getCart();
    
    $page = new PageAdmin();
    
    $page->setTpl("order", [
       "order"=>$order->getValues(),
        "cart"=>$cart->getValues(),
        "products"=>$cart->getProducts()
    ]);
    
});

$app->get("/admin/orders", function () {
    
    User::verifyLogin();
    
    $page = new PageAdmin();
    
//    dd(Order::listAll());
    
    $page->setTpl("orders", [
        "orders"=> Order::listAll()
    ]);
    
});

?>