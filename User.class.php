<?php

// Classe User que estende a classe Conexao para manipulação de usuários no banco de dados
class User extends Conexao {
    
    // Atributos privados para armazenar dados do usuário
    private $user, $senha, $email;
            
    // Construtor que chama o construtor da classe pai Conexao
    function __construct() {
        parent::__construct();
    }
    
    /**
     * Verifica se um usuário com determinado email existe no banco
     * @param string $email - email do usuário a ser buscado
     * @return boolean - retorna true se existir, false caso contrário
     */
    function GetUserEmail($email) {
        // Define o email no objeto
        $this->setEmail($email);
        
        // Consulta SQL para buscar usuário pelo email
        $query = "SELECT * FROM {$this->prefix}user WHERE user_email = :email";
        
        // Parâmetros para a consulta preparada
        $params = array(':email' => $this->getEmail());
        
        // Executa a consulta SQL
        $this->ExecuteSQL($query, $params);
        
        // Retorna o total de registros encontrados (0 ou 1)
        return $this->TotalDados();
    }
    
    /**
     * Altera a senha do usuário com base no email
     * @param string $senha - nova senha (em texto puro)
     * @param string $email - email do usuário para alteração
     * @return boolean - true se a alteração for bem sucedida, false caso contrário
     */
    function AlterarSenha($senha, $email) {
        // Define a nova senha (criptografada) e o email no objeto
        $this->setSenha($senha);
        $this->setEmail($email);

        // Monta a consulta SQL para atualizar a senha do usuário
        $query = "UPDATE {$this->prefix}user SET user_pw = :senha WHERE user_email = :email";
        
        // Parâmetros para a consulta preparada
        $params = array(':senha' => $this->getSenha(), ':email' => $this->getEmail());
        
        // Executa a consulta e retorna true se sucesso, false caso contrário
        if ($this->ExecuteSQL($query, $params)):
            return true;
        else:
            return false;
        endif;
    }

    // --- GETTERS E SETTERS ---

    function getUser() {
        return $this->user;
    }

    function getSenha() {
        return $this->senha;
    }

    function getEmail() {
        return $this->email;
    }

    // Define o nome do usuário
    function setUser($user) {
        $this->user = $user;
    }

    // Define a senha, já criptografando com md5
    function setSenha($senha) {
        $this->senha = md5($senha);
    }

    // Define o email
    function setEmail($email) {
        $this->email = $email;
    }
}

?>
