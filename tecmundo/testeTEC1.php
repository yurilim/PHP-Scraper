<?php
@header("Content-Type:text/html; charset=utf8");
require(__DIR__. "/../simple_html_dom.php");

$log = fopen(__DIR__ .'/tecmundo.log', 'w');
$data = date('r');

$url = 'https://rss.tecmundo.com.br/feed';

$diretorio = "/var/TesteTEC";
$provedorNome = 'tecmundo';
$blur = 'blur';
$diretorioImagens = $diretorio . "/imagens/". $provedorNome;
$diretorioXML = $diretorio . "/xml/tecmundo";

$pasta_criada = true;   
  if(!file_exists($diretorioImagens)){
    if(!mkdir($diretorioImagens, 0775, true)){
      $pasta_criada = false;
    }
  }

  if (!file_exists($diretorioXML)) {
  	if(!mkdir($diretorioXML, 0775, true)){
      $pasta_criada = false;
    }
  }

	if($pasta_criada){

		
	  $tecmundo = simplexml_load_file($url); //carrega XML de RSS
	  $cont = 1;  
	
	    
	  foreach ($tecmundo -> channel -> item as $conteudo){ //para cada item de noticia do XML...

		  $link = file_get_html($conteudo -> link); //carrega HTML da noticia
		  $http = get_headers($conteudo -> link);

		  if (strpos($http[0], '200') || strpos($http[0], '302') !== false){
		    
		    $artigos = $link -> find('.article-header'); //pega artigos da noticia

		    var_dump($artigos);

				    foreach ($artigos as $capturaImagem) { //para cada artigo da noticia...

				      $obterImagem = $capturaImagem -> find('img',0); //obtem primeira imagem

					      if ($obterImagem !== null) { //se existe imagem (não é video)...
					
									$caminhoImg = $diretorioImagens . "/" . $provedorNome . $cont . ".jpg";
					     		

						      $rssfoto = fopen($caminhoImg, "w+");
						      fwrite($rssfoto, file_get_contents($obterImagem -> src)); //salva imagem baseada na URI da noticia
						      fclose($rssfoto);
						      
						    
						      //echo "criacao de arquivo";

						      fwrite($log, date('d/m/Y H:i:s') . ' -- Caminho da imagem -- ' . $caminhoImg ."\n");

					      }else{
				      		fwrite($log, date('d/m/Y H:i:s') . ' -- Não existe imagem para notícia --' ."\n");
				      	}

				      $cont++;
				    }
			}
		}
	}


?> 		    