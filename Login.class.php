<?php 

// Classe para gerenciar o login de clientes e administradores, herda a conexão com banco
class Login extends Conexao {

    // Propriedades privadas para armazenar usuário e senha
    private $user, $senha;

    // Construtor chama o da classe pai para garantir conexão com banco
    function __construct() {
        parent::__construct();
    }

    /**
     * Efetua login do cliente verificando email e senha
     * @param string $user Email do cliente
     * @param string $senha Senha do cliente (texto simples)
     */
    function GetLogin($user, $senha) {
        $this->setUser($user);
        $this->setSenha($senha);

        // Consulta SQL para buscar cliente pelo email e senha criptografada
        $query = "SELECT * FROM {$this->prefix}clientes WHERE cli_email = :email AND cli_pass = :senha";

        $params = array(
            ':email' => $this->getUser(),
            ':senha' => $this->getSenha(),
        );

        // Executa a consulta
        $this->ExecuteSQL($query, $params);

        // Se encontrar o cliente
        if ($this->TotalDados() > 0) {
            $lista = $this->ListarDados();

            // Armazena os dados do cliente na sessão
            $_SESSION['CLI']['cli_id']        = $lista['cli_id'];
            $_SESSION['CLI']['cli_nome']      = $lista['cli_nome'];
            $_SESSION['CLI']['cli_sobrenome'] = $lista['cli_sobrenome'];
            $_SESSION['CLI']['cli_endereco']  = $lista['cli_endereco'];
            $_SESSION['CLI']['cli_numero']    = $lista['cli_numero'];
            $_SESSION['CLI']['cli_bairro']    = $lista['cli_bairro'];
            $_SESSION['CLI']['cli_cidade']    = $lista['cli_cidade'];
            $_SESSION['CLI']['cli_uf']        = $lista['cli_uf'];
            $_SESSION['CLI']['cli_cpf']       = $lista['cli_cpf'];
            $_SESSION['CLI']['cli_cep']       = $lista['cli_cep'];
            $_SESSION['CLI']['cli_rg']        = $lista['cli_rg'];
            $_SESSION['CLI']['cli_ddd']       = $lista['cli_ddd'];
            $_SESSION['CLI']['cli_fone']      = $lista['cli_fone'];
            $_SESSION['CLI']['cli_email']     = $lista['cli_email'];
            $_SESSION['CLI']['cli_celular']   = $lista['cli_celular'];
            $_SESSION['CLI']['cli_data_nasc'] = $lista['cli_data_nasc'];
            $_SESSION['CLI']['cli_hora_cad']  = $lista['cli_hora_cad'];
            $_SESSION['CLI']['cli_data_cad']  = $lista['cli_data_cad'];
            $_SESSION['CLI']['cli_pass']      = $lista['cli_pass']; 

            // Redireciona para a página do cliente após login
            Rotas::Redirecionar(0, Rotas::pag_CLienteLogin());

        } else {
            // Se não encontrar, mostra alerta
            echo '<script> alert("Dados Incorretos"); </script>';
        }
    }

    // Método estático para exibir mensagem de acesso negado com link para login
    static function AcessoNegado() {
        echo '<div class="alert alert-danger"><a href="' . Rotas::pag_ClienteLogin() . '" class="btn btn-danger">Login </a> Acesso Negado, faça Login</div>';
    }

    /**
     * Efetua login de administrador verificando email e senha
     * @param string $user Email do admin
     * @param string $senha Senha do admin (texto simples)
     * @return bool true se login OK, false caso contrário
     */
    function GetLoginADM($user, $senha) {
        $this->setUser($user);
        $this->setSenha($senha);

        // Consulta SQL para buscar admin
        $query = "SELECT * FROM {$this->prefix}user WHERE user_email = :email AND user_pw = :senha";

        $params = array(
            ':email' => $this->getUser(),
            ':senha' => $this->getSenha()
        );

        $this->ExecuteSQL($query, $params);

        // Se encontrou o usuário admin
        if ($this->TotalDados() > 0) {
            $lista = $this->ListarDados();

            // Armazena dados do admin na sessão
            $_SESSION['ADM']['user_id']    = $lista['user_id'];
            $_SESSION['ADM']['user_nome']  = $lista['user_nome'];
            $_SESSION['ADM']['user_email'] = $lista['user_email'];
            $_SESSION['ADM']['user_pw']    = $lista['user_pw'];
            $_SESSION['ADM']['user_data']  = Sistema::DataAtualBR();
            $_SESSION['ADM']['user_hora']  = Sistema::HoraAtual();

            return true;

        } else {
            // Login incorreto
            echo '<h4 class="alert alert-danger"> O login incorreto </h4>';
            // Poderia redirecionar para login aqui se desejar
            return false;
        }
    }

    // Verifica se o cliente está logado (sessão setada)
    static function Logado() {
        if (isset($_SESSION['CLI']['cli_email']) && isset($_SESSION['CLI']['cli_id'])) {
            return true;
        } else {
            return false;
        }
    }

    // Verifica se o admin está logado (sessão setada)
    static function LogadoADM() {
        if (isset($_SESSION['ADM']['user_nome']) && isset($_SESSION['ADM']['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

    // Finaliza sessão do cliente e redireciona para página de login
    static function Logoff() {
        unset($_SESSION['CLI']);
        echo '<h4 class="alert alert-success"> Saindo... </h4>';
        Rotas::Redirecionar(2, Rotas::pag_ClienteLogin());
    }

    // Finaliza sessão do admin e redireciona para login.php
    static function LogoffADM() {
        unset($_SESSION['ADM']);
        Rotas::Redirecionar(0, 'login.php');
    }

    // Exibe menu do cliente se estiver logado, caso contrário acesso negado e redireciona
    static function MenuCliente() {
        // Se não está logado, exibe aviso e redireciona
        if (!self::Logado()) {
            self::AcessoNegado();
            Rotas::Redirecionar(2, Rotas::pag_ClienteLogin());
            exit();
        } else {
            // Se está logado, carrega e exibe menu via Smarty
            $smarty = new Template();

            $smarty->assign('PAG_CONTA', Rotas::pag_ClienteConta());
            $smarty->assign('PAG_CARRINHO', Rotas::pag_Carrinho());
            $smarty->assign('PAG_LOGOFF', Rotas::pag_Logoff());
            $smarty->assign('PAG_CLIENTE_PEDIDOS', Rotas::pag_CLientePedidos());
            $smarty->assign('PAG_CLIENTE_DADOS', Rotas::pag_CLienteDados());
            $smarty->assign('PAG_CLIENTE_SENHA', Rotas::pag_CLienteSenha());
            $smarty->assign('USER', $_SESSION['CLI']['cli_nome']);

            $smarty->display('menu_cliente.tpl');
        }
    }

    // Setter para usuário (email)
    private function setUser($user) {
        $this->user = $user;
    }

    // Setter para senha (usa md5 para criptografia simples)
    private function setSenha($senha) {
        // Pode substituir md5 por sistema mais seguro se desejar
        $this->senha = md5($senha);
    }

    // Getter para usuário
    function getUser() {
        return $this->user;
    }

    // Getter para senha criptografada
    function getSenha() {
        return $this->senha;
    }

}
?>
