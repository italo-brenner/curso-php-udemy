<?php

$id = (isset($_GET["id"])) ? $_GET["id"] : 3;

if (! is_numeric($id) || strlen($id) > 5) {
    exit("Pegamos você");
}

$conn = mysqli_connect("localhost", "root", "", "dbphp7");

$sql = "select * from tb_usuarios where idusuario = $id";

$exec = mysqli_query($conn, $sql);

while ($resultado = mysqli_fetch_object($exec)) {
    echo $resultado->deslogin . '<br>';
}

?>