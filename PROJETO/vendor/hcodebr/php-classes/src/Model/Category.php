<?php

namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;

use Hcode\Mailer;

class Category extends Model {
    
    
    public function listAll(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        return $sql->select("select * from tb_categories order by idcategory");
        
    }
     
    public function save(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
                
        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
            ":idcategory"=> $this->getidcategory(),
            ":descategory"=> $this->getdescategory()
        ));
        
        $this->setData($results[0]);
    }
    
    public function get($idcategory){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("select * from tb_categories where idcategory = :idcategory", array(
            ":idcategory"=>$idcategory
        ));
        
        $this->setData($results[0]);
        
    }
    
    public function delete(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("DELETE FROM tb_categories where idcategory = :idcategory", array(
            ":idcategory"=> $this->getidcategory()
        ));
        
    }
    
}

?>









