<?php

class Sistema {

    /**
     * Retorna a data atual no formato brasileiro (dd/mm/aaaa)
     * @return string Data atual formato BR
     */
    static function DataAtualBR() {
        return date('d/m/Y');
    }

    /**
     * Retorna a data atual no formato americano (aaaa-mm-dd), usado em bancos de dados MySQL
     * @return string Data atual formato US
     */
    static function DataAtualUS() {
        return date('Y-m-d');
    }

    /**
     * Retorna a hora atual no formato hh:mm:ss
     * @return string Hora atual
     */
    static function HoraAtual() {
        return date('H:i:s');
    }

    /**
     * Formata um valor numérico em moeda brasileira (ex: 1500.99 => 1.500,99)
     * @param float $valor Valor a ser formatado
     * @return string Valor formatado em Real (R$)
     */
    static function MoedaBR($valor) {
        return number_format($valor, 2, ",", ".");
    }

    /**
     * Converte data do formato americano (aaaa-mm-dd) para o formato brasileiro (dd/mm/aaaa)
     * @param string $data Data no formato americano
     * @return string Data no formato brasileiro
     */
    public static function Fdata($data) {
        $data_correta = explode("-", $data);
        $data = $data_correta[2] . "/" . $data_correta[1] . "/" . $data_correta[0];
        return $data;
    }

    /**
     * Gera uma senha aleatória
     * @return string Senha gerada
     */
    static function GerarSenha() {
        $tamanho = 1;
        $string = "";

        for ($i = 0; $i < $tamanho; $i++) {
            // Gera caracteres aleatórios e números para montar a senha
            $string .= chr(rand(109, 122));
            $string .= rand(40, 99);
            $string .= chr(rand(109, 122));
            $string .= rand(20, 89);
            $string .= chr(rand(109, 122));
            $string .= chr(rand(109, 122));
        }
        // Substitui caracteres potencialmente confusos
        $string = str_replace('o', 'z', $string);
        $string = str_replace('0', 'b', $string);

        return $string;
    }

    /**
     * Valida um CPF
     * @param string $cpf CPF a ser validado
     * @return bool true se válido, false caso contrário
     */
    static function ValidarCPF($cpf = false) {
        $d1 = 0;
        $d2 = 0;

        // Remove tudo que não seja número
        $cpf = preg_replace("/[^0-9]/", "", $cpf);

        // Lista de CPFs inválidos comuns
        $ignore_list = array(
            '00000000000', '01234567890', '11111111111', '22222222222', '33333333333',
            '44444444444', '55555555555', '66666666666', '77777777777', '88888888888',
            '99999999999'
        );

        // Verifica tamanho e se está na lista de inválidos
        if (strlen($cpf) != 11 || in_array($cpf, $ignore_list)) {
            return false;
        } else {
            // Calcula o primeiro dígito verificador
            for ($i = 0; $i < 9; $i++) {
                $d1 += $cpf[$i] * (10 - $i);
            }
            $r1 = $d1 % 11;
            $d1 = ($r1 > 1) ? (11 - $r1) : 0;

            // Calcula o segundo dígito verificador
            for ($i = 0; $i < 9; $i++) {
                $d2 += $cpf[$i] * (11 - $i);
            }
            $r2 = ($d2 + ($d1 * 2)) % 11;
            $d2 = ($r2 > 1) ? (11 - $r2) : 0;

            // Verifica se os dígitos conferem
            return (substr($cpf, -2) == $d1 . $d2) ? true : false;
        }
    }

    /**
     * Obtém o IP do usuário (considera proxies)
     * @return string Endereço IP
     */
    static function GetIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Exibe um botão "Voltar" que volta para a página anterior usando JavaScript
     */
    static function VoltarPagina() {
        echo '<script>
                function goBack() {
                    window.history.back();
                }
              </script>';
        echo '<button onclick="goBack()" class="btn btn-default">
                <i class="glyphicon glyphicon-circle-arrow-left"></i> Voltar
              </button>';
    }

    /**
     * Gera um slug amigável para URL baseado em uma string
     * @param string $string Texto original
     * @return string Slug formatado
     */
    static function GetSlug($string) {
        if (is_string($string)) {
            $string = strtolower(trim(utf8_decode($string)));

            $before = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr';
            $after  = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
            $string = strtr($string, utf8_decode($before), $after);

            $replace = array(
                '/[^a-z0-9.-]/' => '-',
                '/-+/'         => '-',
                '/\-{2,}/'     => ''
            );

            $string = preg_replace(array_keys($replace), array_values($replace), $string);
        }
        return trim(substr($string, 0, 55));
    }

    /**
     * Criptografa uma string usando SHA-512
     * @param string $valor Valor a ser criptografado
     * @return string Valor criptografado
     */
    static function Criptografia($valor) {
        return hash('SHA512', $valor);
    }
}

?>
