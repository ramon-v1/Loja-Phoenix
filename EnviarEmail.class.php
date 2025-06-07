<?php
// Classe para envio de e-mails, estendendo a PHPMailer
class EnviarEmail extends PHPMailer {

    // Construtor da classe
    function __construct() {
        parent::__construct();      // Chama o construtor da PHPMailer

        $this->IsSMTP();            // Define o método SMTP para envio
        $this->IsHTML(true);        // Define que o e-mail será em formato HTML
        $this->CharSet = 'UTF-8';   // Define charset para UTF-8 para suportar caracteres especiais

        $this->SMTPDebug = 0;       // Desabilita debug do SMTP (0 = off)

        // Configurações do servidor SMTP, carregadas da classe Config
        $this->Port = Config::EMAIL_PORTA;
        $this->Host = Config::EMAIL_HOST;
        $this->SMTPAuth = Config::EMAIL_SMTPAUTH;

        // Configurações do remetente
        $this->FromName = Config::EMAIL_NOME;     // Nome que aparecerá no remetente
        $this->From = Config::EMAIL_USER;         // E-mail remetente
        $this->Username = Config::EMAIL_USER;     // Usuário SMTP
        $this->Password = Config::EMAIL_SENHA;    // Senha SMTP
    }

    /**
     * Método para enviar e-mail
     * @param string $assunto Assunto do e-mail
     * @param string $msg Corpo do e-mail (HTML)
     * @param array $destinatarios Array com e-mails destinatários
     * @param string|null $reply E-mail para resposta (reply-to), opcional
     * @return bool Retorna true se enviado com sucesso, false em caso de erro
     */
    function Enviar($assunto, $msg, $destinatarios = array(), $reply = null) {
        try {
            $this->ClearAllRecipients();  // Limpa destinatários anteriores

            $this->Subject = $assunto;   // Define o assunto do e-mail
            $this->Body = $msg;           // Define o corpo do e-mail em HTML

            // Se e-mail de reply-to foi passado, adiciona-o
            if ($reply) {
                $this->AddReplyTo($reply);
            }

            // Adiciona todos os destinatários no loop
            foreach ($destinatarios as $email) {
                $this->AddAddress($email, $email);
            }

            // Tenta enviar o e-mail e retorna true ou lança exceção
            if ($this->Send()) {
                return true;
            } else {
                throw new Exception($this->ErrorInfo);
            }

        } catch (Exception $e) {
            // Em caso de erro, exibe mensagem e retorna false
            echo "<h4>Mailer Exception: " . $e->getMessage() . "</h4>";
            return false;
        }
    }
}
?>
