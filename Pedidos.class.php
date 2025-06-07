<?php 

class Pedidos extends Conexao {

    function __construct() {
        parent::__construct();
    }

    function PedidoGravar($cliente, $cod, $ref, $freteValor = null, $freteTipo = null) {
        $retorno = FALSE;

        $query  = "INSERT INTO " . $this->prefix . "pedidos ";   
        $query .= "(ped_data, ped_hora, ped_cliente, ped_cod, ped_ref, ped_frete_valor, ped_frete_tipo, ped_pag_status)"; 
        $query .= " VALUES ";
        $query .= "(:data, :hora, :cliente, :cod, :ref, :frete_valor, :frete_tipo, :ped_pag_status)";

        $params = array(
            ':data'          => Sistema::DataAtualUS(),
            ':hora'          => Sistema::HoraAtual(),
            ':cliente'       => (int)$cliente,
            ':cod'           => $cod,
            ':ref'           => $ref,
            ':frete_valor'   => $freteValor,
            ':frete_tipo'    => $freteTipo,
            ':ped_pag_status'=> 'NAO' 
        );

        if ($this->ExecuteSQL($query, $params)) {
            $this->ItensGravar($cod);
            $retorno = TRUE;
        }
        return $retorno;
    }

    function Atualizar($cod, $dados) {
    if (empty($cod) || empty($dados) || !is_array($dados)) {
        return FALSE; // parâmetros inválidos
    }

    // Montar dinamicamente os campos e parâmetros para o SQL
    $setPartes = [];
    $params = [];

    foreach ($dados as $campo => $valor) {
        // Previne campos inválidos (opcional: crie uma lista de campos permitidos)
        $setPartes[] = "$campo = :$campo";
        $params[":$campo"] = $valor;
    }

    // Adicionar o parâmetro do código do pedido
    $params[':cod'] = $cod;

    $setString = implode(", ", $setPartes);

    $query = "UPDATE " . $this->prefix . "pedidos SET $setString WHERE ped_cod = :cod";

    return $this->ExecuteSQL($query, $params);
}


    function GetPedidosCliente($cliente = null) {
        $query = "SELECT * FROM {$this->prefix}pedidos p INNER JOIN {$this->prefix}clientes c ON p.ped_cliente = c.cli_id";

        $params = [];
        if ($cliente !== null) {
            $query .= " WHERE ped_cliente = :cliente";
            $params[':cliente'] = (int)$cliente;
        }
        
        $query .= " ORDER BY ped_id DESC ";
        // Paginação deve ser implementada de forma segura, revise a função PaginacaoLinks para uso com parâmetros
        $query .= $this->PaginacaoLinks("ped_id", $this->prefix."pedidos" . ($cliente !== null ? " WHERE ped_cliente=".$params[':cliente'] : ""));

        $this->ExecuteSQL($query, $params);
        $this->GetLista();   
    }

    private function GetLista() {
        $i = 1;
        $this->itens = []; // garante reset da lista
        while ($lista = $this->ListarDados()):
            $this->itens[$i] = array(
                'ped_id'          => $lista['ped_id'],
                'ped_data'        => Sistema::Fdata($lista['ped_data']),
                'ped_data_us'     => $lista['ped_data'],
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
                'cli_nome'        => $lista['cli_nome'],
                'cli_sobrenome'   => $lista['cli_sobrenome'],
            );
            $i++;
        endwhile;
    }

    function GetPedidosREF($ref) {
        $query = "SELECT * FROM {$this->prefix}pedidos p INNER JOIN {$this->prefix}clientes c ON p.ped_cliente = c.cli_id";
        $query .= " WHERE ped_ref = :ref";

        // A paginação deve ser tratada com cuidado, pois concatenação direta pode ser perigosa
        $query .= $this->PaginacaoLinks("ped_id", $this->prefix."pedidos WHERE ped_ref = '".$ref."'");

        $params = array(':ref' => $ref);

        $this->ExecuteSQL($query, $params);
        $this->GetLista();
    }

    function GetPedidosDATA($data_ini, $data_fim) {
        $query = "SELECT * FROM {$this->prefix}pedidos p INNER JOIN {$this->prefix}clientes c ON p.ped_cliente = c.cli_id";
        $query .= " WHERE ped_data BETWEEN :data_ini AND :data_fim ";

        $query .= $this->PaginacaoLinks("ped_id", $this->prefix."pedidos WHERE ped_data BETWEEN '".$data_ini."' AND '".$data_fim."'");

        $params = array(':data_ini' => $data_ini, ':data_fim' => $data_fim);

        $this->ExecuteSQL($query, $params);
        $this->GetLista();
    }

    function Apagar($ped_cod) {
        $params = array(':ped_cod' => $ped_cod);

        $query =  "DELETE FROM {$this->prefix}pedidos WHERE ped_cod = :ped_cod";        
        $ret1 = $this->ExecuteSQL($query, $params);

        $query =  "DELETE FROM {$this->prefix}pedidos_itens WHERE item_ped_cod = :ped_cod";
        $ret2 = $this->ExecuteSQL($query, $params);

        return ($ret1 && $ret2);
    }

    function ItensGravar($codpedido) {
        $carrinho = new Carrinho();

        foreach ($carrinho->GetCarrinho() as $item) {
            $query  = "INSERT INTO ".$this->prefix."pedidos_itens ";
            $query .= "(item_produto, item_valor, item_qtd, item_ped_cod)";
            $query .= " VALUES (:produto, :valor, :qtd, :cod)";
                
            $params = array(
                ':produto' => $item['pro_id'],
                ':valor'   => $item['pro_valor_us'],
                ':qtd'     => (int)$item['pro_qtd'],
                ':cod'     => $codpedido  
            );

            $this->ExecuteSQL($query, $params);
        }
    }

    function LimparSessoes() {
        unset($_SESSION['PRO']);
        unset($_SESSION['PED']['pedido']);
        unset($_SESSION['PED']['ref']);
    }

    /**
     * Atualiza o status de pagamento do pedido
     * @param string $cod Código do pedido
     * @param string $novoStatus Novo status para ped_pag_status
     * @return bool TRUE se sucesso, FALSE caso contrário
     */
    function AtualizarStatusPagamento($cod, $novoStatus) {
        $query = "UPDATE " . $this->prefix . "pedidos SET ped_pag_status = :status WHERE ped_cod = :cod";
        $params = array(
            ':status' => $novoStatus,
            ':cod' => $cod
        );

        return $this->ExecuteSQL($query, $params);
    }
}

?>
