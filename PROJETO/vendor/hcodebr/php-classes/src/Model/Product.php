<?php

namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;

use Hcode\Mailer;

class Product extends Model {
    
    
    public function listAll(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        return $sql->select("select * from tb_products");
        
    }
    
    public static function checkList($list){
        
        foreach ($list as &$row) {
            $p = new Product();
            $p->setData($row);
            $row = $p->getValues();
        }
        
        return $list;
        
    }

        public function save(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        
//        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct,:vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
//            ":idproduct"=> $this->getidproduct(),
//            ":desproduct"=> $this->getdesproduct(),
//            ":vlprice"=> $this->getvlprice(),
//            ":vlwidth"=> $this->getvlwidth(),
//            ":vlheight"=> $this->getvlwidth(),
//            ":vllength"=> $this->getvllenght(),
//            ":vlweight"=> $this->getvlweight(),
//            ":desurl"=> $this->getdesurl()
//        ));
                   
        try{
            $results = $sql->select("UPDATE tb_products SET 
                     desproduct = :desproduct,
                     vlprice = :vlprice,
                     vlwidth = :vlwidth,
                     vlheight = :vlheight,
                     vllength = :vllength,
                     vlweight= :vlweight,
                     desurl = :desurl
                     where idproduct = :idproduct", array(
                                                          ":idproduct"=> $this->getidproduct(),
                                                          ":desproduct"=> $this->getdesproduct(),
                                                          ":vlprice"=> $this->getvlprice(),
                                                          ":vlwidth"=> $this->getvlwidth(),
                                                          ":vlheight"=> $this->getvlwidth(),
                                                          ":vllength"=> $this->getvllenght(),
                                                          ":vlweight"=> $this->getvlweight(),
                                                          ":desurl"=> $this->getdesurl()  
        ));
        } catch (Exception $ex) {
            throw new Exception($ex->message());
        }
        
     
        
        echo "";
//        $this->setData($results[0]);
        
        
    }
    
    
    public function create(){
                              
        $sql = new Sql(CONFIG_DB_ECOMERCE);
                
        $sql->query("INSERT into tb_products (desproduct,vlprice,vlwidth,vlheight,vllength,vlweight,desurl)
                                      VALUES (:desproduct,:vlprice,:vlwidth,:vlheight,:vllength,:vlweight,:desurl)", array(
            
            ":desproduct"=> $this->getdesproduct(),
            ":vlprice"=> $this->getvlprice(),
            ":vlwidth"=> $this->getvlwidth(),
            ":vlheight"=> $this->getvlheight(),
            ":vllength"=> $this->getvllength(),
            ":vlweight"=> $this->getvlweight(),
            ":desurl"=> $this->getdesurl()
        ));
//        
//        $sql->query("INSERT into tb_categories (descategory) VALUES (:descategory)", array(
//            ":descategory"=> $this->getdesproduct()
//        ));
//                 
//        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
//            ":idcategory"=> $this->getidcategory(),
//            ":descategory"=> $this->getdesproduct()
//        ));
        
                           
//        $this->setData($results[0]);
                                  
    }
    
    public function get($idproduct){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("select * from tb_products where idproduct = :idproduct", array(
            ":idproduct"=>$idproduct
        ));
        
        $this->setData($results[0]);
        
    }
    
    public function delete(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        try{
            $sql->query("DELETE FROM tb_products where idproduct = :idproduct", array(
            ":idproduct"=> $this->getidproduct()
            ));
        } catch (\Exception $ex) {
             echo $ex->getMessage();  
             die();
        }
        
        
        
        
       
        
    }
    
    public function checkPhoto(){
        
        if(file_exists(BASEDIR . DIRECTORY_SEPARATOR . "resorces". DIRECTORY_SEPARATOR . "site" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $this->getidproduct(). ".jpg")){
            
            $url = "/resorces/site/img/products/".$this->getidproduct() . ".jpg";
            
        }else{
            
            $url = "/resorces/site/img/products/product.jpg";
            
        }
        
        return $this->setdesphoto($url);
        
    }
    
    public function getValues() {
        
        $this->checkPhoto();
        
        $values = parent::getValues();
        
        
        
        return $values;
    }
 
    
    public function addPhoto($file){
        
        $extension = explode(".", $file["name"]);
        $extension = end($extension);
        
        switch ($extension){
            
            case "jpg":
            case "jpeg":
                $image = imagecreatefromjpeg($file["tmp_name"]);
                break;
            
            case "gif":
                $image = imagecreatefromgif($file["tmp_name"]);
                break;
            
            case "png":
                $image = imagecreatefrompng($file["tmp_name"]);
                break;                                
            
        }
        
        $destino = BASEDIR . DIRECTORY_SEPARATOR . "resorces". DIRECTORY_SEPARATOR . "site" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $this->getidproduct(). ".jpg";
        
        imagejpeg($image, $destino);
        
        imagedestroy($image);
        
        $this->checkPhoto();
    }
    
}

?>









