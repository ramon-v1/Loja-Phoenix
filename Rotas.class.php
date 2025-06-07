<?php 

// Classe Rotas: centraliza URLs e caminhos do sistema, facilitando a manutenção das rotas
class Rotas {

    // Variáveis estáticas para armazenar partes fixas das rotas e pastas
    public static $pag;
    private static $pasta_controller = 'controller';
    private static $pasta_view = 'view';
    private static $pasta_ADM = 'adm';

    // Retorna a URL base do site (exemplo: http://meusite.com/pasta)
    static function get_SiteHOME() {
        return Config::SITE_URL . '/' . Config::SITE_PASTA;
    }

    // Retorna o caminho raiz do site no servidor (exemplo: /var/www/html/pasta)
    static function get_SiteRAIZ() {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . Config::SITE_PASTA;
    }

    // Retorna o caminho para a pasta view (tema do site)
    static function get_SiteTEMA() {
        return self::get_SiteHOME() . '/' . self::$pasta_view;
    }

    // Rotas públicas para páginas comuns do site (clientes, produtos, carrinho etc.)
    static function pag_ClienteCadastro() {
        return self::get_SiteHOME() . '/cadastro';
    }

    static function pag_CLienteDados() {
        return self::get_SiteHOME() . '/clientes_dados';
    }

    static function pag_CLienteSenha() {
        return self::get_SiteHOME() . '/clientes_senha';
    }

    static function pag_ClienteRecovery() {
        return self::get_SiteHOME() . '/clientes_recovery';
    }

    static function pag_CLientePedidos() {
        return self::get_SiteHOME() . '/clientes_pedidos';
    }

    static function pag_ClienteItens() {
        return self::get_SiteHOME() . '/cliente_itens';
    }

    static function pag_Carrinho() {
        return self::get_SiteHOME() . '/carrinho';
    }

    static function pag_ClienteLogin() {
        return self::get_SiteHOME() . '/login';
    }

    static function pag_Logoff() {
        return self::get_SiteHOME() . '/logoff';
    }

    static function pag_CarrinhoAlterar() {
        return self::get_SiteHOME() . '/carrinho_alterar';
    }

    static function pag_Produtos() {
        return self::get_SiteHOME() . '/produtos';
    }

    static function pag_ProdutosInfo() {
        return self::get_SiteHOME() . '/produtos_info';
    }

    static function pag_Contato() {
        return self::get_SiteHOME() . '/contato';
    }

    static function pag_MinhaConta() {
        return self::get_SiteHOME() . '/minhaconta';
    }

    static function pag_ClienteConta() {
        return self::get_SiteHOME() . '/minhaconta';
    }

    static function pag_PedidoConfirmar() {
        return self::get_SiteHOME() . '/pedido_confirmar';
    }

    static function pag_PedidoFinalizar() {
        return self::get_SiteHOME() . '/pedido_finalizar';
    }

    static function pag_PedidoRetorno() {
        return self::get_SiteHOME() . '/pedido_retorno';
    }

    static function pag_PedidoRetornoERRO() {
        return self::get_SiteHOME() . '/pedido_retorno_erro';
    }

    // Pasta onde ficam as imagens
    static function get_ImagePasta() {
        return 'media/images/';
    }

    // URL base para as imagens do site
    static function get_ImageURL() {
        return self::get_SiteHOME() . '/' . self::get_ImagePasta();
    }

    // ROTAS PARA ÁREA ADMINISTRATIVA

    // URL base da área administrativa
    static function get_SiteADM() {
        return self::get_SiteHOME() . '/' . self::$pasta_ADM;
    }

    // Páginas administrativas para produtos
    static function pag_ProdutosADM() {
        return self::get_SiteADM() . '/adm_produtos';
    }

    static function pag_ProdutosNovoADM() {
        return self::get_SiteADM() . '/adm_produtos_novo';
    }

    static function pag_ProdutosEditarADM() {
        return self::get_SiteADM() . '/adm_produtos_editar';
    }

    static function pag_ProdutosDeletarADM() {
        return self::get_SiteADM() . '/adm_produtos_deletar';
    }

    static function pag_ProdutosImgADM() {
        return self::get_SiteADM() . '/adm_produtos_img';
    }

    // Páginas administrativas para clientes
    static function pag_ClientesADM() {
        return self::get_SiteADM() . '/adm_clientes';
    }

    static function pag_ClientesEditarADM() {
        return self::get_SiteADM() . '/adm_clientes_editar';
    }

    // Páginas administrativas para pedidos e itens
    static function pag_PedidosADM() {
        return self::get_SiteADM() . '/adm_pedidos';
    }

    static function pag_ItensADM() {
        return self::get_SiteADM() . '/adm_itens';
    }

    // Página administrativa para categorias
    static function pag_CategoriasADM() {
        return self::get_SiteADM() . '/adm_categorias';
    }

    // Página para logoff na área administrativa
    static function pag_LogoffADM() {
        return self::get_SiteADM() . '/adm_logoff';
    }

    // Gera link para a imagem redimensionada usando um script (ex: thumb.php)
    static function ImageLink($img, $largura, $altura) {
        $imagem = self::get_ImageURL() . "thumb.php?src={$img}&w={$largura}&h={$altura}&zc=1";
        return $imagem;
    }

    // Retorna o nome da pasta dos controllers
    static function get_Pasta_Controller() {
        return self::$pasta_controller;
    }

    // Método para redirecionar a página após um tempo determinado (em segundos)
    static function Redirecionar($tempo, $pagina) {
        $url = '<meta http-equiv="refresh" content="' . $tempo . '; url=' . $pagina . '">';
        echo $url;
    }

    // Método que verifica a variável $_GET['pag'] e inclui o controller correspondente
    static function get_Pagina() {
        if (isset($_GET['pag'])) {

            $pagina = $_GET['pag'];

            // Divide a URL pelo separador "/" e guarda em array estático
            self::$pag = explode('/', $pagina);

            // Monta o caminho do controller (apenas a primeira parte da URL)
            $pagina = 'controller/' . self::$pag[0] . '.php';

            // Se o arquivo existe, inclui; senão, inclui a página de erro
            if (file_exists($pagina)) {
                include $pagina;
            } else {
                include 'erro.php';
            }

        } else {
            // Caso não exista parâmetro pag, inclui a página home.php
            include 'home.php';
        }
    }
}

?>
