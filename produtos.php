<?php 

$smarty = new Template();

$produtos = new Produtos();

if (isset($_POST['txt_buscar'])) {
    $nome = filter_var($_POST['txt_buscar'], FILTER_SANITIZE_STRING);
    $produtos->GetProdutosNome($nome);

} elseif (isset(Rotas::$pag[1])) {
    $produtos->GetProdutosCateID(Rotas::$pag[1]);

} else {
    $produtos->GetProdutos();
}


$smarty->assign('PRO', $produtos->GetItens());
$smarty->assign('PRO_INFO', Rotas::pag_ProdutosInfo());
$smarty->assign('PRO_TOTAL', $produtos->TotalDados());
$smarty->assign('PAGINAS', $produtos->ShowPaginacao());
$smarty->assign('PRODUTOS', Rotas::pag_Produtos());


$smarty->display('produtos.tpl');


?>

