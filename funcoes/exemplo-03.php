<?php

function ola($texto = "mundo", $periodo = "Bom dia") {
    echo "Olá $texto! $periodo!<br>";
}

echo ola();
echo ola("", "Boa noite");
echo ola("Gláucio", "Boa tarde");
echo ola("João", "");

?>