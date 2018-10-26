<?php


namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Model\Cart;

class Order extends Model {
    
    const ORDER_ERROR_MSG = "ERROR_MSG";
    const ORDER_SUCESS_MSG = "SUCESS_MSG";
    
    public function save(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        
//        dd($this->getvltotal());
        
                
//        
//        $results = $sql->query("INSERT INTO tb_orders (idorder, idcart, iduser, idstatus, idaddress, vltotal)
//                                           VALUES(:idorder,:idcart,:iduser,:idstatus,:idaddress,:vltotal)",[
//            ":idorder"=> $this->getidorder(),
//            ":idcart"=> $this->getidcart(),
//            ":iduser"=> $this->getiduser(),
//            ":idstatus"=> $this->getidstatus(),
//            ":idaddress"=> $this->getidaddress(),
//            ":vltotal"=> $this->getvltotal()
//                                               
//                                           ]);

        $results = $sql->select("CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress,:vltotal)", [
            ":idorder"=> $this->getidorder(),
            ":idcart"=> $this->getidcart(),
            ":iduser"=> $this->getiduser(),
            ":idstatus"=> $this->getidstatus(),
            ":idaddress"=> $this->getidaddress(),
            ":vltotal"=> $this->getvltotal()
        ]);
        
        if(count($results) > 0){
//            die("cheguei até aquiaaaaaaaaaaaa");
            $this->setData($results[0]);
            
            
        }
        
    }
    
    
    public function get($order){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT *
                     from tb_orders pai 
                     INNER JOIN tb_ordersstatus fil USING(idstatus) 
                     INNER JOIN tb_carts net USING(idcart)
                     INNER JOIN tb_users biz ON biz.iduser = pai.iduser
                     INNER JOIN tb_addresses tara USING(idaddress)
                     INNER JOIN tb_persons hept ON tara.idperson = biz.idperson
                     WHERE 
                     pai.idorder = :idorder",[
                         ":idorder"=>$order
                     ]);
        
        if(count($results)> 0){
            
            $this->setData($results[0]);
            
        }
        
    }
    
    public static function listAll(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
         $results = $sql->select("SELECT *
                     from tb_orders pai 
                     INNER JOIN tb_ordersstatus fil USING(idstatus) 
                     INNER JOIN tb_carts net USING(idcart)
                     INNER JOIN tb_users biz ON biz.iduser = pai.iduser
                     INNER JOIN tb_addresses tara USING(idaddress)
                     INNER JOIN tb_persons hept ON tara.idperson = biz.idperson
                     ORDER BY pai.idorder ASC
                     ");
        
        if(count($results)> 0){
            
            return $results;
            
        }
        
        
    }

    public function delete() {
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", [
            ":idorder"=> $this->getidorder()
        ]);
        
    }

    public function getCart() {
        
        $cart = new Cart();
        
        $cart->get((int) $this->getidcart());
        
        return $cart;
        
    }
    
    
    public static function setError($msg){
        
        $_SESSION[Order::ORDER_ERROR_MSG]= $msg;
                                       
    }
    
    public static function getError(){
        
        $msg = (isset($_SESSION[Order::ORDER_ERROR_MSG]) && $_SESSION[Order::ORDER_ERROR_MSG]) ? $_SESSION[Order::ORDER_ERROR_MSG] : ""; 
        
        User::clearError();
        
        return $msg;
        
    }

    public static function clearError() {
        
        $_SESSION[Order::ORDER_ERROR_MSG] = null;
        
    }
    

    
    
        public static function setSucessMsg($msg){
        
        $_SESSION[Order::ORDER_SUCESS_MSG]= $msg;
                                       
    }
    
    public static function getSucessMsg(){
        
        $msg = (isset($_SESSION[Order::ORDER_SUCESS_MSG]) && $_SESSION[Order::ORDER_SUCESS_MSG]) ? $_SESSION[Order::ORDER_SUCESS_MSG] : ""; 
        
        User::clearSucessMsg();
        
        return $msg;
        
    }

    public static function clearSucessMsg() {
        
        $_SESSION[Order::ORDER_SUCESS_MSG] = null;
        
    }
    
    

} 


?>

