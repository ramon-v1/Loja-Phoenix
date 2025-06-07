<?php 
// Classe para manipular os itens relacionados a pedidos, herda da classe Conexao para acesso ao banco
class Itens extends Conexao {

    // Propriedade para armazenar o valor total dos itens do pedido
    private $total_valor;

    // Construtor que chama o construtor da classe pai para inicializar a conexão
    function __construct() {
        parent::__construct();
    }

    /**
     * Busca os itens de um pedido e, opcionalmente, de um cliente específico
     * @param int $pedido Código do pedido
     * @param int|null $cliente Código do cliente (opcional)
     */
    function GetItensPedido($pedido, $cliente = null) {
        // Monta a query para obter dados das tabelas pedidos, pedidos_itens e produtos
        $query = "SELECT * FROM {$this->prefix}pedidos p, {$this->prefix}pedidos_itens i, {$this->prefix}produtos d";
        $query .= " WHERE p.ped_cod = i.item_ped_cod AND i.item_produto = d.pro_id";
        $query .= " AND p.ped_cod = :pedido";
        
        // Se cliente informado, adiciona filtro para o cliente
        if ($cliente != null) {
            // Corrigido: parametro deve ser :cliente e não {:cliente}
            $query .= " AND p.ped_cliente = :cliente";
            $params[':cliente'] = (int)$cliente;
        }  

        // Define o parâmetro do pedido
        $params[':pedido'] = (int)$pedido;

        // Executa a query com os parâmetros informados
        $this->ExecuteSQL($query, $params);

        // Processa os resultados para montar a lista de itens
        $this->GetLista();   
    }

    /**
     * Monta a lista de itens do pedido e calcula o total
     */
    private function GetLista() {
        
        $i = 1; 
        $sub = 0;

        // Loop para percorrer todos os registros retornados pela consulta
        while ($lista = $this->ListarDados()):

            // Calcula o subtotal do item: valor unitário * quantidade
            $sub = $lista['item_valor'] * $lista['item_qtd'];

            // Acumula o subtotal no total geral
            $this->total_valor += $sub;

            // Monta array com os dados do item e do pedido formatados
            $this->itens[$i] = array(
                'ped_id'          => $lista['ped_id'],
                'ped_data'        => Sistema::Fdata($lista['ped_data']), // Data formatada para exibição
                'ped_data_us'     => $lista['ped_data'],                // Data no formato original
                'ped_hora'        => $lista['ped_hora'],
                'ped_cliente'     => $lista['ped_cliente'],
                'ped_cod'         => $lista['ped_cod'],
                'ped_ref'         => $lista['ped_ref'],
                'ped_pag_status'  => $lista['ped_pag_status'],
                'ped_pag_forma'   => $lista['ped_pag_forma'],
                'ped_pag_tipo'    => $lista['ped_pag_tipo'],
                'ped_pag_codigo'  => $lista['ped_pag_codigo'],
                'ped_frete_valor' => $lista['ped_frete_valor'],
                'ped_frete_tipo'  => $lista['ped_frete_tipo'],
                'item_id'         => $lista['item_id'],
                'item_nome'       => $lista['pro_nome'],
                'item_valor'      => Sistema::MoedaBR($lista['item_valor']), // Valor formatado em moeda BR
                'item_valor_us'   => $lista['item_valor'],                   // Valor numérico real
                'item_qtd'        => $lista['item_qtd'],
                'item_img'        => Rotas::ImageLink($lista['pro_img'], 60, 60), // URL da imagem redimensionada
                'item_sub'        => Sistema::MoedaBR($sub),                 // Subtotal formatado
                'item_sub_us'     => $sub,                                   // Subtotal numérico
            );

            $i++;

        endwhile;
    }

    /**
     * Retorna o valor total dos itens do pedido
     * @return float
     */
    function GetTotal() {
        return $this->total_valor;
    }

}
?>
