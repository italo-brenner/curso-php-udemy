<?php

$hierarquia = array(
    array(
        'nome_cargo' => 'CEO',
        'subordinados' => array(
            array(
                'nome_cargo' => 'Diretor Comercial',
                'subordinados' => array(
                    array(
                        'nome_cargo' => 'Gerente de Vendas'
                    )
                )
            ),
            array(
                'nome_cargo' => 'Diretor Financeiro',
                'subordinados' => array(
                    array(
                        'nome_cargo' => 'Gerente de Contas a Pagar',
                        'subordinados' => array(
                            array(
                                'nome_cargo' => 'Supervisor de Pagamentos'
                            )
                        )
                    ),
                    array(
                        'nome_cargo' => 'Gerente de Compras',
                        'subordinados' => array(
                            array(
                                'nome_cargo' => 'Supervisor de Suprementos'
                            )
                        )
                    )
                )
            )
        )
    )
);

function exibe($cargos) {
    $html = '<ul>';

    foreach($cargos as $cargo) {
        $html .= '<li>';
        $html .= $cargo['nome_cargo'];
        $html .= '</li>';
        if (isset($cargo['subordinados']) && count($cargo['subordinados']) > 0) {
            $html .= exibe($cargo['subordinados']);
        }
    }

    $html .= '</ul>';
    return $html;
}

echo exibe($hierarquia);

?>