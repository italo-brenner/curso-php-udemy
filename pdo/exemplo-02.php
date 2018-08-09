<?php

$conn = new PDO("sqlsrv:Server=PYXDESK02;Database=dbphp7;ConnectionPooling=0", "userphp", "pass");

$stmt = $conn->prepare("select * from tb_usuarios order by deslogin");

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {

    foreach ($row as $key => $value) {
        echo "<strong>$key</strong>$value<br>";
    }

    echo "====================================================<br>";
}

?>