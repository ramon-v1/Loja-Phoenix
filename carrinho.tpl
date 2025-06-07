<h3>Meu Carrinho</h3>

<!-- Botões e opções superiores -->
<section class="row">
    <div class="col-md-12" align="right">
        <a href="{$PAG_PRODUTOS}" class="btn btn-info" title="">Comprar Mais</a>
    </div>
</section>

<br>

<!-- Tabela de listagem dos itens -->
<section class="row">
    <center>
        <table class="table table-bordered" style="width: 95%;">
            <tr class="text-danger bg-danger">
                <td></td>
                <td>Produto</td>
                <td>Valor R$</td>
                <td>Qtd</td>
                <td>Sub Total R$</td>
                <td></td>
            </tr>

            {foreach from=$PRO item=P}
                <tr>
                    <td><img src="{$P.pro_img}" alt="{$P.pro_nome}"></td>
                    <td>{$P.pro_nome}</td>
                    <td>{$P.pro_valor}</td>
                    <td>{$P.pro_qtd}</td>
                    <td>{$P.pro_subTotal}</td>
                    <td>
                        <form name="carrinho_dell" method="post" action="{$PAG_CARRINHO_ALTERAR}">
                            <input type="hidden" name="pro_id" value="{$P.pro_id}">
                            <input type="hidden" name="acao" value="del">
                            <button class="btn btn-danger btn-sm">
                                <i class="glyphicon glyphicon-minus"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            {/foreach}
        </table>
    </center>
</section>

<!-- Bloco de cálculo de frete -->
<section class="row" id="dadosfrete">
    <div class="col-md-4"></div>

    <div class="col-md-4">
        <!-- Pode adicionar campos adicionais de frete aqui -->
    </div>

    <div class="col-md-4">
        <form action="teste.php" method="post">
            <div class="form-group">
                <label for="cep_frete"></label>
            </div>
            <button class="btn btn-warning btn-block" id="buscar_frete" type="submit">
                <i class="glyphicon glyphicon-send"></i> Calcular Frete
            </button>
        </form>
    </div>
</section>

<br>
<hr>

<!-- Total e botões inferiores -->
<section class="row" id="total">
    <div class="col-md-4 text-right">
        <!-- Espaço reservado -->
    </div>

    <div class="col-md-4 text-right text-danger bg-warning">
        <h4>Total: R$ {$TOTAL}</h4>
    </div>

    <div class="col-md-4">
        <!-- Botão Limpar -->
        <form name="limpar" method="post" action="{$PAG_CARRINHO_ALTERAR}">
            <input type="hidden" name="acao" value="limpar">
            <input type="hidden" name="pro_id" value="1">
            <button class="btn btn-danger btn-block">
                <i class="glyphicon glyphicon-trash"></i> Limpar Tudo
            </button>
            <br>
        </form>

        <!-- Botão Confirmar Pedido -->
        <form name="pedido_confirmar" id="pedido_confirmar" method="post" action="{$PAG_CONFIRMAR}">
            
        </form>
    </div>
</section>

<br>
<hr>
