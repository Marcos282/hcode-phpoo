<?php

namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;

use Hcode\Mailer;

class User extends Model {
    
    const SESSION = "User";
    const SESSION_ERROR_LOGIN = "Error_login";
    const SESSION_ERROR_REGISTER = "Error_register";
    const SESSION_SUCESS = "Sucess_msg";
    
//    const SECRET = "sENHa_cOM_16_KARACtER3s";

    public static function getFromSession(){
        
        $user = new User();
        
        if(isset($_SESSION[User::SESSION]) && $_SESSION[User::SESSION]["iduser"] > 0){
            
            
            
            $user->setData($_SESSION[User::SESSION]);
            
        }
        
        return $user;
        
    }
    
    public static function checkLogin($inadmin = true){
        
         if(
           !isset($_SESSION[User::SESSION])     
           ||
           !$_SESSION[User::SESSION]
           ||
           !(int)$_SESSION[User::SESSION]["iduser"] > 0     
  
           ){
           // Não está logado
             return FALSE;         
            } else{
                
                if($inadmin === true &&(bool)$_SESSION[User::SESSION]["inadmin"] === true ){
                    return true;
                }else if($inadmin === false){
                    return true;
                }else{
                    return false;
                }
                
            }
        
    }

    public static function  login ($login,$password){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * from tb_users where deslogin = :LOGIN",array(
            ":LOGIN"=>$login
        ));
        
        if(count($results) === 0 ){
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
        
        $data = $results[0];
        
        if(password_verify($password, $data['despassword'])=== true){
            
            $user = new User();
            
//            $user->setiduser($data['iduser']);
            
            $user->setData($data);
            
//            dd($user);
            
            $_SESSION[User::SESSION] = $user->getValues();
            
            return $user;
            
        }else{
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
        
    }
    
    public static function verifyLogin($inadmin = true){
        
        if(!User::checkLogin($inadmin)){
           
            if($inadmin){
                
                header("Location: /admin/login");
                
            }else{
                
                header("Location: /login");
                
            }
            
            
            exit();
            
        } 
    }
    
    public static function logout(){
       
        $_SESSION[User::SESSION] = NULL;
        
    }
    
    public function listAll(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
        
    }
    
    public function save(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $password = password_hash($this->getdespassword(), PASSWORD_DEFAULT, array(
        "cost" => 12
    ));
        
       # dd($this->getdesperson());
        
        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$password,
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            "inadmin"=>$this->getinadmin()
        ));
        
        $this->setData($results[0]);
    }
    
    public function get($iduser) {
       
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ":iduser"=>$iduser
        ));
        
        
        $this->setData($results[0]);
        
        
    }
    
    public function update(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
       # dd($this->getdesperson());
        
        $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"=>  $this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>User::getPassWordHash($this->getdespassword()),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            "inadmin"=>$this->getinadmin()
        ));
        
        $this->setData($results[0]);
    }
    
    public function delete(){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("CALL sp_users_delete(:iduser)",array(
           ":iduser"=> $this->getiduser() 
        ));
        
    }
    
    public static function getForgot($email, $inadmin = true){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("select * from tb_users pai inner join tb_persons fil 
			ON pai.iduser = fil.idperson
			WHERE
			fil.desemail = :email", array(
                            ":email"=>$email
                        ));
        
        if(count($results) === 0){
            
            throw new \Exception("Não foi possível recuperar a senha.");
            
        }else{
            
            $data = $results[0];
            
            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser,:desip)", array(
                ":iduser"=>$data['iduser'],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));
            
             
            if(count($results2)=== 0 ){
                                
                throw new \Exception("Não foi possível recuperar a senha.");
                
            }else{
               
//                dd($data);
                
                $dataRecovery = $results2[0];
                
                $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, SECRET, $dataRecovery['idrecovery'], MCRYPT_MODE_ECB));
                
                if($inadmin === true){
                    
                    $link = "http://brasweb.lignetbrasil.com/admin/forgot/reset?code=$code";
                    
                }else{
                    
                    $link = "http://brasweb.lignetbrasil.com/forgot/reset?code=$code";
                    
                }
                
                
                $mailer = new Mailer($data['desemail'], $data['desperson'], 'Redefinir a senha', 'forgot', array(
                    "name"=>$data['desperson'],
                    "link"=>$link
                ));
                
                $mailer->send();
                
                return $data;
            }
            
        }
    }
    
    public static function validForgotDecript($code){
        
                
        $idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, SECRET, base64_decode($code), MCRYPT_MODE_ECB);
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT
                                *
                                FROM
                                           tb_userspasswordsrecoveries pai
                                INNER JOIN tb_users fil ON pai.iduser = fil.iduser
                                INNER JOIN tb_persons net ON fil.idperson = net.idperson
                                WHERE
                                        pai.idrecovery = :idrecovery
                                AND pai.dtrecovery IS NULL
                                AND DATE_ADD(
                                        pai.dtregister,
                                        INTERVAL 1 HOUR
                                ) >= NOW();", array(
                                    ":idrecovery"=>$idrecovery
                                ));

        if(count($results)===0){
            throw new \Exception("Não foi possível recuperar a senha.");
        }else{
            return $results[0];
        }
    }
    
    public static function  setForgotUsed($idrecovery){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("UPDATE tb_userspasswordsrecoveries set dtrecovery = now() WHERE idrecovery = :idrecovery", array(
            ":idrecovery"=>$idrecovery
        ));
        
    }
    
    public function setPassword($newPassword){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $sql->query("UPDATE tb_users
                        SET despassword = :newpassword
                        WHERE
                        iduser = :iduser", array(
                            "newpassword"=>$newPassword,
                            "iduser"=> $this->getiduser()
                        ));
        
    }
    
    public static function setError($msg){
        
        $_SESSION[User::SESSION_ERROR_LOGIN]= $msg;
                                       
    }
    
    public static function getError(){
        
        $msg = (isset($_SESSION[User::SESSION_ERROR_LOGIN]) && $_SESSION[User::SESSION_ERROR_LOGIN]) ? $_SESSION[User::SESSION_ERROR_LOGIN] : ""; 
        
        User::clearError();
        
        return $msg;
        
    }

    public static function clearError() {
        
        $_SESSION[User::SESSION_ERROR_LOGIN] = null;
        
    }
    
    public static function getdesperson(){
        
        $user = User::getFromSession();


        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * FROM tb_persons where idperson = :idperson", [
            ":idperson"=>$user->getidperson()
        ]);
        
        
        if (count($results )> 0){
            
            return $results[0]["desperson"];
            
        }
        
    }
    
    public static function getPassWordHash($password){
        
        $password = password_hash($password, PASSWORD_DEFAULT, array(
        
             "cost" => 12));
                 
        return $password;
        
    }

    public static function setRegisterError($msg) {
        
        $_SESSION[User::SESSION_ERROR_REGISTER] = $msg;
        
    }
    
        public static function getErrorRegister(){
        
        $msg = (isset($_SESSION[User::SESSION_ERROR_REGISTER]) && $_SESSION[User::SESSION_ERROR_REGISTER]) ? $_SESSION[User::SESSION_ERROR_REGISTER] : ""; 
        
        User::clearErrorRegister();
        
        return $msg;
        
    }
    
    public static function clearErrorRegister() {
        
        $_SESSION[User::SESSION_ERROR_REGISTER] = null;
        
    }

    
    public static function checkLoginExist($login){
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT * from tb_users WHERE deslogin = :deslogin", [
            ":deslogin"=>$login
        ]);
        
        return (count($results) >0);
        
        
    }
    
    public function getValues() {
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results =$sql->select("SELECT * from tb_users pai inner join tb_persons fil 
	ON pai.idperson = fil.idperson
	WHERE 
	pai.idperson = :idperson", [
            ":idperson"=>14
        ]);
                           
        $this->setData($results[0]);                       
        
        $values = parent::getValues();
        
        return $values; 
    }
    
    
        public static function setSucessMsg($msg) {
        
        $_SESSION[User::SESSION_SUCESS] = $msg;
        
    }
    
        public static function getSucessMsg(){
        
        $msg = (isset($_SESSION[User::SESSION_SUCESS]) && $_SESSION[User::SESSION_SUCESS]) ? $_SESSION[User::SESSION_SUCESS] : ""; 
        
        User::clearSucessMsg();
        
        return $msg;
        
    }
    
    public static function clearSucessMsg() {
        
        $_SESSION[User::SESSION_SUCESS] = null;
        
    }

    public function getOrders() {
        
           
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        $results = $sql->select("SELECT *
                     from tb_orders pai 
                     INNER JOIN tb_ordersstatus fil USING(idstatus) 
                     INNER JOIN tb_carts net USING(idcart)
                     INNER JOIN tb_users biz ON biz.iduser = pai.iduser
                     INNER JOIN tb_addresses tara USING(idaddress)
                     INNER JOIN tb_persons hept ON tara.idperson = biz.idperson
                     WHERE 
                     pai.iduser = :iduser",[
                         ":iduser"=> $this->getiduser()
                     ]);
        
        if(count($results)> 0){
            
            return $results;
            
        }else{
            
            throw new Exception("Não foi possível realizar a consulta");
            
        }
        
    }

}

?>









