<?php

$conn = new PDO("mysql:host=localhost;dbname=dbphp7", "root", "");

$stmt = $conn->prepare("delete from tb_usuarios where idusuario = :ID");

$id = 1;

$stmt->bindParam(":ID", $id);

$stmt->execute();

echo "Delete OK!";

?>