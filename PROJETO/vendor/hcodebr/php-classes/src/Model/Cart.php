<?php

namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Model\Product;
use Hcode\Model\User;

use Hcode\Mailer;

class Cart extends Model {

    CONST SESSION = "Cart";
    CONST SESSION_ERROR = "MSG_ERROR";
    
    
    private $error;


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
        
        
        $this->getCalculatorTotal();
        
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
        
        $this->getCalculatorTotal();
        
        
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
    
    
    public function getProductsTotals(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT
                            SUM(pai.vlprice) AS vlprice,
                            SUM(pai.vlheight) AS vlheight,
                            SUM(pai.vllength) AS vllength,
                            SUM(pai.vlweight) AS vlweight,
                            SUM(pai.vlwidth) AS vlwidth,
                            count(*) AS nrqtd
                    FROM
                            tb_products pai
                    INNER JOIN tb_cartsproducts fil ON pai.idproduct = fil.idproduct
                    AND fil.idcart = :idcart
                    AND fil.dtremoved IS NULL;", [
                        ":idcart"=> $this->getidcart()
                    ]);
        
        return (count($results) > 0) ? $results[0] : [];
            
        }

    public function setFreight($nrzipcode) {
        
        $nrzipcode = str_replace(array(".",",","-"),array("","",""), $nrzipcode);
        
        $totals = $this->getProductsTotals();
        
//        dd($totals);
        
        if(count($totals['nrqtd'])>0){
            
            if ($totals["vllength"] < 16) $totals["vllength"] = 16;
            if ($totals["vlheight"] <  2) $totals["vllength"] =  2;
            
            $qs = http_build_query([
                "nCdEmpresa"=>"oi",
                "sDsSenha"=>"",
                "nCdServico"=>"40010",
                "sCepOrigem"=>"24736020",
                "sCepDestino"=>$nrzipcode,
                "nVlPeso"=>$totals["vlweight"],
                "nCdFormato"=>"1",
                "nVlComprimento"=>$totals["vllength"],
                "nVlAltura"=>$totals["vlheight"],
                "nVlLargura"=>$totals["vlwidth"],
                "nVlDiametro"=>"0",
                "sCdMaoPropria"=>"S",
                "nVlValorDeclarado"=>$totals["vlprice"],
                "sCdAvisoRecebimento"=>"S"
            ]);
            
            $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);
            
            
            
            $results = $xml->Servicos->cServico;
            
            
            
//            Cart::setMsgError("um erro setado");                
            
//            dd(Cart::getMsgError());
            
            if($results->MsgErro != ""){
                
                Cart::setMsgError($results->MsgErro);                
                
                
            }else{
                
                $this->clearMsgError();
                
            }
            
            $this->setnrdays($results->PrazoEntrega);
            $this->setzipcode($nrzipcode);          
            $this->setvlfreight(Cart::formatValueToDecimal($results->Valor));
            
            $this->save();
                                    
            return $results;
                        
            
        }else{
            
            echo "error";
        }                                        
            
    }
    
    
    
    public function setError($msg){
        
        $this->error = $msg;
        
    }
    
    public function getError(){
        
        return $this->error;
        
    }
    
    public static function setMsgError($msg){
        
        $_SESSION[Cart::SESSION_ERROR] = $msg;
        
    }
    
    public static function getMsgError(){
    
//        return $_SESSION[Cart::SESSION_ERROR];
            
        $msg = (isset($_SESSION[Cart::SESSION_ERROR])) ? $_SESSION[Cart::SESSION_ERROR] : "";
//
        Cart::clearMsgError();
//
        return $msg;
        
    }
    public static function clearMsgError(){
        
        unset($_SESSION[Cart::SESSION_ERROR]);
        
    }

        
    public static function formatValueToDecimal($value){
        
        $value = str_replace(".", "", $value);
        return str_replace(",", ".", $value);
                
    }

    public function updateFreight() {
        
        if($this->getdeszipcode() != ""){
            
            $this->setFreight($this->getdeszipcode());
            
        }
        
        
    }

    
    public function getValues() {
        
        $this->getCalculatorTotal();
        
        return parent::getValues();
    }

    public function getCalculatorTotal() {
        
        $this->updateFreight();
        
        $totals = $this->getProductsTotals();
        
        $this->setvlsubtotal($totals['vlprice']);
        $this->setvltotal($totals['vlprice'] + $this->getvlfreight());
        
    }

}

?>









