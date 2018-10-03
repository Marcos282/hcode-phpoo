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
            
            $this->setData($row);
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
    
    
    public function insert($login,$senha) {
    
        $this->setDeslogin($login);
        $this->setDessenha($senha);
        
        $sql = new Sql();
        
        $sql->query("INSERT INTO tb_cadastro (id,login,senha) values(:LOGIN,:SENHA)",array(
            
            ":LOGIN"=> $this->getDeslogin(),
            ":SENHA"=> $this->getDessenha()
        ));
        
        
    }
    
    
    public function update($login,$password) {
        
        $this->setDeslogin($login);
        $this->setDessenha($password);
        
        $sql = new Sql();
        
        $sql->query("UPDATE tb_usuario SET login = :LOGIN, senha = :SENHA WHERE id = :ID", array(
            ":LOGIN"=> $this->getDeslogin(),
            ":SENHA"=> $this->getDessenha()
        ));
        
    }
    
    public function delete() {
        
        $sql = new Sql();
        
        $sql->query("DELETE from tb_usuario where id = :ID",array(
            ':ID'=> $this->getIdusuario()
        ));
        
        $this->setIdusuario(0);
        $this->setDeslogin("");
        $this->setDessenha("");
        $this->setDtcadastro(new DateTime());
                
    }
    
    public function login($login,$password) {
        $sql = new Sql();
        
        $result = $sql->select("SELECT * from tb_usuario WHERE login = :LOGIN and senha = :PASSWORD",array(
            ":LOGIN"=>$login,
            ":PASSWORD"=>$password
        ));
                
        if(count($result[0])> 0){
            $row = $result[0];
            
            $this->setData($row);
        }else{
            throw new Exception("Usuário ou senha inválidos");
        }
    }
    
    public function setData($data) {
            $this->setIdusuario($data['id']);
            $this->setDeslogin($data['login']);
            $this->setDessenha($data['senha']);
            $this->setDtcadastro($data['dtcadastro']);
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