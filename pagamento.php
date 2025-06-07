<?php
 
session_start();

// Inclua os arquivos necessários, caso ainda não estejam
require_once 'lib/autoload.php'; // ajuste o caminho se necessário

if (isset($_SESSION['PEDIDO_COD'])) {
    $pedido_cod = $_SESSION['PEDIDO_COD'];

    $pedidos = new Pedidos();

    $dados = array(
        "ped_pag_status" => 'SIM'
    );

    // Aqui está a linha que faltava
    $pedidos->Atualizar($pedido_cod, $dados);

    // unset($_SESSION['PEDIDO_COD']); // Opcional, se quiser limpar após o pagamento
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        /* style.css */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #000000; /* Preto puro */
            position: relative;
        }

        /* Remover estilos não utilizados do carrinho */
        /* .container, h1, #itens-carrinho p, .acoes-carrinho button { display: none; } */

        /* Estilos para o Overlay e Modal */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); /* Preto semitransparente */
            display: none;
            z-index: 999;
        }

        #modal-pagamento {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #1a1a1a; /* Preto muito escuro */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: none; /* Inicia oculto e será exibido pelo JS */
            width: 90%;
            max-width: 400px;
            animation: fadeIn 0.3s ease-out;
            border: 1px solid #FFD700; /* Dourado principal */
        }

        #modal-pagamento h2 {
            color: #FFD700; /* Dourado principal */
            margin-bottom: 20px;
            text-align: center;
        }

        #modal-pagamento form label {
            display: block;
            margin-bottom: 8px;
            color: #FFD700; /* Dourado principal */
            text-align: left;
        }

        #modal-pagamento form input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-bottom: 1px solid #FFD700; /* Dourado principal */
            border-radius: 10px;
            font-size: 1em;
            color: #000000; /* Preto puro para o texto do input */
            background-color: #fff; /* Fundo branco para input */
            outline: none;
        }

        #modal-pagamento form input[type="text"]:focus {
            border-bottom: 2px solid #FFD700; /* Dourado principal */
        }

        #modal-pagamento form #btn-confirmar-compra,
        #modal-pagamento form #btn-fechar-modal {
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 10px;
            transition: background-color 0.3s ease;
            flex-grow: 1;
            width: calc(50% - 5px);
        }

        #modal-pagamento form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        #modal-pagamento form label,
        #modal-pagamento form input {
            width: 100%;
        }
          #btn-confirmar-compra2{
            background-color: #FFD700; /* Dourado principal */
            color: #000000; /* Preto puro */
          }

        #modal-pagamento form #btn-confirmar-compra {
            background-color: #FFD700; /* Dourado principal */
            color: #000000; /* Preto puro */
        }
        #btn-confirmar-compra2:hover {
            background-color: #000000; /* Preto puro */
            color: #FFD700 !important; /* Dourado principal */
        }


        #modal-pagamento form #btn-confirmar-compra:hover {
            background-color: #000000; /* Preto puro */
            color: #FFD700 !important; /* Dourado principal */
        }

        #btn-fechar-modal {
            background-color: #FFD700 !important; /* Dourado principal */
            color: #000000; /* Preto puro */
            margin-left: 10px;
        }

        #btn-fechar-modal:hover {
            background-color: #000000 !important; /* Preto puro */
            color: #FFD700 !important; /* Dourado principal */
        }

        /* ESTILOS PARA A ANIMAÇÃO DE VERIFICAÇÃO */
        #animacao-verificacao {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #1a1a1a; /* Preto muito escuro */
            color: #FFD700; /* Dourado principal */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.5s ease-out;
            border: 1px solid #FFD700; /* Dourado principal */
        }

        #animacao-verificacao.show {
            opacity: 1;
        }

        #animacao-verificacao a { /* Estilo para o link "Voltar ao Início" */
            color: #FFD700; /* Dourado principal */
            text-decoration: none;
            font-size: 0.8em;
            margin-top: 20px;
            border: 1px solid #FFD700;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        #animacao-verificacao a:hover {
            background-color: #FFD700; /* Dourado principal */
            color: #000000; /* Preto puro */
        }


        .checkmark {
            width: 100px;
            height: 100px;
            display: block;
            stroke-width: 2;
            stroke: #FFD700; /* Dourado principal */
            stroke-miterlimit: 10;
            margin: 0 auto 15px;
            animation: scale-inter .3s ease-in-out .9s both;
            animation-play-state: paused;
        }

        .checkmark__circle {
            stroke-dasharray: 62.83;
            stroke-dashoffset: 62.83;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #FFD700; /* Dourado principal */
            fill: none;
            animation: stroke-inter .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
            animation-play-state: paused;
        }

        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 18;
            stroke-dashoffset: 18;
            stroke: #FFD700; /* Dourado principal */
            animation: stroke-inter .3s cubic-bezier(0.65, 0, 0.45, 1) .8s forwards;
            animation-play-state: paused;
            transform: translate(calc(15px - 50%), calc(15px - 50%));
        }

        .checkmark.play-animation .checkmark__circle,
        .checkmark.play-animation .checkmark__check {
            animation-play-state: running;
        }

        /* Animações Keyframes para o efeito Inter */
        @keyframes stroke-inter {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scale-inter {
            0%, 100% {
                transform: none;
            }
            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }

        @keyframes fill-inter {
            100% {
                box-shadow: inset 0px 0px 0px 12px #FFD700; /* Dourado principal */
            }
        }

        /* Estilos genéricos para esconder/mostrar */
        .hidden {
            display: none !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div id="modal-pagamento">
        <h2>INFORMAÇÕES DE PAGAMENTO</h2>
        <form id="form-pagamento">
            <label for="numero-cartao">Número do Cartão:</label>
            <input type="text" id="numero-cartao" name="numero-cartao" placeholder="XXXX XXXX XXXX XXXX" required>

            <label for="validade-cartao">Validade (MM/AA):</label>
            <input type="text" id="validade-cartao" name="validade-cartao" placeholder="MM/AA" required>

            <label for="cvv-cartao">Código de Segurança:</label>
            <input type="text" id="cvv-cartao" name="cvv-cartao" placeholder="XXX" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required>

            <button type="submit" id="btn-confirmar-compra">Confirmar Compra</button>
            <button type="button" id="btn-fechar-modal">Cancelar</button>
        </form>
    </div>

    <div id="animacao-verificacao" class="hidden">
        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
            <circle class="checkmark__circle" cx="15" cy="15" r="10" fill="none"/>
            <path class="checkmark__check" fill="none" d="M8 15 l5 5 l9 -9"/>
        </svg>
        <p>Pedido Finalizado!</p>
        <p>Processando...</p>
         <form action="clientes_pedidos" method="POST">
        <button id="btn-confirmar-compra2">Pedidos</button>


        </form>
    </div>

    <script>
        /* script.js */
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('overlay');
            const modalPagamento = document.getElementById('modal-pagamento');
            const btnFecharModal = document.getElementById('btn-fechar-modal');
            const formPagamento = document.getElementById('form-pagamento');
            const animacaoVerificacao = document.getElementById('animacao-verificacao');
            const checkmarkSvg = animacaoVerificacao.querySelector('.checkmark');

            // Referências aos campos de input do modal
            const numeroCartaoInput = document.getElementById('numero-cartao');
            const validadeCartaoInput = document.getElementById('validade-cartao');
            const cvvCartaoInput = document.getElementById('cvv-cartao');
            const cpfInput = document.getElementById('cpf');

            // Função para abrir o modal de pagamento
            const openPaymentModal = () => {
                overlay.style.display = 'block';
                modalPagamento.style.display = 'block';
                numeroCartaoInput.focus(); // Foca no primeiro campo ao abrir o modal
            };

            // Abre a mini tela de pagamento automaticamente ao carregar a página
            openPaymentModal();

            // Fecha a mini tela de pagamento
            btnFecharModal.addEventListener('click', () => {
                // Você pode decidir o que acontece ao "Cancelar"
                // Por exemplo, fechar tudo ou redirecionar para outra página.
                // Por enquanto, vamos fechar o modal e overlay.
                overlay.style.display = 'none';
                modalPagamento.style.display = 'none';
                formPagamento.reset(); // Limpa os campos do formulário
                checkmarkSvg.classList.remove('play-animation'); // Reseta a animação
                // Se a intenção é que o usuário não volte para a página, você pode redirecionar aqui.
                // window.location.href = 'pagina-inicial.html';
            });

            overlay.addEventListener('click', () => {
                btnFecharModal.click(); // Fecha o modal clicando no overlay
            });

            // --- Lógica de Submissão do Formulário de Pagamento ---
            formPagamento.addEventListener('submit', async (event) => {
                event.preventDefault(); // Impede o envio padrão do formulário

                const cpf = cpfInput.value.trim();
                const numeroCartao = numeroCartaoInput.value.trim().replace(/\s/g, '');
                const validadeCartao = validadeCartaoInput.value.trim();
                const cvvCartao = cvvCartaoInput.value.trim();

                // --- VALORES DE TESTE PARA QUE O BOTÃO "CONFIRMAR COMPRA" FUNCIONE ---
                // VOCÊ DEVE USAR ESTES VALORES EXATAMENTE AO TESTAR!
                const CARTAO_VALIDO = '1234567890123456';
                const VALIDADE_VALIDA = '12/25'; // MM/AA
                const CVV_VALIDO = '123';

                let isFormValid = true; // Flag para controlar a validade geral do formulário

                // 1. Validar CPF
                if (!cpf || cpf.length !== 11 || !/^\d+$/.test(cpf)) {
                    alert('Por favor, digite um CPF válido (somente números, 11 dígitos).');
                    isFormValid = false;
                }
                // 2. Validar Número do Cartão
                else if (numeroCartao !== CARTAO_VALIDO) {
                    alert(`Número do cartão inválido. Use o número de teste: ${CARTAO_VALIDO}`);
                    isFormValid = false;
                }
                // 3. Validar Validade do Cartão
                else if (validadeCartao !== VALIDADE_VALIDA) {
                    alert(`Validade do cartão inválida. Use a validade de teste: ${VALIDADE_VALIDA}`);
                    isFormValid = false;
                }
                // 4. Validar CVV
                else if (!cvvCartao || (cvvCartao.length !== 3 && cvvCartao.length !== 4) || !/^\d+$/.test(cvvCartao)) {
                    alert('Por favor, digite um CVV válido (3 ou 4 dígitos numéricos).');
                    isFormValid = false;
                }
                // 5. Validar CVV (com o valor de teste)
                else if (cvvCartao !== CVV_VALIDO) {
                    alert(`CVV inválido. Use o CVV de teste: ${CVV_VALIDO}`);
                    isFormValid = false;
                }

                // Se o formulário NÃO for válido, exibe o alerta e PARA a execução da função.
                if (!isFormValid) {
                    console.log('Validação do formulário falhou. Verifique os dados inseridos.');
                    return;
                }

                // --- Se todas as validações passaram, o código continua a partir daqui ---

                // Esconde o modal de pagamento e o overlay
                modalPagamento.style.display = 'none';
                overlay.style.display = 'none';

                // Mostra e inicia a animação de verificação
                animacaoVerificacao.classList.remove('hidden');
                setTimeout(() => {
                    animacaoVerificacao.classList.add('show');
                    checkmarkSvg.classList.add('play-animation'); // Inicia a animação do SVG
                }, 50); // Pequeno atraso para garantir que a classe 'hidden' seja removida antes de 'show'

                // REMOVIDO: O setTimeout que escondia a animação após um tempo.
                // A div permanecerá visível.
            });
        });
    </script>
</body>
</html>