<?php

use \Hcode\Page;
use \Hcode\Model\Product;

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


		

									//iremos recuperar o id da funcao com get
							
									$category = new Category();
									//fazemos um cast para int para ter certeza que isso é um numero 
									$category->get((int)$idcategory);
							
									//iremos usar a classe de paginas do site 
							
									$page = new Page();
									//iremos passar os dados dessa categoria
									$page->setTpl("category", [
										'category'=>$category->getValues(),
										'products'=>[]
							
									]);
							
								});
							

?>
