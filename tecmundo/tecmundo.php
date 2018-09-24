<?php
@header("Content-Type:text/html; charset=utf8");
require(__DIR__. "/../simple_html_dom.php");

$log = fopen(__DIR__ .'/tecmundo.log', 'w');
$data = date('r');

$url = 'http://rss.tecmundo.com.br/feed';

$diretorio = "/var/www/html/multitoky_web/dados/resources/rss";
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

		$xml = fopen($diretorioXML . '/' . $provedorNome . '.xml', 'w');

		fwrite($xml, "<rss version='2.0'>" ."\n");
		fwrite($xml, "<channel>" ."\n");
		fwrite($xml, "<title>Tecmundo MTK</title>" ."\n");
		fwrite($xml, "<description>RSS Tecmundo por Multitoky</description>"."\n");
		fwrite($xml, "<language>pt-BR</language>" ."\n");

	  $tecmundo = simplexml_load_file($url); //carrega XML de RSS
	  $cont = 1;  
	  
	  if (file_exists($diretorioImagens)){        
	    foreach (glob($diretorioImagens .'/*.jpg') as $apagarImg) {
	      unlink($apagarImg);
	    }
	  } 
	    
	  	foreach ($tecmundo -> channel -> item as $conteudo){ //para cada item de noticia do XML...

			  $link = file_get_html($conteudo -> link); //carrega HTML da noticia
			  $http = get_headers($conteudo -> link);

			  if (strpos($http[0], '200') || strpos($http[0], '302') !== false){
			    
			    $artigos = $link -> find('.article-header'); //pega artigos da noticia


			     //var_dump($artigos);
			      //echo '<AquiO>';
			      //print_r($obterImagem);
			      //echo '</AquiO';
				  //die();



			    foreach ($artigos as $capturaImagem) { //para cada artigo da noticia...

			      $obterImagem = $capturaImagem -> find('img',0); //obtem primeira imagem

			      var_dump($obterImagem);
			      //echo '<AquiO>';
			      //print_r($obterImagem);
			      //echo '</AquiO';
				  	die();

			      	if ($obterImagem !== null) { //se existe imagem (não é video)...
			
									$caminhoImg = $diretorioImagens . "/" . $provedorNome . $cont . ".jpg";
					     		$blurImg = $diretorioImagens . "/" . $blur . ".jpg";

						    /*echo 'DEBUG';
		          	 	echo '<AquiO>';
		         	  	print_r($obterImagem);
		          	  echo '</AquiO>';
		          	  die();
		          	*/

					        $rssfoto = fopen($caminhoImg, "w+");
					        fwrite($rssfoto, file_get_contents($obterImagem -> src)); //salva imagem baseada na URI da noticia
					        fclose($rssfoto);

					        $md5diretorio = md5_file($caminhoImg);							
									$md5Img = md5_file($obterImagem -> src);


									if ($md5diretorio != $md5Img) {
								    unlink($caminhoImg);
								    fwrite($log, date('d/m/Y H:i:s') . ' -- Imagem corrompida --' ."\n");

									} else{
										
										shell_exec('convert ' . $caminhoImg . ' -adaptive-resize 1920 ' . $caminhoImg);
										shell_exec('convert ' . $caminhoImg . ' -adaptive-resize x1080 -blur 0x18 ' . $blurImg);
										shell_exec('mogrify ' . $blurImg . ' -extent 1920x1080 -gravity center ' . $blurImg);
										shell_exec('composite ' . $caminhoImg . ' -gravity center ' . $blurImg . ' '. $caminhoImg);
						
									  }
				        
						      fwrite($log, date('d/m/Y H:i:s') . ' -- Caminho da imagem -- ' . $caminhoImg ."\n");

						      fwrite($xml, '<item>' ."\n");
						      fwrite($xml, '<description>' . $conteudo -> title .'</description>' ."\n");
						      fwrite($xml, '<linkfoto>');
						      fwrite($xml, 'http://gerenciador.multitoky.com.br/dados/resources/rss/imagens/tecmundo/tecmundo' . $cont . '.jpg');
						      fwrite($xml, '</linkfoto>' . "\n");
						      fwrite($xml, '<pubdate>' . $data .'</pubdate>' ."\n");
						      fwrite($xml, '</item>' ."\n\n");

			      	}else{
		      			fwrite($log, date('d/m/Y H:i:s') . ' -- Não existe imagem para notícia --' ."\n");
		      		 }

			      $cont++;
		    	}





		  }else{
		    fwrite($log, date('d/m/Y H:i:s') . ' -- URL não encotrada ou impossível de ser acessada --' ."\n");        
		 }

	  	}

	  //unlink($blurImg);

	  fwrite($xml, '</channel>' . "\n" . '</rss>');
 	  fclose($xml);

	}else{
		fwrite($log, date('d/m/Y H:i:s') . ' -- Pasta não foi criada --' .$provedorNome ."\n");
	 }

fclose($log);
	
?>