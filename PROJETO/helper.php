<?php

use Hcode\Model\User;
use Hcode\Model\Cart;

function dd($param) {
    echo "<pre>";
    var_dump($param);
    exit;
}

function formatPrice($vlprice){
    
    return number_format((float)$vlprice, 2 , ",",".");
    
}

function checkLogin($inadmin = true) {
    
    return User::checkLogin($inadmin);
    
}

function getUserName() {
    
    $user = User::getFromSession();
    
    return $user->getdesperson();
    
}

function getCartNrQtd(){
    
   $cart =  Cart::getFromSession();
   
   $totals = $cart->getProductsTotals();
   
   return $totals["nrqtd"];      
    
}

function getVlSubTotal(){
    
   $cart =  Cart::getFromSession();
   
   $totals = $cart->getProductsTotals();
   
   return formatPrice($totals["vlprice"]);      
    
}

?>