<?php 
// Classe Produtos estende a classe Conexao para manipulação dos dados dos produtos no banco
class Produtos extends Conexao{

    // Atributos privados da classe referentes às colunas da tabela produtos
    private $pro_nome, $pro_categoria, $pro_ativo, $pro_modelo, $pro_ref, 
            $pro_valor, $pro_estoque, $pro_peso , $pro_altura, $pro_largura, $pro_comprimento,
            $pro_img, $pro_desc, $pro_slug;

    // Construtor que chama o construtor da classe pai (Conexao)
    function __construct(){
        parent::__construct();
    }

    // Método para obter todos os produtos com suas categorias
    function GetProdutos(){
        $query = "SELECT * FROM {$this->prefix}produtos p INNER JOIN {$this->prefix}categorias c ON p.pro_categoria = c.cate_id";
        $query .= " ORDER BY pro_id DESC";
        $query .= $this->PaginacaoLinks("pro_id", $this->prefix."produtos");

        $this->ExecuteSQL($query);
        $this->GetLista();
    }

    // Método para obter produto específico pelo ID
    function GetProdutosID($id){
        $query = "SELECT * FROM {$this->prefix}produtos p INNER JOIN {$this->prefix}categorias c ON p.pro_categoria = c.cate_id";
        // Atenção: condição correta deve usar WHERE, não AND isolado
        $query .= " WHERE pro_id = :id";

        $params = array(':id'=>(int)$id);

        $this->ExecuteSQL($query, $params);
        $this->GetLista();
    }

    // Método para obter produtos de uma categoria específica
    function GetProdutosCateID($id){
        // Sanitiza ID para garantir que seja número inteiro
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM {$this->prefix}produtos p INNER JOIN {$this->prefix}categorias c ON p.pro_categoria = c.cate_id";
        // Novamente, usar WHERE para condição inicial
        $query .= " WHERE pro_categoria = :id";

        $query .= $this->PaginacaoLinks("pro_id", $this->prefix."produtos WHERE pro_categoria=".(int)$id);

        $params = array(':id'=>(int)$id);

        $this->ExecuteSQL($query, $params);
        $this->GetLista();
    }

    // Método para buscar produtos pelo nome (usando LIKE)
    function GetProdutosNome($nome){
        $query = "SELECT * FROM {$this->prefix}produtos p INNER JOIN {$this->prefix}categorias c ON p.pro_categoria = c.cate_id";
        $query .= " WHERE pro_nome LIKE '%$nome%'"; // Atenção: possível SQL Injection aqui, ideal usar bind param

        $query .= $this->PaginacaoLinks("pro_id", $this->prefix."produtos WHERE pro_nome LIKE '%$nome%'");

        $params = array(':nome'=>$nome); // Não usado no SQL, está sem bind param

        $this->ExecuteSQL($query,$params);
        $this->GetLista();
    }

    // Método privado que preenche o array itens com os resultados da consulta
    private function GetLista(){
        $i = 1;
        while($lista = $this->ListarDados()):
            $this->itens[$i] = array(
                'pro_id' => $lista['pro_id'],
                'pro_nome' => $lista['pro_nome'],  
                'pro_desc' => $lista['pro_desc'],  
                'pro_peso' => $lista['pro_peso'],  
                'pro_valor' => Sistema::MoedaBR($lista['pro_valor']),  // Valor formatado para BRL
                'pro_valor_us' => $lista['pro_valor'],  // Valor bruto para cálculos
                'pro_altura' => $lista['pro_altura'],  
                'pro_largura' => $lista['pro_largura'],  
                'pro_comprimento' => $lista['pro_comprimento'],  
                'pro_img' => Rotas::ImageLink($lista['pro_img'], 180, 180),  // Imagem em tamanho médio
                'pro_img_g' => Rotas::ImageLink($lista['pro_img'], 300, 300), // Imagem grande
                'pro_img_p' => Rotas::ImageLink($lista['pro_img'], 70, 70),   // Imagem pequena
                'pro_slug' => $lista['pro_slug'], 
                'pro_ref' => $lista['pro_ref'], 
                'cate_nome' => $lista['cate_nome'], 
                'cate_id' => $lista['cate_id'],
                'pro_modelo' => $lista['pro_modelo'],  
                'pro_estoque' => $lista['pro_estoque'],  
                'pro_ativo' => $lista['pro_ativo'], 
                'pro_img_arquivo' => Rotas::get_SiteRAIZ() .'/'. Rotas::get_ImagePasta().$lista['pro_img'], 
                'pro_img_atual' => $lista['pro_img'],   
            );
            $i++;
        endwhile;
    }

    // Método para preparar o objeto com valores antes de inserir ou alterar
    function Preparar($pro_nome, $pro_categoria, $pro_ativo, $pro_modelo, $pro_ref, 
            $pro_valor, $pro_estoque, $pro_peso , $pro_altura, $pro_largura, $pro_comprimento,
            $pro_img, $pro_desc, $pro_slug=null){
        
        $this->setPro_nome($pro_nome);
        $this->setPro_categoria($pro_categoria);
        $this->setPro_ativo($pro_ativo);
        $this->setPro_modelo($pro_modelo);
        $this->setPro_ref($pro_ref);
        $this->setPro_valor($pro_valor);
        $this->setPro_estoque($pro_estoque);
        $this->setPro_peso($pro_peso);
        $this->setPro_altura($pro_altura);
        $this->setPro_largura($pro_largura);
        $this->setPro_comprimento($pro_comprimento);
        $this->setPro_img($pro_img);
        $this->setPro_desc($pro_desc);
        $this->setPro_slug($pro_nome);
    }

    // Método para inserir um produto no banco
    function Inserir(){
        $query = "INSERT INTO {$this->prefix}produtos (pro_nome, pro_categoria, pro_ativo, pro_modelo, pro_ref,";
        $query .= " pro_valor, pro_estoque, pro_peso , pro_altura, pro_largura, pro_comprimento ,pro_img, pro_desc, pro_slug)";
        $query .= " VALUES ";
        $query .= " (:pro_nome, :pro_categoria, :pro_ativo, :pro_modelo, :pro_ref, :pro_valor, :pro_estoque, :pro_peso ,";
        $query .= " :pro_altura, :pro_largura, :pro_comprimento ,:pro_img, :pro_desc, :pro_slug)";

        $params = array(
            ':pro_nome'=> $this->getPro_nome(),   
            ':pro_categoria'=> $this->getPro_categoria(),   
            ':pro_ativo'=> $this->getPro_ativo(),   
            ':pro_modelo'=> $this->getPro_modelo(),   
            ':pro_ref'=> $this->getPro_ref(),   
            ':pro_valor'=> $this->getPro_valor(),   
            ':pro_estoque'=> $this->getPro_estoque(),   
            ':pro_peso'=> $this->getPro_peso(),   
            ':pro_altura'=> $this->getPro_altura(), 
            ':pro_largura'=> $this->getPro_largura(),
            ':pro_comprimento'=> $this->getPro_comprimento(),   
            ':pro_img'=> $this->getPro_img(),   
            ':pro_desc'=> $this->getPro_desc(),   
            ':pro_slug'=> $this->getPro_slug(),   
        );

        if($this->ExecuteSQL($query, $params)):
            return TRUE;
        else:
            return FALSE; 
        endif;
    }

    // Método para alterar um produto existente pelo ID
    function Alterar($id){
        $query = "UPDATE {$this->prefix}produtos SET pro_nome=:pro_nome, pro_categoria=:pro_categoria,";
        $query .= " pro_ativo=:pro_ativo, pro_modelo=:pro_modelo, pro_ref=:pro_ref,";
        $query .= " pro_valor=:pro_valor, pro_estoque=:pro_estoque, pro_peso=:pro_peso ,";
        $query .= " pro_altura=:pro_altura, pro_largura=:pro_largura,";
        $query .= " pro_comprimento=:pro_comprimento ,pro_img=:pro_img, pro_desc=:pro_desc, pro_slug=:pro_slug";
        $query .= " WHERE pro_id = :pro_id";

        $params = array(
            ':pro_nome'=> $this->getPro_nome(),   
            ':pro_categoria'=> $this->getPro_categoria(),   
            ':pro_ativo'=> $this->getPro_ativo(),   
            ':pro_modelo'=> $this->getPro_modelo(),   
            ':pro_ref'=> $this->getPro_ref(),   
            ':pro_valor'=> $this->getPro_valor(),   
            ':pro_estoque'=> $this->getPro_estoque(),   
            ':pro_peso'=> $this->getPro_peso(),   
            ':pro_altura'=> $this->getPro_altura(), 
            ':pro_largura'=> $this->getPro_largura(),
            ':pro_comprimento'=> $this->getPro_comprimento(),   
            ':pro_img'=> $this->getPro_img(),   
            ':pro_desc'=> $this->getPro_desc(),   
            ':pro_slug'=> $this->getPro_slug(),   
            ':pro_id'=> (int)$id,   
        );

        if($this->ExecuteSQL($query, $params)):
            return TRUE;
        else:
            return FALSE; 
        endif;
    }

    // Método para apagar um produto pelo ID
    function Apagar($id){
        $query = "DELETE FROM {$this->prefix}produtos WHERE pro_id = :id";
        $params = array(':id' => (int)$id);

        if($this->ExecuteSQL($query, $params)):
            return TRUE;
        else:
            return FALSE; 
        endif;
    }

    // =================
    // MÉTODOS GET
    // =================

    function getPro_nome() {
        return $this->pro_nome;
    }

    function getPro_categoria() {
        return $this->pro_categoria;
    }

    function getPro_ativo() {
        return $this->pro_ativo;
    }

    function getPro_modelo() {
        return $this->pro_modelo;
    }

    function getPro_ref() {
        return $this->pro_ref;
    }

    function getPro_valor() {
        return $this->pro_valor;
    }

    function getPro_estoque() {
        return $this->pro_estoque;
    }

    function getPro_peso() {
        return $this->pro_peso;
    }

    function getPro_altura() {
        return $this->pro_altura;
    }

    function getPro_largura() {
        return $this->pro_largura;
    }

    function getPro_comprimento() {
        return $this->pro_comprimento;
    }

    function getPro_img() {
        return $this->pro_img;
    }
    function getPro_desc() {
        return $this->pro_desc;
    }

    function getPro_slug() {
        return $this->pro_slug;
    }





    //MÉTODOS SET

    function setPro_nome($pro_nome) {
        $this->pro_nome = $pro_nome;
    }

    function setPro_categoria($pro_categoria) {
        $this->pro_categoria = $pro_categoria;
    }

    function setPro_ativo($pro_ativo) {
        $this->pro_ativo = $pro_ativo;
    }

    function setPro_modelo($pro_modelo) {
        $this->pro_modelo = $pro_modelo;
    }

    function setPro_ref($pro_ref) {
        $this->pro_ref = $pro_ref;
    }

    function setPro_valor($pro_valor) {
        //1.335,99 => 1.33599
        
        // procura a virgula e troca por ponto
      $pro_valor = str_replace('.', '', $pro_valor); 
      $pro_valor = str_replace(',', '.', $pro_valor); 
       
        $this->pro_valor = $pro_valor ;
       // echo $this->pro_valor;
        
    }
    
    function setPro_estoque($pro_estoque) {
        $this->pro_estoque = $pro_estoque;
    }

    function setPro_peso($pro_peso) {
      
       ///  1,600  => 1.600
        $this->pro_peso = str_replace(',', '.', $pro_peso);
   
    }

    function setPro_altura($pro_altura) {
       
        $this->pro_altura = $pro_altura;
    }

    function setPro_largura($pro_largura) {
        $this->pro_largura = $pro_largura;
    }

    function setPro_comprimento($pro_comprimento) {
        $this->pro_comprimento = $pro_comprimento;
    }

    function setPro_img($pro_img) {
        $this->pro_img = $pro_img;
    }

    function setPro_desc($pro_desc) {
        $this->pro_desc = $pro_desc;
    }

    function setPro_slug($pro_slug) {
       
        
        $this->pro_slug = Sistema::GetSlug($pro_slug);
    }





}

?>
