<?php
// Verifica se o ID do produto foi enviado via POST e se é válido
if (!isset($_POST['pro_id']) || (int)$_POST['pro_id'] < 1) {
    echo '<h4 class="alert alert-danger">Erro na operação: ID do produto inválido!</h4>';
    Rotas::Redirecionar(1, Rotas::pag_Carrinho());
    exit();
}

// Captura e garante que o ID do produto é um número inteiro
$id = (int)$_POST['pro_id'];

// Cria uma nova instância do carrinho
$carrinho = new Carrinho();

try {
    // Tenta adicionar o produto ao carrinho
    $carrinho->CarrinhoADD($id);
} catch (Exception $e) {
    // Em caso de erro, exibe uma mensagem amigável ao usuário
    echo '<h4 class="alert alert-danger">
            Erro na operação: Não foi possível adicionar o produto.
          </h4>';
}

// Redireciona o usuário de volta para a página do carrinho
Rotas::Redirecionar(1, Rotas::pag_Carrinho());
?>
