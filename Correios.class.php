<?php
class Correios
{
    // Variáveis públicas da classe
    public
        $frete = array(),       // Array para armazenar resultados do frete
        $error,                 // Para armazenar possíveis erros
        $servico,               // Código do serviço (PAC ou SEDEX)
        $servico2,              // Código do segundo serviço (SEDEX ou PAC)
        $cepOrigem,             // CEP de origem (remetente)
        $cepDestino,            // CEP de destino (destinatário)
        $peso,                  // Peso do pacote em kg
        $formato = '1',         // Formato do pacote (1 = caixa/pacote)
        $comprimento,           // Comprimento do pacote em cm
        $altura,                // Altura do pacote em cm
        $largura,               // Largura do pacote em cm
        $diametro,              // Diâmetro do pacote em cm
        $maoPropria = 'N',      // Indica se há serviço de "mão própria" ('S' ou 'N')
        $valordeclarado = '0',  // Valor declarado para seguro
        $avisoRecebimento = 'N',// Serviço de aviso de recebimento ('S' ou 'N')
        $retorno = 'xml';       // Tipo de retorno da API (xml)

    // Variáveis privadas da classe
    private
        $url   = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx',  // URL da API dos Correios
        $sedex = '04014',   // Código do serviço SEDEX
        $pac   = '04510';   // Código do serviço PAC

    /**
     * Construtor da classe
     * @param string $destino CEP de destino
     * @param float $peso Peso em kg
     */
    function __construct($destino, $peso)
    {
        // Define os códigos dos serviços que serão consultados
        $this->servico   = $this->pac;    // PAC
        $this->servico2  = $this->sedex;  // SEDEX

        // Define o CEP de origem baseado na constante do sistema
        $this->cepOrigem = Config::SITE_CEP;

        // Define o CEP de destino e peso conforme parâmetros
        $this->cepDestino = $destino;
        $this->peso       = $peso;

        // Define dimensões fixas do pacote em centímetros
        $this->comprimento = '35';
        $this->altura      = '35';
        $this->largura     = '35';
        $this->diametro    = '90';
    }

    /**
     * Realiza a consulta e cálculo dos valores e prazos dos serviços PAC e SEDEX
     * @return array|bool Retorna array com valores e prazos ou false em caso de erro
     */
    public function Calcular()
    {
        // Monta a URL para consultar o serviço PAC
        $cURL = curl_init(sprintf(
            $this->url . '?nCdServico=%s&sCepOrigem=%s&sCepDestino=%s&nVlPeso=%s&nCdFormato=%s&nVlComprimento=%s&nVlAltura=%s&nVlLargura=%s&nVlDiametro=%s&sCdMaoPropria=%s&nVlValorDeclarado=%s&sCdAvisoRecebimento=%s&StrRetorno=%s',
            $this->servico,
            $this->cepOrigem,
            $this->cepDestino,
            $this->peso,
            $this->formato,
            $this->comprimento,
            $this->altura,
            $this->largura,
            $this->diametro,
            $this->maoPropria,
            $this->valordeclarado,
            $this->avisoRecebimento,
            $this->retorno
        ));

        // Configura a opção para retornar o resultado da execução
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

        // Executa a requisição e captura o retorno em XML
        $string = curl_exec($cURL);

        // Fecha a conexão cURL
        curl_close($cURL);

        // Carrega o XML retornado em um objeto simples
        $xml = simplexml_load_string($string);

        // Verifica se houve erro no serviço PAC
        if ($xml->Erro != '') :
            // Armazena o erro para ser recuperado posteriormente
            $this->error = array($xml->cServico->Erro, $xml->cServico->MsgErro);
            return false; // Retorna false indicando falha

        else :
            // Se não houve erro, captura os dados do serviço PAC
            $servico1 = $xml->cServico;

            // Agora monta a URL para consultar o serviço SEDEX
            $cURL = curl_init(sprintf(
                $this->url . '?nCdServico=%s&sCepOrigem=%s&sCepDestino=%s&nVlPeso=%s&nCdFormato=%s&nVlComprimento=%s&nVlAltura=%s&nVlLargura=%s&nVlDiametro=%s&sCdMaoPropria=%s&nVlValorDeclarado=%s&sCdAvisoRecebimento=%s&StrRetorno=%s',
                $this->servico2,
                $this->cepOrigem,
                $this->cepDestino,
                $this->peso,
                $this->formato,
                $this->comprimento,
                $this->altura,
                $this->largura,
                $this->diametro,
                $this->maoPropria,
                $this->valordeclarado,
                $this->avisoRecebimento,
                $this->retorno
            ));

            // Configura opção de retorno para execução
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

            // Executa a requisição e captura o retorno do SEDEX em XML
            $string = curl_exec($cURL);

            // Fecha a conexão cURL
            curl_close($cURL);

            // Carrega o XML do SEDEX
            $xml = simplexml_load_string($string);
            $servico2 = $xml->cServico;

            // Retorna um array com os dados dos dois serviços
            return [
                'pac' => [
                    'valor' => $servico1->Valor,
                    'tipo'  => 'PAC',
                    'Prazo' => $servico1->PrazoEntrega,
                    'erro'  => 0,
                ],
                'sedex' => [
                    'valor' => $servico2->Valor,
                    'tipo'  => 'SEDEX',
                    'Prazo' => $servico2->PrazoEntrega,
                    'erro'  => 0,
                ],
            ];

        endif;
    }

    /**
     * Retorna erros armazenados na classe, se houver
     * @return array|false Array com erro ou false se não houver erro
     */
    public function error()
    {
        if (is_null($this->error)) {
            return false;
        } else {
            return $this->error;
        }
    }
}
