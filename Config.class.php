<?php 

Class Config{

	//INFORMÃÇÕES BÁSICAS DO SITE
	/*const SITE_URL = "http://localhost";
	const SITE_PASTA = "loja";
	const SITE_NOME = "Loja do Freitas - PHP 7 e Mysqli";
	const SITE_EMAIL_ADM = "lojavirtualfreitas@gmail.com";
	const BD_LIMIT_POR_PAG = 6;
	const SITE_CEP = '31535522';


	//INFORMAÇÕES DO BANCO DE DADOS
	const BD_HOST = "localhost",
		  BD_USER = "root",
		  BD_SENHA = "",
		  BD_BANCO = "lojafreitas",
		  BD_PREFIX = "qc_"; */




	//INFORMÃÇÕES BÁSICAS DO SITE HOSPEDADO
	const SITE_URL = "http://localhost";
	const SITE_PASTA = "loja";
	const SITE_NOME = "FÊNIX ESSENCE CLOTHING";
	const SITE_EMAIL_ADM = "roger.azulao@gmail.com";
	const BD_LIMIT_POR_PAG = 6;
	const SITE_CEP = '05270150';


	//INFORMAÇÕES DO BANCO DE DADOS HOSPEDADO
	const BD_HOST = "localhost",
		  BD_USER = "root",
		  BD_SENHA = "",
		  BD_BANCO = "lojafreitas",
		  BD_PREFIX = "qc_";




	//INFORMAÇÕES PARA PHP MAILLER
	const EMAIL_HOST = "smtp.gmail.com";
	const EMAIL_USER = "roger.azulao@gmail.com";
	const EMAIL_NOME = "Contato Loja Freitas";
	const EMAIL_SENHA = "lojadofreitas";
	const EMAIL_PORTA = 587;
	const EMAIL_SMTPAUTH = true;
	const EMAIL_SMTPSECURE = "tls";
	const EMAIL_COPIA = "roger.azulao@gmail.com";



	//CONSTANTES PARA O PAGSEGURO
	const PS_EMAIL  = "qcursos@hotmail.com"; // email pagseguro
    const PS_TOKEN  = "0E86ADF6373348509E7B35389D92004C"; // token produção
    const PS_TOKEN_SBX = "1FB4D7860EA9491BA7AB4A9D9336C275";  // token do sandbox
    const PS_AMBIENTE = "production"; // production   /  sandbox

}
 ?>

