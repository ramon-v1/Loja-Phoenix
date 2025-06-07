<?php

if (isset($_SESSION['PRO'])) {

    $smarty = new Template();
    $carrinho = new Carrinho();

    $itens = $carrinho->GetCarrinho();

    // Calcular subtotal do carrinho
    $subtotal_carrinho = 0;
    foreach ($itens as $item) {
        $subtotal_carrinho += $item['pro_subTotal_us'];
    }

    // Exemplo fixo de frete (substitua com cálculo real)
    $frete_para_calculo = isset($_SESSION['frete_escolhido_valor']) ? $_SESSION['frete_escolhido_valor'] : 0.00;

    $total_final_pedido = $subtotal_carrinho + $frete_para_calculo;

    $smarty->assign('PRO', $itens);
    $smarty->assign('frete_para_calculo', number_format($frete_para_calculo, 2, ',', '.'));
    $smarty->assign('TOTAL', number_format($subtotal_carrinho, 2, ',', '.'));
    $smarty->assign('TOTAL_COM_FRETE', number_format($total_final_pedido, 2, ',', '.'));

    $smarty->assign('PAG_CARRINHO_ALTERAR', Rotas::pag_CarrinhoAlterar());
    $smarty->assign('PAG_CARRINHO', Rotas::pag_Carrinho());
    $smarty->assign('PAG_FINALIZAR', Rotas::pag_PedidoFinalizar());

    $smarty->display('pedido_confirmar.tpl');

} else {
    echo '<h4 class="alert alert-danger"> Não possui produtos no carrinho! </h4>';
    Rotas::Redirecionar(3, Rotas::pag_Produtos());
}
if (isset($_SESSION['PEDIDO_COD'])) {
    $pedido_cod = $_SESSION['PEDIDO_COD'];

    $pedidos = new Pedidos();

    $dados = array(
        "ped_pag_status" => 'SIM'
    );

    // Aqui está a linha que faltava
    $pedidos->Atualizar($pedido_cod, $dados);

    // unset($_SESSION['PEDIDO_COD']); // Opcional, se quiser limpar após o pagamento
}
/*
echo '<pre>';
var_dump($carrinho->GetCarrinho());
echo '</pre>';
*/
?>
