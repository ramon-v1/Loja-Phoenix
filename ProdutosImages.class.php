<?php 

// Classe ProdutosImages estende Conexao para manipular imagens relacionadas a produtos
class ProdutosImages extends Conexao {

    // Método para buscar todas as imagens de um produto pelo ID do produto
    function GetImagesPRO($pro) {
        $query = "SELECT * FROM {$this->prefix}imagens WHERE img_pro_id = :pro";

        // Parâmetro para query (ID do produto)
        $params = array(':pro' => (int)$pro);

        // Executa a consulta com os parâmetros
        $this->ExecuteSQL($query, $params);

        // Preenche o array itens com os dados retornados
        $this->GetLista();
    }

    // Método privado que preenche o array $this->itens com os dados das imagens
    private function GetLista() {
        $i = 1;
        while ($lista = $this->ListarDados()):
            $this->itens[$i] = array(
                'img_id'      => $lista['img_id'],
                // Gera link da imagem redimensionada para 150x150 (miniatura)
                'img_nome'    => Rotas::ImageLink($lista['img_nome'], 150, 150),
                'img_pro_id'  => $lista['img_pro_id'],
                // Gera link da imagem redimensionada para 500x500 (visualização maior)
                'img_link'    => Rotas::ImageLink($lista['img_nome'], 500, 500),
                'img_arquivo' => $lista['img_nome'], // Nome original do arquivo da imagem
            );
            $i++;
        endwhile;
    }

    // Método para inserir uma nova imagem associada a um produto
    public function Insert($img, $produto) {
        $query = "INSERT INTO {$this->prefix}imagens (img_nome, img_pro_id) ";
        $query .= "VALUES (:img_nome, :img_pro_id)";

        $params = array(
            ':img_nome'   => $img,
            ':img_pro_id' => (int)$produto
        );

        // Executa o comando de inserção
        $this->ExecuteSQL($query, $params);
    }

    // Método para deletar uma imagem pelo nome do arquivo
    public function Deletar($img_nome) {
        $query = "DELETE FROM {$this->prefix}imagens ";
        $query .= "WHERE img_nome = :img_nome";

        $params = array(':img_nome' => $img_nome);

        // Executa o comando de deleção
        $this->ExecuteSQL($query, $params);
    }
}

?>
