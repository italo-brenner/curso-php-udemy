<?php

$conn = new PDO("mysql:host=localhost;dbname=dbphp7", "root", "");

$conn->beginTransaction();

$stmt = $conn->prepare("delete from tb_usuarios where idusuario = ?");

$id = 2;

$stmt->execute(array($id));

//$conn->rollBack();
$conn->commit();

echo "Delete OK!";

?>
