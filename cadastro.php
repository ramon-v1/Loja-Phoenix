<?php

// Instancia o template do Smarty
$smarty = new Template();

// Exibe o menu do cliente logado
Login::MenuCliente();

// Passa os dados da sessão para o template
foreach ($_SESSION['CLI'] as $campo => $valor) {
    $smarty->assign(strtoupper($campo), $valor); // Ex.: cli_nome → CLI_NOME
}

// Verifica se foi enviado um POST com os campos obrigatórios
if (isset($_POST['cli_nome']) && isset($_POST['cli_email']) && isset($_POST['cli_cpf'])) {
    
    // Captura os dados do formulário
    $cli_nome      = $_POST['cli_nome'];
    $cli_sobrenome = $_POST['cli_sobrenome'];
    $cli_data_nasc = $_POST['cli_data_nasc'];
    $cli_rg        = $_POST['cli_rg'];
    $cli_cpf       = $_POST['cli_cpf'];
    $cli_ddd       = $_POST['cli_ddd'];
    $cli_fone      = $_POST['cli_fone'];
    $cli_celular   = $_POST['cli_celular'];
    $cli_endereco  = $_POST['cli_endereco'];
    $cli_numero    = $_POST['cli_numero'];
    $cli_bairro    = $_POST['cli_bairro'];
    $cli_cidade    = $_POST['cli_cidade'];
    $cli_uf        = $_POST['cli_uf'];
    $cli_cep       = $_POST['cli_cep'];
    $cli_email     = $_POST['cli_email'];
    $cli_senha     = $_POST['cli_senha'];

    // Dados imutáveis da sessão
    $cli_data_cad = $_SESSION['CLI']['cli_data_cad'];
    $cli_hora_cad = $_SESSION['CLI']['cli_hora_cad'];

    // Verificação da senha para confirmar alteração
    if ($_SESSION['CLI']['cli_pass'] != md5($cli_senha)) {
        echo '<div class="alert alert-danger">';
        echo '<p>A senha informada está incorreta. Por favor, tente novamente.</p>';
        Sistema::VoltarPagina();
        echo '</div>';
        exit();
    }

    // Instancia a classe de clientes e prepara os dados
    $clientes = new Clientes();

    $clientes->Preparar(
        $cli_nome, $cli_sobrenome, $cli_data_nasc, $cli_rg, $cli_cpf,
        $cli_ddd, $cli_fone, $cli_celular, $cli_endereco, $cli_numero,
        $cli_bairro, $cli_cidade, $cli_uf, $cli_cep, $cli_email,
        $cli_data_cad, $cli_hora_cad, $cli_senha
    );

    // Realiza a atualização dos dados
    if ($clientes->Editar($_SESSION['CLI']['cli_id'])) {
        
        echo '<script>alert("Dados alterados com sucesso! Atualizando os dados do login...");</script>';
        echo '<div class="alert alert-success">Dados atualizados com sucesso!</div>';

        // Atualiza os dados da sessão de login
        $login = new Login();
        $login->GetLogin($cli_email, $cli_senha);
    } else {
        echo '<div class="alert alert-danger">Ocorreu um erro ao tentar editar seus dados. Tente novamente.</div>';
        exit();
    }

} else {
    // Não houve POST, então exibe os dados atuais no template
    $smarty->display('cliente_dados.tpl');
}

?>
