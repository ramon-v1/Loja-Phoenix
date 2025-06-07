<?php 

// Cria uma nova instância do template Smarty
$smarty = new Template();

// Atribui a variável 'BANNER' para o template, com a URL da imagem redimensionada
$smarty->assign('BANNER', Rotas::ImageLink('banner.jpg', 750, 230));

// Exibe o template 'home.tpl'
$smarty->display('home.tpl');

// Inclui o arquivo controller de produtos para carregar funcionalidades relacionadas a produtos
include_once Rotas::get_Pasta_Controller() . '/produtos.php';

?>
