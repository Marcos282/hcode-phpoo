<?php

namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Model\Product;
use Hcode\Model\User;

use Hcode\Mailer;

class Cart extends Model {

    CONST SESSION = "Cart";
    
    public static function getFromSession(){
        
        $cart = new Cart();
        
        if(isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0){
         
            $cart->get((int)$_SESSION[Cart::SESSION]['idcart']);
            
        }else{
            
            $cart->getFromSessionID();
            if(!$cart->getidcart() >0){
                
                $data = [
                    "dessessionid"=> session_id()
                ];
                
                if(User::checkLogin(false)){
                    
                    $user = User::getFromSession();
                    
                    $data["iduser"] = $user->getiduser();
                }
                
                $cart->setData($data);
                
                $cart->save();
                
                $cart->setToSession();
                
                
            }
        }
        
        return $cart;
        
    }
    
    public function setToSession(){
        
        $_SESSION[Cart::SESSION] = $this->getValues();
        
    }


    
    public function getFromSessionID(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * from tb_carts WHERE dessessionid = :dessessionid;", array(
            ":dessessionid"=> session_id() // ESSA FUNÇÃO session_id() É NATIVA DO PHP PARA PEGAR A SESSAO.  
        ));
        
        if(count($results) > 0){
            
            $this->setData($results[0]);
        }
        
    }

    
    public function get($idcart){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * from tb_carts WHERE idcart = :idcart;", array(
            ":idcart"=>$idcart
        ));
        
        if(count($results) > 0){
            
            $this->setData($results[0]);
        }
        
    }

    public function save(){

        $sql = new Sql(CONFIG_DB_ECOMERCE);

        $results = $sql->select("CALL sp_carts_save(:idcart,:dessessionid,:iduser,:deszipcode,:vlfreight,:nrdays)",[
           ":idcart"=> $this->getidcart(),
           ":dessessionid"=> $this->getdessessionid(),
           ":iduser"=> $this->getiduser(),
           ":deszipcode"=> $this->getzipcode(),
           ":vlfreight"=> $this->getvlfreight(),
           ":nrdays"=> $this->getnrdays(),        
        ]);

        $this->setData($results[0]);

    }
    
    public function addProduct (Product $product){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("INSERT INTO tb_cartsproducts (idcart,idproduct) VALUES (:idcart, :idproduct)", array(
            
            ":idcart"=> $this->getidcart(),
            ":idproduct"=>$product->getidproduct()
            
        ));        
    }
    
    public function removeProduct (Product $product, $all = false){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        if($all){  // Se desejar remover todos os produtos
            
            $sql->select("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart and idproduct = :idproduct and dtremoved  IS NULL", array(
                
            ":idcart"=> $this->getidcart(),
            ":idproduct"=>$product->getidproduct()
                
            ));
            
        }else{ // Se somente for um produto por vez
            
            $sql->select("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart and idproduct = :idproduct and dtremoved  IS NULL LIMIT 1 ", array(
                
            ":idcart"=> $this->getidcart(),
            ":idproduct"=>$product->getidproduct()
                
            ));
            
        }
        
        
    }
    
    public function getProducts(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
                    
        
        $rows = $sql->select("SELECT fil.idproduct, fil.desproduct, fil.vlprice, fil.vlweight, fil.vlheight, fil.vllength, fil.vlweight, fil.desurl, count(*) as nrqtd, SUM(fil.vlprice) as vltotal  FROM tb_cartsproducts pai
                            INNER JOIN tb_products fil ON pai.idproduct = fil.idproduct
                            WHERE 
                            pai.idcart = :idcart AND
                            pai.dtremoved IS NULL
                            GROUP BY fil.idproduct, fil.desproduct, fil.vlprice, fil.vlweight, fil.vlheight, fil.vllength, fil.vlweight, fil.desurl 
                            ORDER BY fil.desproduct", [
                                "idcart"=> $this->getidcart()
                            ]);

        return Product::checkList($rows);     
    }
    
     
}

?>









