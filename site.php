<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;

$app->get('/', function() {
	//iremos trazer todos os produtos que estao no banco
$products = Product::listAll();


	//instanciamos a classe Page e entao ira chamar o construct e ira adicionar o header a tela

	$page = new Page();
//quando chamar o setTpl("index") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("index", [ //iremos tratar a nossa lista
		'products'=>Product::checkList($products) //iremos trazer os produtos na tela


	]);

	//Testes
	// $sql = new Hcode\DB\Sql();

	// $results = $sql->select("SELECT * FROM  tb_users");


	// echo json_encode($results);

});



		//iremos criar a rota para a categoria
								  //iremos passar o id da categoria dentro da function
								  //essa rota ira retornar uma pagina do site 
								  $app->get("/categories/:idcategory", function($idcategory){

										//se foi definida a pagina se esta passando na url, fazemos um cast para ver se e um numero que esta sendo passado do getpage, se nao e a pagina 1 mesmo
											$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

									//iremos recuperar o id da funcao com get
							
									$category = new Category();
									//fazemos um cast para int para ter certeza que isso é um numero 
									$category->get((int)$idcategory);
							
										//funcao para paginacao dos products 
										//iremos passar fora porque tem muitas informacoes
																			//passamos como parametro o page da verificacao	
									$pagination = $category->getProductsPage($page);
									
									//crimos um array vazio para pages do loop
									$pages = [];
									//criamos um for ele ira acontecer do numero 1 ate quando for menor ou igual o meu total de paginas, que esta no meu pagination e la eu tenho o total de paginas pages    que o meu 
									for($i=1; $i <= $pagination['pages']; $i++){


										//irei fazer um array push pois iremos adicionar mais um item ao nosso array page 
										array_push($pages, [
											//passamos no link o valor a rota do categories, barra o id da minha categoria que esta carregado, e iremos concatenar com o interrogacao page e passamos o $i que e a variavel de incremento, quando passamos o interrogacao ele permite que voce mande variaveis de query string      
											//entao de fato antes do interrogacao temos o nosso caminho de fato a url
											'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
											'page'=>$i //o numero da pagina para mostrar ao usuario


										]);

									}
									//iremos usar a classe de paginas do site 
							
									$page = new Page();
									//iremos passar os dados dessa categoria
									$page->setTpl("category", [
										'category'=>$category->getValues(),
										//trazendo a lista dos nossos produtos, se eu não passar nada dentro do get ira ser true e ira trazer todos os produtos relacionados as categorias
												//os produtos estao em pagination e um array estao na chave data
										'products'=>$pagination["data"],
										'pages'=>$pages
							
									]);
							
								});
							

?>
