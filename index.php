<?php 

require_once("vendor/autoload.php");
//Usamos os seguintes namespaces
use \Slim\Slim;
//Iremos fazer o use da classe page que estamos usando agora
use \Hcode\Page;

use \Hcode\PageAdmin;


$app = new Slim(); //criando a instancia do slim estamos criando uma nova aplicacao


$app->config('debug', true); //deixando no modo debug



//Nossas rotas

$app->get('/', function() {
	
	//instanciamos a classe Page e entao ira chamar o construct e ira adicionar o header a tela

	$page = new Page();
//quando chamar o setTpl("index") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("index");

	//Testes
	// $sql = new Hcode\DB\Sql();

	// $results = $sql->select("SELECT * FROM  tb_users");


	// echo json_encode($results);

});


$app->get('/admin', function() {
	
	//instanciamos a classe PageAdmin e entao ira chamar o construct e ira adicionar o header a tela

	$page = new PageAdmin();
//quando chamar o setTpl("index") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("index");


});


$app->run(); // mandando executar o projeto

 ?>