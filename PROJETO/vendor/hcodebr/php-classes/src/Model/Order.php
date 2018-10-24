<?php


namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class Order extends Model {
    
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
    
} 


?>

