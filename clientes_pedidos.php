<?php
$smarty = new Template();

Login::MenuCliente();

$pedidos = new Pedidos();

if (isset($_SESSION['PEDIDO_COD'])) {
    $pedido_cod = $_SESSION['PEDIDO_COD'];

    $dados = array(
        "ped_pag_status" => 'SIM'
    );

    $pedidos->AtualizarStatusPagamento($pedido_cod, 'SIM');

}

// buscar pedidos do cliente logado
$pedidos->GetPedidosCliente($_SESSION['CLI']['cli_id']);

$smarty->assign('PEDIDOS', $pedidos->GetItens());
$smarty->assign('PEDIDOS_QUANTIDADE', $pedidos->TotalDados());
$smarty->assign('PAG_ITENS', Rotas::pag_ClienteItens());
$smarty->assign('PAGINAS', $pedidos->ShowPaginacao());

$smarty->display('clentes_pedidos.tpl');
?>