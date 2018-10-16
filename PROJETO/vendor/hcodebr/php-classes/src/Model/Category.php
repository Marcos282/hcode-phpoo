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
        
        
//        dd($this->getidcategory());
        
        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
            ":idcategory"=> $this->getidcategory(),
            ":descategory"=> $this->getdescategory()
        ));
        
        $this->setData($results[0]);
        
        Category::updateFields();
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
        
        Category::updateFields();
        
    }
    
    public static function updateFields(){
        
        $categories = Category::listAll(); 
        
        $html = array();
         
        foreach ($categories as $row) {
            array_push($html,'<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }
        
        file_put_contents(BASEDIR.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'categories-menu.html', implode("",$html));
        
    }
    
    public function getProducts($related = true){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        if($related === true){
            
            return $sql->select("SELECT * FROM tb_products
                          WHERE
                          idproduct IN (
                                        SELECT pai.idproduct FROM tb_products pai
                                    INNER JOIN tb_productscategories fil ON pai.idproduct = fil.idproduct
                                    AND fil.idcategory = :idcategory)", array(
                                        "idcategory"=> $this->getidcategory()
                                    ));
            
        }else{
            
            return $sql->select("SELECT * FROM tb_products
                          WHERE
                          idproduct NOT IN (
                                        SELECT pai.idproduct FROM tb_products pai
                                    INNER JOIN tb_productscategories fil ON pai.idproduct = fil.idproduct
                                    AND fil.idcategory = idcategory)", array(
                                        "idcategory"=> $this->getidcategory()
                                    ));
            
        }
        
    }
    
    public function addProduct($product){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("INSERT into tb_productscategories (idcategory, idproduct) VALUES (:idcategory,:idproduct)", array(
            ":idcategory"=> $this->getidcategory(),
            "idproduct"=>$product->getidproduct()
        ));
        
    }
    
    public function removeProduct($product){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("DELETE FROM tb_productscategories where idcategory = :idcategory and idproduct = :idproduct", array(
            ":idcategory"=> $this->getidcategory(),
            "idproduct"=>$product->getidproduct()
        ));
        
    }
    
    public function getProductsPage($page = 1,$itensPerPage = 2 ){
        
        
        $start = ($page - 1) * $itensPerPage;
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT SQL_CALC_FOUND_ROWS *
                        FROM tb_products pai
                        INNER JOIN tb_productscategories fil ON pai.idproduct = fil.idproduct
                        INNER JOIN tb_categories net ON fil.idcategory = net.idcategory
                        AND net.idcategory = :idcategory
                        LIMIT $start, $itensPerPage;", array(
                            ":idcategory"=> $this->getidcategory()
                        ));
        
        $fullResult = $sql->select("SELECT FOUND_ROWS() AS NR_TOTAL;"); 
        
        return array(
            "data"=> Product::checkList($results),
            "total"=>$fullResult[0]["NR_TOTAL"],
            "pages"=> ceil($fullResult[0]["NR_TOTAL"] / $itensPerPage)
        );

    }
    
}

?>









