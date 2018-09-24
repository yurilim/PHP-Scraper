Usando a biblioteta SImple_html_dom é possível realizar o Scrapper, técnica usada para obter partes de 
sites específicos.

Observação: Verificar o uso do método da biblioteca, adequado para obtenção da url, caso tenha sofrido alteração.  
Exemplo simples abaixo:

<?php
@header("Content-Type:text/html; charset=utf8");
require(__DIR__. "/../simple_html_dom.php");

$url = 'http://qualquersite.com.br';

$site = file_get_html($url);

//var_dump($site);
?>
