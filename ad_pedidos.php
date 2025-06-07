<?php
// Verifica se existe um parâmetro na URL (ex: /cliente_pedidos/15)
if (isset(Rotas::$pag[1])) {
    
    // Converte o parâmetro para inteiro (evita injeção ou erro de tipo)
    $id = (int)Rotas::$pag[1];
    
    // Busca os pedidos do cliente pelo ID informado
    $pedidos->GetPedidosCliente($id);

// Caso contrário, verifica se o formulário de pesquisa por data foi enviado
} elseif (isset($_POST['data_ini']) and isset($_POST['data_fim'])) {
    
    // Busca os pedidos dentro do intervalo de datas especificado
    $pedidos->GetPedidosData($_POST['data_ini'], $_POST['data_fim']);
}
?>