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
//$usuario = new Usuario();
//$usuario->login("root", "!@#$");
//echo $usuario;

// Criando um novo usuário
//$aluno = new Usuario("aluno", "@luno");
//$aluno->insert();
//echo $aluno;

// Alterar um usuário
//$usuario = new Usuario();
//$usuario->loadById(6);
//$usuario->update("professor", "!@#$");
//echo $usuario;

// Deletar usuario
$usuario = new Usuario();
$usuario->loadById(6);
$usuario->delete();
echo $usuario;

?>