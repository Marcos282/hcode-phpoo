<?php

class Usuario{
    private $idusuario;
    private $deslogin;
    private $dessenha;
    private $dtcadastro;
           
    function getIdusuario() {
        return $this->idusuario;
    }

    function getDeslogin() {
        return $this->deslogin;
    }

    function getDessenha() {
        return $this->dessenha;
    }

    function getDtcadastro() {
        return $this->dtcadastro;
    }

    function setIdusuario($idusuario) {
        $this->idusuario = $idusuario;
    }

    function setDeslogin($deslogin) {
        $this->deslogin = $deslogin;
    }

    function setDessenha($dessenha) {
        $this->dessenha = $dessenha;
    }

    function setDtcadastro($dtcadastro) {
        $this->dtcadastro = $dtcadastro;
    }

    public function loadById($id) {
        $sql = new Sql();
        
        $result = $sql->select("SELECT * from tb_usuario WHERE id = :ID",array(
            ":ID"=>$id
        ));
                
        if(count($result[0])> 0){
            $row = $result[0];
            
            $this->setIdusuario($row['id']);
            $this->setDeslogin($row['login']);
            $this->setDessenha($row['senha']);
            $this->setDtcadastro($row['dtcadastro']);
        }
    }
    
    // Como não tem nenhuma atributo dentro da classe ($this) podemos colocar ela como static
    
    public static function getList() {
        
        $sql = new Sql();
        
        return $sql->select("SELECT * from tb_usuario");
    }
    
    public static function search($login) {
        
        $sql = new Sql();
        
        return $sql->select("SELECT * from tb_usuario WHERE login like :SEARCH order by login",array(
            ':SEARCH'=>"%".$login."%"
        ));
        
    }
    
    
        public function login($login,$password) {
        $sql = new Sql();
        
        $result = $sql->select("SELECT * from tb_usuario WHERE login = :LOGIN and senha = :PASSWORD",array(
            ":LOGIN"=>$login,
            ":PASSWORD"=>$password
        ));
                
        if(count($result[0])> 0){
            $row = $result[0];
            
            $this->setIdusuario($row['id']);
            $this->setDeslogin($row['login']);
            $this->setDessenha($row['senha']);
            $this->setDtcadastro($row['dtcadastro']);
        }else{
            throw new Exception("Usuário ou senha inválidos");
        }
    }
    
    public function __toString() {
        return json_encode(array(
            "id"=>  $this->getIdusuario(),
            "login"=>  $this->getDeslogin(),
            "senha"=>  $this->getDessenha(),
            "dtcadastro"=>  $this->getDtcadastro(),
        ));
    }
       
}


?>