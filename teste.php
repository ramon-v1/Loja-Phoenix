<?php
// --- CONFIGURAÇÃO DE ERROS (APENAS PARA DESENVOLVIMENTO!) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIM CONFIGURAÇÃO DE ERROS ---

// Inicia ou resume uma sessão PHP. É fundamental para armazenar dados do usuário
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializa variáveis
$sedex_valor = $sedex_prazo = $pac_valor = $pac_prazo = $mensagem_erro = '';
$cep_digitado = ''; // Armazenará o CEP digitado para pré-preencher o campo.

// --- Subtotal do Carrinho (Exemplo) ---
// Substitua isso pelo seu cálculo REAL do subtotal dos produtos no carrinho.
$subtotal_carrinho = isset($_SESSION['subtotal_produtos']) ? $_SESSION['subtotal_produtos'] : 250.00;

// --- Lógica de Processamento do Formulário de CÁLCULO DE FRETE (CEP) ---
if (isset($_POST['cep_submit'])) {
    $cep_digitado = isset($_POST['cep']) ? trim($_POST['cep']) : '';
    $cep_limpo = preg_replace('/[^0-9]/', '', $cep_digitado);

    if (strlen($cep_limpo) === 8) {
        if ($cep_limpo === '02850000') {
            $sedex_valor = 50.00;
            $sedex_prazo = '1 dia útil';
            $pac_valor = 30.00;
            $pac_prazo = '4 dias úteis';
        } else if ($cep_limpo === '06160280'){
            $sedex_valor = 40.00;
            $sedex_prazo = '1 dia útil';
            $pac_valor = 30.00;
            $pac_prazo = '4 dias úteis';
        } else {
            $sedex_valor = 0.00;
            $sedex_prazo = 'A definir';
            $pac_valor = 0.00;
            $pac_prazo = 'A definir';
            $mensagem_erro = "Para o CEP <strong>" . htmlspecialchars($cep_digitado) . "</strong>, os valores de frete são calculados posteriormente.";
        }
        // Armazenar o CEP e os resultados do frete na sessão.
        $_SESSION['cep_digitado'] = $cep_digitado;
        $_SESSION['sedex_valor'] = $sedex_valor;
        $_SESSION['sedex_prazo'] = $sedex_prazo;
        $_SESSION['pac_valor'] = $pac_valor;
        $_SESSION['pac_prazo'] = $pac_prazo;
        $_SESSION['mensagem_erro'] = $mensagem_erro;

        // Limpa a opção de frete escolhida e seu valor ao recalcular o CEP,
        // para que o usuário precise selecionar novamente.
        unset($_SESSION['frete_escolhido_tipo']);
        unset($_SESSION['frete_escolhido_valor']);

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();

    } else {
    $mensagem_erro = "Por favor, digite um CEP válido com 8 dígitos numéricos.";
    $_SESSION['mensagem_erro'] = $mensagem_erro;
    unset($_SESSION['cep_digitado']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
  }
}

// Recupera os valores de frete e CEP da sessão após o redirecionamento
$cep_digitado = isset($_SESSION['cep_digitado']) ? $_SESSION['cep_digitado'] : '';
$sedex_valor = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
$sedex_prazo = isset($_SESSION['sedex_prazo']) ? $_SESSION['sedex_prazo'] : '';
$pac_valor = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;
$pac_prazo = isset($_SESSION['pac_prazo']) ? $_SESSION['pac_prazo'] : '';
$mensagem_erro = isset($_SESSION['mensagem_erro']) ? $_SESSION['mensagem_erro'] : '';
unset($_SESSION['mensagem_erro']);


// --- NOVO: Endpoint AJAX para Atualização Instantânea do Frete e Total ---
// Este bloco será executado SOMENTE se uma requisição AJAX enviar 'action=update_display_total'.
// Ele retorna um JSON com os valores formatados.
if (isset($_POST['action']) && $_POST['action'] === 'update_display_total') {
    $selected_freight_type_ajax = isset($_POST['freight_type']) ? $_POST['freight_type'] : '';

    // Pega os valores de frete que já foram calculados e estão na sessão.
    // É crucial que esses valores já existam na sessão antes de tentar usá-los aqui.
    $current_sedex_val = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
    $current_pac_val = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;

    $frete_para_exibir_ajax = 0.00; // Valor que será exibido no campo "Frete Selecionado"

    if ($selected_freight_type_ajax === 'sedex') {
        $frete_para_exibir_ajax = $current_sedex_val;
        // Não salvaremos 'frete_escolhido_tipo' e 'frete_escolhido_valor' na sessão AQUI.
        // Isso só será feito quando o botão "Confirmar Frete" for clicado,
        // garantindo que a escolha não seja "confirmada" apenas ao clicar no rádio.
    } elseif ($selected_freight_type_ajax === 'sedex_plusplus') {
        $frete_para_exibir_ajax = $current_pac_val;
    }

    $total_final_pedido_ajax = $subtotal_carrinho + $frete_para_exibir_ajax;

    // Define o cabeçalho para indicar que a resposta é JSON
    header('Content-Type: application/json');
    // Retorna os valores formatados como JSON para o JavaScript
    echo json_encode([
        'selected_freight_value' => number_format($frete_para_exibir_ajax, 2, ',', '.'),
        'total_order_value' => number_format($total_final_pedido_ajax, 2, ',', '.')
    ]);
    exit(); // Encerra o script após enviar a resposta JSON
}

$mostrar_resumo_pedido = false;
if (isset($_SESSION['mostrar_resumo_pedido']) && $_SESSION['mostrar_resumo_pedido'] === true) {
    $mostrar_resumo_pedido = true;
    // Opcional: Desabilita novamente para que ele não fique sempre aparecendo
    // unset($_SESSION['mostrar_resumo_pedido']);
}

// --- Lógica de Processamento do Formulário de ESCOLHA DE OPÇÃO DE FRETE (para o botão Confirmar Frete) ---
// Este bloco é executado APENAS quando o botão 'Confirmar Frete' é CLICADO.
if (isset($_POST['opcao_frete_submit']) && isset($_POST['opcao_frete'])) {
    $opcao_frete_escolhida = $_POST['opcao_frete'];

    // Recupera os valores de frete da sessão (já calculados pelo CEP)
    $valor_sedex_sessao = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
    $valor_pac_sessao = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;

    $valor_frete_confirmado = 0.00;
    if ($opcao_frete_escolhida === 'sedex') {
        $valor_frete_confirmado = $valor_sedex_sessao;
    } elseif ($opcao_frete_escolhida === 'sedex_plusplus') {
        $valor_frete_confirmado = $valor_pac_sessao;
    }

    // Salva a opção e o valor do frete escolhidos na sessão (para uso em outras páginas, como checkout)
    $_SESSION['frete_escolhido_valor'] = $valor_frete_confirmado;
    $_SESSION['frete_escolhido_tipo'] = $opcao_frete_escolhida;

    // Limpa o CEP e os valores de frete calculados da sessão ao CONFIRMAR o frete.
    // Isso prepara a página para um novo cálculo de frete, se necessário.
    unset($_SESSION['cep_digitado']);
    unset($_SESSION['sedex_valor']);
    unset($_SESSION['sedex_prazo']);
    unset($_SESSION['pac_valor']);
    unset($_SESSION['pac_prazo']);

    header('Location: ' . $_SERVER['PHP_SELF']); // Redireciona para atualizar a página
    exit();
}

// --- Cálculo do Total Final (Subtotal + Frete para renderização inicial da página) ---
// Este é o cálculo para a primeira carga da página ou após um redirecionamento POST.
// Ele usa o 'frete_escolhido_valor' que foi SALVO na sessão pelo botão 'Confirmar Frete'.
$frete_para_calculo = isset($_SESSION['frete_escolhido_valor']) ? $_SESSION['frete_escolhido_valor'] : 0.00;
$total_final_pedido = $subtotal_carrinho + $frete_para_calculo;

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cálculo de Frete</title>
  <script src="https://cdn.tailwindcss.com"></script>
 <style>
  body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    background-color: #000000;
    color: #FFD700;
  }

  /* Estilos para o radio button selecionado */
  input[type="radio"].peer:checked + div {
    border-color: #2563EB; /* blue-600 */
    background-color: #1f2029; /* blue-50 */
  }

  /* Botões e seus textos continuam com estilos normais */
  button,
  .btn,
  a.bg-blue-700,
  a.bg-blue-600 {
    color: white !important;
  }
</style>

</head>
<body class="bg-[#000000] text-[#FFD700] flex items-center justify-center min-h-screen" >
  <div class="p-4 md:p-6 bg-gray-900 border border-yellow-800 rounded-lg max-w-md w-full mx-4">
    <h1 class="text-3xl font-bold text-[#B8860B] text-center mb-8" >Calcular Frete</h1>

<form method="post" action="teste2.php" class="space-y-6">
      <label for="cep" class="block text-lg font-semibold text-[#B8860B]">CEP:</label>
      <input
        type="text"
        id="cep"
        name="cep"
        placeholder="Digite o CEP (somente números)"
        required
        class="w-full p-3 border border-gray-300 rounded-md text-[#000] focus:ring-blue-500 focus:border-blue-500"
      >

      <button
            type="submit"
            name="cep_submit"
           class="w-full bg-yellow-600 hover:bg-yellow-700 text-black font-semibold py-3 px-4 rounded-md text-base md:text-lg transition duration-150 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-black-500 focus:ring-opacity-50">
                Calcular Frete
        </button>

      <div class="text-center mt-4">
        <a href="carrinho" class="text-black-600 hover:text-yellow-800 hover:underline text-sm md:text-base">
          &larr; Voltar para o carrinho
        </a>
      </div>
    </form>

   