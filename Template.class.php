<?php 

// Classe Template estendendo SmartyBC para uso do motor de templates Smarty
class Template extends SmartyBC {

    // Método construtor
    function __construct() {
        // Chama o construtor da classe pai SmartyBC
        parent::__construct();

        // Define o diretório onde estão os arquivos de template (.tpl)
        $this->setTemplateDir('view/');

        // Define o diretório onde serão armazenados os templates compilados
        $this->setCompileDir('view/compile/');

        // Define o diretório para armazenamento de cache dos templates
        $this->setCacheDir('view/cache/');
    }
}

?>
