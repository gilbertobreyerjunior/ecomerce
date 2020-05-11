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

?>
