<?php

require_once("config.php");

// Carrega um usuario
//$root = new Usuario();
//$root->loadById(3);
//echo $root;

// Carrega uma lista de usuario
//$lista = Usuario::getList();
//echo json_encode($lista);

// Carrega uma lista de usuarios buscando pelo nome
//$search = Usuario::search("jo");
//echo json_encode($search);

// Carrega um usuário usando o login e a senha
$usuario = new Usuario();
$usuario->login("root", "!@#$");
echo $usuario;

?>