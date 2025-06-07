<?php

// Cria uma nova instância da classe Template (para carregar e exibir um template .tpl)
$smarty = new Template();

// Exibe o menu do cliente logado
Login::MenuCliente();

// Se o código do pedido não foi enviado via POST, redireciona para a página de pedidos do cliente
if (!isset($_POST['cod_pedido'])) {
    Rotas::Redirecionar(1, Rotas::pag_ClientePedidos());
    exit();
}

// Cria uma instância da classe Itens para manipular os itens do pedido
$itens = new Itens();

// Filtra o código do pedido para remover caracteres indesejados (proteção contra injeções)
$pedido = filter_var($_POST['cod_pedido'], FILTER_SANITIZE_STRING);

// Busca os itens do pedido no banco de dados
$itens->GetItensPedido($pedido);

// Passa os itens e o total para o template
$smarty->assign('ITENS', $itens->GetItens());
$smarty->assign('TOTAL', $itens->GetTotal());

// Se o status do pagamento for 'NAO', mostra novamente o botão para pagar via PagSeguro
if ($itens->GetItens()[1]['ped_pag_status'] == 'NAO') {
    $_SESSION['PEDIDO_COD'] = $pedido; // Adicione esta linha

    $smarty->assign('REF', $pedido);
    $smarty->assign('TEMA', Rotas::get_SiteTEMA());
}


// Exibe o template com os dados carregados
$smarty->display('cliente_itens.tpl');

?>
