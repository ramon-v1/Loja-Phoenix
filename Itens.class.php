<?php 

// Classe responsável por manipular os itens dos pedidos, herda da classe Conexao
class Itens extends Conexao {

    // Propriedade para armazenar o valor total dos itens
    private $total_valor;

    // Construtor chama o construtor da classe pai Conexao
    function __construct() {
        parent::__construct();
    }

    /**
     * Busca os itens de um pedido específico e opcionalmente de um cliente específico
     * @param int $pedido Código do pedido
     * @param int|null $cliente Código do cliente (opcional)
     */
    function GetItensPedido($pedido, $cliente = null) {
        // Query para buscar dados dos pedidos, itens e produtos relacionados
        $query = "SELECT * FROM {$this->prefix}pedidos p, {$this->prefix}pedidos_itens i, {$this->prefix}produtos d";
        $query .= " WHERE p.ped_cod = i.item_ped_cod AND i.item_produto = d.pro_id";
        $query .= " AND p.ped_cod = :pedido";

        // Se o cliente for informado, adiciona condição na query
        if ($cliente != null) {
            $query .= " AND p.ped_cliente = :cliente";
            $params[':cliente'] = (int)$cliente;
        }

        // Parâmetro obrigatório pedido
        $params[':pedido'] = $pedido;

        // Executa a query com os parâmetros
        $this->ExecuteSQL($query, $params);

        // Monta a lista dos itens retornados
        $this->GetLista();
    }


    // Método privado para montar a lista dos itens obtidos na query
    private function GetLista() {

        $i = 1;
        while ($lista = $this->ListarDados()):

            // Calcula subtotal do item (valor unitário * quantidade)
            $sub = $lista['item_valor'] * $lista['item_qtd'];

            // Soma o subtotal ao valor total do pedido
            $this->total_valor += $sub;

            // Monta array com dados do item para uso posterior
            $this->itens[$i] = array(
                'ped_id'           => $lista['ped_id'],
                'ped_data'         => Sistema::Fdata($lista['ped_data']),   // Data formatada
                'ped_data_us'      => $lista['ped_data'],                  // Data original
                'ped_hora'         => $lista['ped_hora'],
                'ped_cliente'      => $lista['ped_cliente'],
                'ped_cod'          => $lista['ped_cod'],
                'ped_ref'          => $lista['ped_ref'],
                'ped_pag_status'   => $lista['ped_pag_status'],
                'ped_pag_forma'    => $lista['ped_pag_forma'],
                'ped_pag_tipo'     => $lista['ped_pag_tipo'],
                'ped_pag_codigo'   => $lista['ped_pag_codigo'],
                'ped_frete_valor'  => $lista['ped_frete_valor'],
                'ped_frete_tipo'   => $lista['ped_frete_tipo'],
                'item_id'          => $lista['item_id'],
                'item_nome'        => $lista['pro_nome'],
                'item_valor'       => Sistema::MoedaBR($lista['item_valor']), // Valor formatado em moeda BR
                'item_valor_us'    => $lista['item_valor'],                  // Valor numérico
                'item_qtd'         => $lista['item_qtd'],
                'item_img'         => Rotas::ImageLink($lista['pro_img'], 60, 60), // Link da imagem redimensionada
                'item_sub'         => Sistema::MoedaBR($sub),                // Subtotal formatado
                'item_sub_us'      => $sub,                                  // Subtotal numérico
            );

            $i++;

        endwhile;
    }


    /**
     * Retorna o valor total dos itens do pedido
     * @return float Valor total
     */
    function GetTotal() {
        return $this->total_valor;
    }
}

?>
