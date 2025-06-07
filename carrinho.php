<?php

// Verificação de produtos existentes no carrinho
if (isset($_SESSION['PRO'])) {

    $smarty    = new Template();   // Instancia a classe Template
    $carrinho  = new Carrinho();    // Instancia a classe Carrinho

    // Assigns (atribuições) para o template
    $smarty->assign('PRO', $carrinho->GetCarrinho());
    $smarty->assign('TOTAL', Sistema::MoedaBR($carrinho->GetTotal()));
    $smarty->assign('PAG_PRODUTOS', Rotas::pag_Produtos());
    $smarty->assign('PAG_CARRINHO_ALTERAR', Rotas::pag_CarrinhoAlterar());
    $smarty->assign('PAG_CONFIRMAR', Rotas::pag_PedidoConfirmar());
    $smarty->assign('PESO', number_format($carrinho->GetPeso(), 3, '.', ''));

    // Exibe o template carrinho.tpl
    $smarty->display('carrinho.tpl');

} else {
    echo '<h4 class="alert alert-danger">Não possui produtos no carrinho!</h4>';
    Rotas::Redirecionar(3, Rotas::pag_Produtos());
}

/*
// Debug - visualizar o conteúdo do carrinho
echo '<pre>';
var_dump($carrinho->GetCarrinho());
echo '</pre>';
*/
?>
