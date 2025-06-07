
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> 
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
    </div>

    <script src="script.js"></script>
</body>
</html>
<style>
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    background-color: #f4f4f4;
    position: relative;
}

.container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 90%;
    max-width: 600px;
}


/* Estilos para o Overlay e Modal */
#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: none;
    z-index: 999;
}

#modal-pagamento {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #1a1a1a; /* Preto claro */
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    display: none;
    width: 90%;
    max-width: 400px;
    animation: fadeIn 0.3s ease-out;
    border: 1px solid #FFD700; /* Borda dourada mais forte */
}

#modal-pagamento h2 {
    color: #FFD700; /* Título do modal dourado mais forte */
    margin-bottom: 20px;
    text-align: center;
}

#modal-pagamento form label {
    display: block;
    margin-bottom: 8px;
    color: #FFD700; /* Títulos dos campos dourados mais forte */
    text-align: left;
}

#modal-pagamento form input[type="text"] {
    width: calc(100% - 20px);
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-bottom: 1px solid #FFD700; /* Borda inferior dourada mais forte */
    border-radius: 10px;
    font-size: 1em;
    color: #000;
    background-color: #fff;
    outline: none;
}

#modal-pagamento form input[type="text"]:focus {
    border-bottom: 2px solid #FFD700; /* Um pouco mais espessa ao focar e mais forte */
}

#modal-pagamento form #btn-confirmar-compra,
#modal-pagamento form #btn-fechar-modal {
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    margin-top: 10px;
    /* Adicionando transição para a cor de fundo */
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

#modal-pagamento form #btn-confirmar-compra {
    background-color: #FFD700;
    color: #000;
}

#modal-pagamento form #btn-confirmar-compra:hover {
    background-color: #000;
    color: #FFD700 !important;
}

#btn-fechar-modal {
    background-color: #FFD700 !important;
    color: #000;
    margin-left: 10px;
}

#btn-fechar-modal:hover {
    background-color: #000 !important;
    color: #FFD700 !important;
}

/* ESTILOS PARA A ANIMAÇÃO DE VERIFICAÇÃO */
#animacao-verificacao {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #1a1a1a;
    color: #FFD700;
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
    border: 1px solidrgb(73, 240, 7);
}

#animacao-verificacao.show {
    opacity: 1;
}

.checkmark {
    width: 100px;
    height: 100px;
    display: block;
    stroke-width: 2;
    stroke: #FFD700;
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
    stroke: #FFD700;
    fill: none;
    animation: stroke-inter .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    animation-play-state: paused;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 18;
    stroke-dashoffset: 18;
    stroke: #FFD700;
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
        box-shadow: inset 0px 0px 0px 12px #FFD700;
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Seleciona seu novo botão pelo ID
    const btnAbrirModalPagamento = document.getElementById('btn-abrir-modal-pagamento');

    const overlay = document.getElementById('overlay');
    const modalPagamento = document.getElementById('modal-pagamento');
    const btnFecharModal = document.getElementById('btn-fechar-modal');
    const formPagamento = document.getElementById('form-pagamento');
    const animacaoVerificacao = document.getElementById('animacao-verificacao');
    const checkmarkSvg = animacaoVerificacao.querySelector('.checkmark');

    // Esses elementos agora são manipulados diretamente pelo script
    // Certifique-se de que esses IDs existem no seu template se você quiser limpar o carrinho visualmente.
    // Se não existirem, pode ser necessário ajustar seu HTML para incluí-los ou remover essas linhas.
    const itensCarrinho = document.getElementById('itens-carrinho');
    const acoesCarrinho = document.querySelector('.acoes-carrinho');
    const totalDisplay = document.querySelector('#total .col-md-12 h4');

    // Referências aos campos de entrada do modal
    const numeroCartaoInput = document.getElementById('numero-cartao');
    const validadeCartaoInput = document.getElementById('validade-cartao');
    const cvvCartaoInput = document.getElementById('cvv-cartao');
    const cpfInput = document.getElementById('cpf');

    // Função para abrir o modal de pagamento
    const openPaymentModal = (source = 'button') => {
        if (source === 'input' && modalPagamento.style.display !== 'none' && modalPagamento.style.display !== '') {
            return;
        }
        overlay.style.display = 'block';
        modalPagamento.style.display = 'block';

        if (source === 'button') {
            numeroCartaoInput.focus();
        }
    };

    // Abre o modal de pagamento quando o botão "Pague com Cartão" é clicado
    if (btnAbrirModalPagamento) { // Verifica se o botão existe antes de adicionar o listener
        btnAbrirModalPagamento.addEventListener('click', () => openPaymentModal('button'));
    }


    // Abre o modal de pagamento ao focar em qualquer campo do formulário
    numeroCartaoInput.addEventListener('focus', () => openPaymentModal('input'));
    validadeCartaoInput.addEventListener('focus', () => openPaymentModal('input'));
    cvvCartaoInput.addEventListener('focus', () => openPaymentModal('input'));
    cpfInput.addEventListener('focus', () => openPaymentModal('input'));


    // Fecha o modal de pagamento
    btnFecharModal.addEventListener('click', () => {
        overlay.style.display = 'none';
        modalPagamento.style.display = 'none';
        formPagamento.reset(); // Limpa os campos do formulário
        checkmarkSvg.classList.remove('play-animation'); // Reseta o estado da animação
    });

    overlay.addEventListener('click', () => {
        btnFecharModal.click(); // Fecha o modal clicando no overlay
    });

    // --- Lógica de Envio do Formulário de Pagamento ---
    formPagamento.addEventListener('submit', async (event) => {
        event.preventDefault(); // Previne o envio padrão do formulário

        const cpf = cpfInput.value.trim();
        const numeroCartao = numeroCartaoInput.value.trim().replace(/\s/g, '');
        const validadeCartao = validadeCartaoInput.value.trim();
        const cvvCartao = cvvCartaoInput.value.trim();

        // --- VALORES DE TESTE PARA O BOTÃO "CONFIRMAR COMPRA" ---
        const CARTAO_VALIDO = '1234567890123456';
        const VALIDADE_VALIDA = '12/25'; // MM/AA
        const CVV_VALIDO = '123';

        let isFormValid = true;

        // 1. Valida CPF
        if (!cpf || cpf.length !== 11 || !/^\d+$/.test(cpf)) {
            alert('Por favor, digite um CPF válido (somente números, 11 dígitos).');
            isFormValid = false;
        }
        // 2. Valida Número do Cartão
        else if (numeroCartao !== CARTAO_VALIDO) {
            alert(`Número do cartão inválido. Use o número de teste: ${CARTAO_VALIDO}`);
            isFormValid = false;
        }
        // 3. Valida Validade do Cartão
        else if (validadeCartao !== VALIDADE_VALIDA) {
            alert(`Validade do cartão inválida. Use a validade de teste: ${VALIDADE_VALIDA}`);
            isFormValid = false;
        }
        // 4. Valida CVV
        else if (!cvvCartao || (cvvCartao.length !== 3 && cvvCartao.length !== 4) || !/^\d+$/.test(cvvCartao)) {
            alert('Por favor, digite um CVV válido (3 ou 4 dígitos numéricos).');
            isFormValid = false;
        }
        // 5. Valida CVV (com valor de teste)
        else if (cvvCartao !== CVV_VALIDO) {
            alert(`CVV inválido. Use o CVV de teste: ${CVV_VALIDO}`);
            isFormValid = false;
        }

        if (!isFormValid) {
            console.log('Validação do formulário falhou. Verifique os dados inseridos.');
            return;
        }

        // --- Se todas as validações passarem, o código continua a partir daqui ---

        // Esconde o modal de pagamento e o overlay
        modalPagamento.style.display = 'none';
        overlay.style.display = 'none';

        // Mostra e inicia a animação de verificação
        animacaoVerificacao.classList.remove('hidden');
        setTimeout(() => {
            animacaoVerificacao.classList.add('show');
            checkmarkSvg.classList.add('play-animation'); // Inicia a animação SVG
        }, 50);

        const animationDuration = 1500;
        const displayDuration = 2000;

        setTimeout(() => {
            // Limpa as informações do carrinho
            // Nota: Se 'itens-carrinho' não for o pai da sua tabela, você precisará ajustar este seletor.
            const tableBody = document.querySelector('.table-bordered tbody');
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr class="text-danger bg-danger">
                        <td>Produto</td>
                        <td>Valor R$</td>
                        <td>X</td>
                        <td>Sub Total R$</td>
                    </tr>
                `; // Reseta o cabeçalho da tabela, removendo todas as linhas de produtos
            }


            // Atualiza o total para R$ 0.00
            if (totalDisplay) {
                totalDisplay.innerHTML = 'Total : R$ 0.00';
            }


            // Opcionalmente, limpa outros elementos relacionados ao carrinho, se existirem
            if (itensCarrinho) {
                 itensCarrinho.innerHTML = '<p>Seu carrinho está vazio. Comece a comprar!</p>';
            }
            if (acoesCarrinho) {
                acoesCarrinho.innerHTML = ''; // Remove os botões de ação do carrinho
            }


            // Esconde a animação de verificação
            animacaoVerificacao.classList.remove('show');
            setTimeout(() => {
                animacaoVerificacao.classList.add('hidden');
                checkmarkSvg.classList.remove('play-animation');
            }, 500);
        }, animationDuration + displayDuration);
    });
});
</script>