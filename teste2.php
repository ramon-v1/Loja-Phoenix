<?php
// --- CONFIGURAÇÃO DE ERROS (APENAS PARA DESENVOLVIMENTO!) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- FIM CONFIGURAÇÃO DE ERROS ---

// Inicia ou resume uma sessão PHP. É fundamental para armazenar dados do usuário
// O 'session_status()' verifica se uma sessão já foi iniciada para evitar erros.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializa variáveis que serão usadas para exibir informações de frete e CEP.
// Isso evita avisos de variáveis indefinidas no primeiro carregamento da página.
$sedex_valor = $sedex_prazo = $pac_valor = $pac_prazo = $mensagem_erro = '';
$cep_digitado = ''; // Armazenará o CEP digitado para pré-preencher o campo no formulário.

// --- Subtotal do Carrinho (Exemplo e Lógica de Fallback) ---
// Este bloco tenta obter o subtotal do carrinho.
// Em um sistema real, a classe 'Carrinho' seria definida e populada anteriormente.
if (class_exists('Carrinho')) {
    // Se a classe Carrinho existir, instancia e obtém o total.
    $carrinho = new Carrinho();
    $subtotal_carrinho = $carrinho->GetTotal();
} else {
    // Se a classe Carrinho não existir (para fins de teste ou se o arquivo não foi incluído),
    // ele tenta incluí-la e, se ainda assim não conseguir, usa um valor padrão.
    // Lembre-se de ajustar o caminho para 'Carrinho.class.php' conforme a sua estrutura de pastas.
    require_once './model/Carrinho.class.php';
    $carrinho = new Carrinho();
    // Usa o operador de coalescência nula (??) para garantir que $valor_total seja '0' se GetTotal() retornar null.
    $valor_total = $carrinho->GetTotal() ?? '0';
    // Converte o valor para float, substituindo vírgula por ponto se necessário.
    $subtotal_carrinho = (float) str_replace(',', '.', $valor_total);
}

// --- Lógica de Processamento do Formulário de CÁLCULO DE FRETE (CEP) ---
// Este bloco é executado quando o usuário envia o formulário do CEP.
if (isset($_POST['cep_submit'])) {
    // Pega o CEP digitado, remove espaços em branco e caracteres não numéricos.
    $cep_digitado = isset($_POST['cep']) ? trim($_POST['cep']) : '';
    $cep_limpo = preg_replace('/[^0-9]/', '', $cep_digitado);

    // Valida se o CEP limpo tem exatamente 8 dígitos.
    if (strlen($cep_limpo) === 8) {
        // Lógica de simulação de frete baseada em CEPs específicos.
        // Em um cenário de produção, esta parte faria uma chamada a uma API externa (Correios, transportadora).
        if ($cep_limpo === '04000000') {
            $sedex_valor = 50.00;
            $sedex_prazo = '1 dia útil';
            $pac_valor = 30.00;
            $pac_prazo = '7 dias úteis';
        } else if ($cep_limpo === '06160280'){
            $sedex_valor = 40.00;
            $sedex_prazo = '1 dia útil';
            $pac_valor = 30.00;
            $pac_prazo = '7 dias úteis';
        } else {
            // Valores genéricos para outros CEPs não listados, simulando um cálculo.
            $sedex_valor = 25.00;
            $sedex_prazo = '3 dias úteis';
            $pac_valor = 15.00;
            $pac_prazo = '4 dias úteis';
            // $mensagem_erro pode ser usada aqui para informar que é um cálculo simulado ou para CEPs não específicos.
        }
        // Armazena os resultados do cálculo do frete e o CEP digitado na sessão.
        // Isso permite que os dados persistam após um redirecionamento ou recarregamento da página.
        $_SESSION['cep_digitado'] = $cep_digitado;
        $_SESSION['sedex_valor'] = $sedex_valor;
        $_SESSION['sedex_prazo'] = $sedex_prazo;
        $_SESSION['pac_valor'] = $pac_valor;
        $_SESSION['pac_prazo'] = $pac_prazo;
        $_SESSION['mensagem_erro'] = $mensagem_erro; // Garante que a mensagem de erro seja limpa se o cálculo foi bem-sucedido.

        // Limpa as opções de frete escolhidas anteriormente na sessão.
        // Isso força o usuário a selecionar uma nova opção de frete após um novo cálculo de CEP.
        unset($_SESSION['frete_escolhido_tipo']);
        unset($_SESSION['frete_escolhido_valor']);

        // Redireciona para a própria página via POST-redirect-GET (PRG) pattern.
        // Isso evita o reenvio do formulário se o usuário atualizar a página.
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit(); // Encerra o script após o redirecionamento.

    } else {
        // Se o CEP não for válido, define uma mensagem de erro.
        $mensagem_erro = "Por favor, digite um CEP válido com 8 dígitos numéricos.";
        $_SESSION['mensagem_erro'] = $mensagem_erro; // Armazena o erro na sessão.
        unset($_SESSION['cep_digitado']); // Limpa o CEP inválido da sessão.
        header('Location: ' . $_SERVER['PHP_SELF']); // Redireciona para exibir a mensagem.
        exit();
    }
}

// Recupera os valores de frete e CEP da sessão.
// Isso garante que, mesmo após um redirecionamento, os dados calculados estejam disponíveis.
$cep_digitado = isset($_SESSION['cep_digitado']) ? $_SESSION['cep_digitado'] : '';
$sedex_valor = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
$sedex_prazo = isset($_SESSION['sedex_prazo']) ? $_SESSION['sedex_prazo'] : '';
$pac_valor = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;
$pac_prazo = isset($_SESSION['pac_prazo']) ? $_SESSION['pac_prazo'] : '';
// Renomeia a variável para evitar conflito e limpa a mensagem de erro da sessão após usá-la.
$mensagem_erro_sessao = isset($_SESSION['mensagem_erro']) ? $_SESSION['mensagem_erro'] : '';
unset($_SESSION['mensagem_erro']); // Limpa a mensagem da sessão para que não apareça em recarregamentos futuros.


// --- Endpoint AJAX para Atualização Instantânea do Frete e Total ---
// Este bloco é executado SOMENTE se uma requisição AJAX for feita com 'action=update_display_total'.
// Ele não renderiza HTML, apenas retorna um JSON com os valores formatados para o JavaScript.
if (isset($_POST['action']) && $_POST['action'] === 'update_display_total') {
    $selected_freight_type_ajax = isset($_POST['freight_type']) ? $_POST['freight_type'] : '';

    // Pega os valores de frete que já foram calculados e estão na sessão.
    $current_sedex_val = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
    $current_pac_val = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;

    $frete_para_exibir_ajax = 0.00;

    // Define o valor do frete a ser exibido com base na opção selecionada via AJAX.
    if ($selected_freight_type_ajax === 'sedex') {
        $frete_para_exibir_ajax = $current_sedex_val;
    } elseif ($selected_freight_type_ajax === 'sedex_plusplus') { // 'sedex_plusplus' é usado no HTML para o PAC.
        $frete_para_exibir_ajax = $current_pac_val;
    }

    // Recalcula o subtotal do carrinho. Isso é importante para garantir que o total esteja sempre atualizado.
    if (class_exists('Carrinho')) {
        $carrinho_ajax = new Carrinho();
        $subtotal_carrinho_ajax = $carrinho_ajax->GetTotal();
    } else {
        // Fallback para o subtotal do carrinho caso a classe não esteja disponível (para testes).
        $subtotal_carrinho_ajax = isset($_SESSION['subtotal_carrinho_debug']) ? $_SESSION['subtotal_carrinho_debug'] : 100.00;
    }

    // Calcula o total final do pedido (subtotal do carrinho + frete selecionado).
    $total_final_pedido_ajax = $subtotal_carrinho_ajax + $frete_para_exibir_ajax;

    // Define o cabeçalho para indicar que a resposta é um JSON.
    header('Content-Type: application/json');
    // Codifica os valores para JSON e os imprime.
    // 'number_format' é usado para formatar os valores monetários para o padrão brasileiro (vírgula decimal).
    echo json_encode([
        'selected_freight_value' => number_format($frete_para_exibir_ajax, 2, ',', '.'),
        'total_order_value' => number_format($total_final_pedido_ajax, 2, ',', '.')
    ]);
    exit(); // Encerra o script, pois a resposta AJAX já foi enviada.
}


// --- Lógica de Processamento do Formulário de ESCOLHA DE OPÇÃO DE FRETE (para o botão Confirmar Frete) ---
// Este bloco é executado quando o usuário clica no botão "Continuar" após selecionar uma opção de frete.
if (isset($_POST['opcao_frete_submit']) && isset($_POST['opcao_frete'])) {
    $opcao_frete_escolhida = $_POST['opcao_frete']; // Pega a opção de frete selecionada (ex: 'sedex' ou 'sedex_plusplus').

    // Recupera os valores de frete calculados anteriormente da sessão.
    $valor_sedex_sessao = isset($_SESSION['sedex_valor']) ? $_SESSION['sedex_valor'] : 0.00;
    $valor_pac_sessao = isset($_SESSION['pac_valor']) ? $_SESSION['pac_valor'] : 0.00;

    $valor_frete_confirmado = 0.00;
    // Atribui o valor correto do frete com base na opção escolhida.
    if ($opcao_frete_escolhida === 'sedex') {
        $valor_frete_confirmado = $valor_sedex_sessao;
    } elseif ($opcao_frete_escolhida === 'sedex_plusplus') {
        $valor_frete_confirmado = $valor_pac_sessao;
    }

    // Armazena a opção de frete e seu valor na sessão.
    // Isso é crucial para que a próxima página (ex: confirmação do pedido) saiba qual frete foi escolhido.
    $_SESSION['frete_escolhido_valor'] = $valor_frete_confirmado;
    $_SESSION['frete_escolhido_tipo'] = $opcao_frete_escolhida;

    // Redireciona o usuário para a página de confirmação do pedido.
    // Ajuste 'pedido_confirmar' para o nome real da sua próxima página.
    header('Location: pedido_confirmar');
    exit(); // Encerra o script.
}

// --- Cálculo do Total Final (Subtotal + Frete para renderização inicial da página) ---
// Este cálculo é para a exibição inicial da página.
// Usa o valor do frete que foi *confirmado* e salvo na sessão, ou 0.00 se nenhum foi escolhido ainda.
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: #000000; /* Fundo da página preto */
            color: #DAA520; /* Texto em dourado */
        }
        /* Estilos para o radio button selecionado */
        input[type="radio"].peer:checked + div {
            border-color: #DAA520; /* Borda dourada quando selecionado */
            background-color: #333333; /* Fundo da opção selecionada um preto mais claro */
            box-shadow: 0 0 0 2px #DAA520; /* Sombra dourada */
        }
        .shipping-option-label:hover > div {
            border-color: #B8860B; /* Borda dourada mais escura no hover */
            background-color: #1a1a1a; /* Fundo um pouco mais claro no hover */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <?php if (!empty($mensagem_erro_sessao)): ?>
        <div role="alert" class="mt-6 p-4 bg-red-800 border border-red-600 text-red-200 rounded-md text-sm md:text-base">
            <?php echo $mensagem_erro_sessao; // A mensagem já pode conter HTML (como <strong>), por isso não usamos htmlspecialchars aqui. ?>
        </div>
    <?php endif; ?>

    <?php if (($sedex_valor > 0 || $pac_valor > 0) && empty($mensagem_erro_sessao)): ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-8">
            <div class="p-4 md:p-6 bg-gray-900 border border-yellow-800 rounded-lg">
                <h2 class="text-xl md:text-2xl font-semibold text-yellow-500 mb-4">
                    Opções para o CEP: <span class="font-bold text-yellow-300"><?php echo htmlspecialchars($cep_digitado); ?></span>
                </h2>

                <div class="space-y-4">
                    <label class="flex-1 cursor-pointer shipping-option-label block">
                        <input type="radio" name="opcao_frete" value="sedex"
                               class="peer hidden freight-radio" <?php echo (isset($_SESSION['frete_escolhido_tipo']) && $_SESSION['frete_escolhido_tipo'] == 'sedex') ? 'checked' : ''; ?> required>
                        <div class="p-4 border-2 border-gray-700 rounded-lg peer-checked:border-yellow-600 peer-checked:bg-gray-700 transition-all duration-150 ease-in-out">
                            <div class="flex justify-between items-center">
                                <p class="text-base md:text-lg font-semibold text-yellow-500">SEDEX</p>
                                <p class="text-sm text-yellow-600"><?php echo htmlspecialchars($sedex_prazo); ?></p>
                            </div>
                            <p class="text-yellow-400 text-base md:text-lg mt-1">
                                R$ <?php echo htmlspecialchars(number_format($sedex_valor, 2, ',', '.')); ?>
                            </p>
                        </div>
                    </label>

                    <label class="flex-1 cursor-pointer shipping-option-label block">
                        <input type="radio" name="opcao_frete" value="sedex_plusplus"
                               class="peer hidden freight-radio" <?php echo (isset($_SESSION['frete_escolhido_tipo']) && $_SESSION['frete_escolhido_tipo'] == 'sedex_plusplus') ? 'checked' : ''; ?>>
                        <div class="p-4 border-2 border-gray-700 rounded-lg peer-checked:border-yellow-600 peer-checked:bg-gray-700 transition-all duration-150 ease-in-out">
                            <div class="flex justify-between items-center">
                                <p class="text-base md:text-lg font-semibold text-yellow-500">SEDEX ++</p> <p class="text-sm text-yellow-600"><?php echo htmlspecialchars($pac_prazo); ?></p>
                            </div>
                            <p class="text-yellow-400 text-base md:text-lg mt-1">
                                R$ <?php echo htmlspecialchars(number_format($pac_valor, 2, ',', '.')); ?>
                            </p>
                        </div>
                    </label>
                </div>

                <div class="mt-6">
                    <p class="flex justify-between mb-2 text-yellow-300">
                        <span><strong>Frete Selecionado:</strong></span>
                        <span id="frete_selecionado_valor" class="font-medium">
                            R$ <?php
                                // Garante que $frete_para_calculo esteja definido
                                $frete_para_calculo = $frete_para_calculo ?? 0.00;
                                echo htmlspecialchars(number_format($frete_para_calculo, 2, ',', '.'));
                            ?>
                        </span>
    
                    <hr class="mb-4 border-yellow-700">

                    <form action="" method="post">
                        <button type="submit" name="opcao_frete_submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-black font-semibold py-3 px-4 rounded-md text-base md:text-lg transition duration-150 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                            Continuar
                        </button>
                    </form>
                </div>
            </div>
        </form>
    <?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingOptionLabels = document.querySelectorAll('.shipping-option-label');
        const selectedFreightSpan = document.getElementById('frete_selecionado_valor');
        const totalOrderSpan = document.getElementById('total_pedido_valor');
        const cepInput = document.getElementById('cep'); // Este input CEP não está no HTML atual, mas é bom mantê-lo.
        const freightRadioButtons = document.querySelectorAll('.freight-radio'); // Pega todos os radio buttons de frete.
        const subtotalProdutosSpan = document.getElementById('subtotal_produtos_valor');


        // Função auxiliar para atualizar a exibição do frete e total via AJAX
        function updateShippingDisplay(freightType) {
            const formData = new FormData();
            formData.append('action', 'update_display_total');
            formData.append('freight_type', freightType);

            fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Lança um erro se a resposta da rede não for bem-sucedida.
                    return response.text().then(text => {
                        throw new Error('Network response was not ok: ' + response.statusText + ". Resposta do servidor: " + text);
                    });
                }
                return response.json(); // Analisa a resposta JSON.
            })
            .then(data => {
                // Atualiza os elementos HTML com os dados recebidos.
                if (selectedFreightSpan) {
                    selectedFreightSpan.textContent = 'R$ ' + data.selected_freight_value; // Garante o prefixo 'R$'
                }
                if (totalOrderSpan) {
                    totalOrderSpan.textContent = 'R$ ' + data.total_order_value; // Garante o prefixo 'R$'
                }
            })
            .catch(error => {
                // Lida com erros na requisição AJAX.
                console.error('Erro ao atualizar frete:', error);
                if (selectedFreightSpan) {
                    selectedFreightSpan.textContent = 'R$ --,--'; // Placeholder de erro
                }
                if (totalOrderSpan) {
                    totalOrderSpan.textContent = 'R$ --,--'; // Placeholder de erro
                }
            });
        }

        // Formatação do CEP enquanto o usuário digita (se o campo CEP existir no HTML)
        if (cepInput) {
            cepInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
                if (value.length > 8) {
                    value = value.substring(0, 8); // Limita a 8 dígitos
                }
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5); // Adiciona o hífen
                }
                e.target.value = value;
            });
        }

        // Adiciona um listener para cada LABEL de opção de frete (HOVER)
        // Isso permite que o total seja atualizado apenas no hover se nenhuma opção estiver selecionada.
        shippingOptionLabels.forEach(label => {
            label.addEventListener('mouseenter', function() {
                // Verifica se algum radio button já está checado.
                const isAnyRadioChecked = document.querySelector('.freight-radio:checked');
                if (isAnyRadioChecked) {
                    return; // Uma opção já está selecionada, não atualiza no hover.
                }

                const radioInput = this.querySelector('.freight-radio');
                if (!radioInput) {
                    console.error('Input radio não encontrado dentro da label:', this);
                    return;
                }
                // Atualiza a exibição com base na opção sob o cursor.
                updateShippingDisplay(radioInput.value);
            });
        });

        // Adiciona um listener para cada RADIO BUTTON de opção de frete (CLICK/CHANGE)
        // Isso garante que o total seja atualizado quando uma opção é realmente selecionada.
        freightRadioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    updateShippingDisplay(this.value); // Atualiza a exibição com a opção selecionada.
                }
            });
        });

        // No carregamento da página, se um radio button já estiver checado (ex: da sessão),
        // atualiza a exibição para corresponder a ele.
        const initiallyCheckedRadio = document.querySelector('.freight-radio:checked');
        if (initiallyCheckedRadio) {
            updateShippingDisplay(initiallyCheckedRadio.value);
        } else {
            // Se nenhuma opção de frete estiver pré-selecionada e houver valores de frete disponíveis,
            // a exibição inicial será baseada no $frete_para_calculo do PHP (que será 0.00).
            // Com as novas cores, o texto "Frete Selecionado: R$ 0,00" será dourado.
            // O hover agora ativará a atualização se nada estiver selecionado.
        }
    });
</script>

</body>
</html>