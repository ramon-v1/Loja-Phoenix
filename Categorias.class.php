<?php 

Class Categorias extends Conexao{

    // Propriedades privadas da classe
    private $cate_id, $cate_nome, $cate_slug;

    // Construtor que chama o construtor da classe pai Conexao
    function __construct(){
        parent::__construct();
    }

    // Método para buscar todas as categorias no banco
    function GetCategorias(){
        // Query para selecionar todas as categorias
        $query = "SELECT * FROM {$this->prefix}categorias";

        // Executa a query
        $this->ExecuteSQL($query);

        // Preenche a lista de itens com os dados retornados
        $this->GetLista();
    }

    // Método privado que monta a lista de categorias no formato desejado
    private function GetLista(){
        $i = 1;
        while($lista = $this->ListarDados()):
            $this->itens[$i] = array(
                'cate_id'       => $lista['cate_id'],
                'cate_nome'     => $lista['cate_nome'],
                'cate_slug'     => $lista['cate_slug'],
                'cate_link'     => Rotas::pag_Produtos() . '/' . $lista['cate_id'] . '/' . $lista['cate_slug'],
                'cate_link_adm' => Rotas::pag_ProdutosADM() . '/' . $lista['cate_id'] . '/' . $lista['cate_slug'],
            );
            $i++;
        endwhile;
    }

    // Método para inserir uma nova categoria
    function Inserir($cate_nome){
        // Tratar e sanitizar os campos
        $this->setCate_nome($cate_nome);
        $this->setCate_slug($cate_nome);

        // Monta a query SQL para inserção
        $query = "INSERT INTO {$this->prefix}categorias (cate_nome, cate_slug) ";
        $query .= "VALUES (:cate_nome, :cate_slug)";

        // Parâmetros para bind
        $params = array(
            ':cate_nome' => $this->getCate_nome(),
            ':cate_slug' => $this->getCate_slug(),
        );

        // Executa a query e retorna TRUE em caso de sucesso, FALSE caso contrário
        if($this->ExecuteSQL($query, $params)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    // Método para editar uma categoria existente
    function Editar($cate_id, $cate_nome){
        // Tratar e sanitizar os campos
        $this->setCate_nome($cate_nome);
        $this->setCate_slug($cate_nome);

        // Monta a query SQL para atualização
        $query = "UPDATE {$this->prefix}categorias ";
        $query .= "SET cate_nome = :cate_nome, cate_slug = :cate_slug ";
        $query .= "WHERE cate_id = :cate_id";

        // Parâmetros para bind
        $params = array(
            ':cate_nome' => $this->getCate_nome(),
            ':cate_slug' => $this->getCate_slug(),
            ':cate_id'   => (int)$cate_id,
        );

        // Executa a query e retorna TRUE em caso de sucesso, FALSE caso contrário
        if($this->ExecuteSQL($query, $params)):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    // Método para apagar uma categoria
    function Apagar($cate_id){
        // Verifica se existem produtos vinculados a essa categoria
        $pro = new Produtos();
        $pro->GetProdutosCateID($cate_id);

        if($pro->TotalDados() > 0):
            // Se houver produtos, não permite apagar e exibe alerta
            echo '<div class="alert alert-danger"> Esta categoria tem: ';
            echo $pro->TotalDados();
            echo ' produtos. Não pode ser apagada, para apagar precisa primeiro apagar os produtos dela </div>';
        else:
            // Se não houver produtos, monta a query para apagar a categoria
            $query = "DELETE FROM {$this->prefix}categorias WHERE cate_id = :id";

            // Parâmetros para bind
            $params = array(':id' => (int)$cate_id);

            // Executa a query e retorna TRUE em caso de sucesso, FALSE caso contrário
            if($this->ExecuteSQL($query, $params)):
                return TRUE;
            else:
                return FALSE;
            endif;
        endif;
    }

    // MÉTODOS GET
    function getCate_nome() {
        return $this->cate_nome;
    }

    function getCate_slug() {
        return $this->cate_slug;
    }

    // MÉTODOS SET
    function setCate_nome($cate_nome) {
        // Sanitiza a string para evitar injeção de código
        $this->cate_nome = filter_var($cate_nome, FILTER_SANITIZE_STRING);
    }

    function setCate_slug($cate_slug) {
        // Gera um slug limpo e amigável para URL
        $this->cate_slug = Sistema::GetSlug($cate_slug);
    }

}

?>
