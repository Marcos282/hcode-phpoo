<?php

namespace Hcode\Model;
use Hcode\Model;
use Hcode\DB\Sql;

class Address extends Model {
 
    public static function getCEP($cep){
        
        $cep = str_replace(["-","."],["",""], $cep);
        
        #https://viacep.com.br/ws/01001000/json/
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/$cep/json/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $data = json_decode(curl_exec($ch), true);
        
        
        
        curl_close($ch);
        
        return $data;
    }

    public function loadFromCEP($cep) {
        
        $data = new Address();
        
        $data = Address::getCEP($cep);
        
        
        if(isset($data["logradouro"]) && $data["logradouro"]){                                                
                
            
            $this->setdesaddress($data["logradouro"]);
            $this->setdescomplement($data["complemento"]);
            $this->setdesdistrict($data["bairro"]);
            $this->setdescity($data["localidade"]);
            $this->setdesstate($data["uf"]);
            $this->setdescountry("Brasil");
            $this->setdeszipcode($cep);
            
        }
        
    }
    
    public function save(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson,:desaddress,:descomplement,:descity,:desstate, :descountry,:deszipcode,:desdistrict)",[
            ":idaddress"=>$this->getidaddress(),
            ":idperson"=>$this->getidperson(),
            ":desaddress"=>$this->getdesaddress(),
            ":descomplement"=>$this->getdescomplement(),
            ":descity"=>$this->getdescity(),
            ":desstate"=>$this->getdesstate(),
            ":descountry"=>$this->getdescountry(),
            ":deszipcode"=>$this->getdeszipcode(),
            ":desdistrict"=>$this->getdesdistrict()            
        ]);
        
        if(count($results) > 0){
            
            $this->setData($results[0]);
            
        }
        
    }

}

?>

