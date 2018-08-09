<?php

namespace Cliente;

class Cadastro extends \Cadastro {
    
    public function registrarVenda() {
        echo "Foi registradas uma venda para o cliente " . $this->getNome();
    }
    
}