<?php

class Carrinho
{
    // Total acumulado do valor dos produtos no carrinho (em dólar)
    private $total_valor = 0;

    // Total acumulado do peso dos produtos no carrinho
    private $total_peso = 0;

    // Array dos itens adicionados no carrinho
    private $itens = array();

    // Retorna a lista de itens do carrinho com cálculo de subtotal e peso
    function GetCarrinho($sessao = NULL)
    {
        $i = 1;
        $sub = 1.00;
        $peso = 0;

        if (!isset($_SESSION['PRO']) || count($_SESSION['PRO']) == 0) {
            echo '<h4 class="alert alert-danger"> Não há produtos no carrinho </h4>';
            return array(); // Retorna array vazio para evitar erros no frontend
        }

        foreach ($_SESSION['PRO'] as $lista) {
            // Calcula subtotal do produto (valor em dólar * quantidade)
            $sub = ($lista['VALOR_US'] * $lista['QTD']);
            $this->total_valor += $sub;

            // Calcula peso total do produto (peso unitário * quantidade)
            $peso = $lista['PESO'] * $lista['QTD'];
            $this->total_peso += $peso;

            // Monta array de itens formatados
            $this->itens[$i] = array(
                'pro_id'          => $lista['ID'],
                'pro_nome'        => $lista['NOME'],
                'pro_valor'       => $lista['VALOR'],        // Formato brasileiro (ex: 1.000,99)
                'pro_valor_us'    => $lista['VALOR_US'],     // Valor em dólar (ex: 1000.99)
                'pro_peso'        => $lista['PESO'],
                'pro_qtd'         => $lista['QTD'],
                'pro_img'         => $lista['IMG'],
                'pro_link'        => $lista['LINK'],
                'pro_subTotal'    => Sistema::MoedaBR($sub), // Formata subtotal para real
                'pro_subTotal_us' => $sub                     // Subtotal em dólar
            );
            $i++;
        }

        return $this->itens;
    }

    // Retorna o valor total do carrinho (em dólar)
    function GetTotal()
    {
        return $this->total_valor;
    }

    // Retorna o peso total do carrinho
    function GetPeso()
    {
        return $this->total_peso;
    }

    /**
     * Adiciona, remove ou limpa produtos do carrinho com base na ação
     * Recebe o ID do produto para manipular.
     */
    function CarrinhoADD($id)
    {
        $produtos = new Produtos();
        $produtos->GetProdutosID($id);

        foreach ($produtos->GetItens() as $pro) {
            $ID       = $pro['pro_id'];
            $NOME     = $pro['pro_nome'];
            $VALOR_US = $pro['pro_valor_us'];
            $VALOR    = $pro['pro_valor'];
            $PESO     = $pro['pro_peso'];
            $QTD      = 1;  // Quantidade padrão para adicionar
            $IMG      = $pro['pro_img_p'];
            $LINK     = Rotas::pag_ProdutosInfo() . '/' . $ID . '/' . $pro['pro_slug'];
            $ACAO     = $_POST['acao'] ?? null;  // Verifica se 'acao' existe no POST para evitar warning
        }

        switch ($ACAO) {
            case 'add':
                // Se produto ainda não está no carrinho, adiciona
                if (!isset($_SESSION['PRO'][$ID]['ID'])) {
                    $_SESSION['PRO'][$ID] = array(
                        'ID'       => $ID,
                        'NOME'     => $NOME,
                        'VALOR'    => $VALOR,
                        'VALOR_US' => $VALOR_US,
                        'PESO'     => $PESO,
                        'QTD'      => $QTD,
                        'IMG'      => $IMG,
                        'LINK'     => $LINK,
                    );
                } else {
                    // Se já existe, incrementa a quantidade
                    $_SESSION['PRO'][$ID]['QTD'] += $QTD;
                }

                echo '<h4 class="alert alert-success"> Produto Inserido! </h4>';
                break;

            case 'del':
                // Remove o produto do carrinho
                $this->CarrinhoDEL($id);
                echo '<h4 class="alert alert-success"> Produto Removido! </h4>';
                break;

            case 'limpar':
                // Limpa todo o carrinho
                $this->CarrinhoLimpar();
                echo '<h4 class="alert alert-success"> Produtos Removidos! </h4>';
                break;

            default:
                echo '<h4 class="alert alert-warning"> Ação inválida! </h4>';
                break;
        }
    }

    // Remove um produto específico do carrinho pela ID
    private function CarrinhoDEL($id)
    {
        if (isset($_SESSION['PRO'][$id])) {
            unset($_SESSION['PRO'][$id]);
        }
    }

    // Limpa todo o carrinho
    private function CarrinhoLimpar()
    {
        unset($_SESSION['PRO']);
    }
}

?>
