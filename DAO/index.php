<?php


require_once "config.php";
//
//$marcos = new Usuario;
//
//$marcos->loadById(1);
//        
//echo $marcos;
        
//
//$sql = new Sql();
//
//$result = $sql->select("SELECT * from cadastro");
//
////echo json_encode($result); 
//foreach ($result as $key => $value) {
//      
//    foreach ($value as $key => $valor) {
//        echo $key . ": ";
//        
//        echo $valor;
//        echo "<br>";
//    }
//    
//echo "==========================<br>";
//
//

#EXEMPLO DE LISTAGEM DE USUARIOS
//$lista = Usuario::getList();
//
//echo json_encode($lista);


#EXEMPLO DE BUSCA DE USUARIO
//$search = Usuario::search("mar");
//
//echo json_encode($search);


#EXEMPLO DE LOGIN VALIDANDO USUARIO E SENHA
//$login = new Usuario;
//$login->login("marcos", "lalalala");
//
//echo $login;

#EXEMPLO DE INSERT
//$login = new Usuario();
//$login->insert("sofia","papai");


#EXEMPLO DE UPDATE
//$usuario = new Usuario();
//$usuario->loadById(2);
//
//$usuario->update("sofia", "pai te ama");
//
//echo $usuario;
//

#EXEMPLO DE EXCLUSAO DE USUARIO
$usuario = new Usuario;
$usuario->loadById(4);
$usuario->delete();
echo $usuario;




