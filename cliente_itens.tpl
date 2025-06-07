<br>
<h4 class="text-center">Dados do pedido</h4>

<!-- informações sobre o pedido -->
<section class="row">
    <center>
        <table class="table table-bordered" style="width: 80%">
            <tr class="bg-success">
                <td><b>Data:</b> {$ITENS.1.ped_data}</td>
                <td><b>Hora:</b> {$ITENS.1.ped_hora}</td>
                <td><b>Ref:</b> {$ITENS.1.ped_ref}</td>
                <td><b>Status:</b> {$ITENS.1.ped_pag_status}</td>
            </tr>
        </table>
    </center>
</section>

<!-- listagem dos itens -->
<section class="row" id="listaitens">
    <center>
        <table class="table table-bordered" style="width: 80%">
            <tr class="text-success bg-success">
                <td></td>
                <td>Item</td>
                <td>Valor Uni</td>
                <td>X</td>
                <td>Sub</td>
            </tr>

            {foreach from=$ITENS item=P}
                <tr>
                    <td><img src="{$P.item_img}" alt=""></td>
                    <td>{$P.item_nome}</td>
                    <td class="text-right">{$P.item_valor}</td>
                    <td class="text-center">{$P.item_qtd}</td>
                    <td class="text-right">{$P.item_sub}</td>
                </tr>
            {/foreach}

        </table>
    </center>
</section>

<!-- resumo do pedido -->
<section class="row" id="resumo">
    <center>
        <table class="table table-bordered" style="width: 80%">
            <tr>
                <td class="text-danger"><b>Frete:</b> {$ITENS.1.ped_frete_valor}</td>
                <td class="text-danger"><b>Total:</b> {$TOTAL}</td>
                <td class="text-danger"><b>Final:</b> {$ITENS.1.ped_frete_valor + $TOTAL}</td>
            </tr>
        </table>
    </center>
</section>

<!-- formas de pagamento se não estiver pago -->
{if $ITENS.1.ped_pag_status == 'NAO'}
    <section class="row">
        <h3 class="text-center">Formas de pagamento</h3>

        <div class="col-md-4"></div>

        <div class="col-md-4">
       <form method="post" action="pagamento.php">
    <button type="submit" class="btn btn-success btn-lg btn-block">
        Pague com o cartão
    </button>
</form>

            <img src="{$TEMA}/images/logo-pagseguro.png" alt="">

            <script type="text/javascript" src="{$PS_SCRIPT}"></script>
        </div>

        <div class="col-md-4"></div>
    </section>
{/if}
