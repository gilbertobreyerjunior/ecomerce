<?php 

session_start();

require_once("vendor/autoload.php");
//Usamos os seguintes namespaces
use \Slim\Slim;
//Iremos fazer o use da classe page que estamos usando agora
use \Hcode\Page;

use \Hcode\PageAdmin;

use \Hcode\Model\User;


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
	
	User::verifyLogin();


	//instanciamos a classe PageAdmin e entao ira chamar o construct e ira adicionar o header a tela

	$page = new PageAdmin();
//quando chamar o setTpl("index") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("index");


});




$app->get('/admin/login', function() {
	
	//instanciamos a classe PageAdmin e entao ira chamar o construct e ira adicionar o header a tela

	$page = new PageAdmin([
//desabilitando o header e footer padrão
		"header"=>false, 
		"footer"=>false

	]);
//quando chamar o setTpl("login") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("login");


});



$app->post('/admin/login', function() {
	//Iremos validar o login criamos uma nova classe que o nosso model do nosso Usuario, e iremos criar um metodo estatico chamado login ele ira receber o post do login do formulario login com o usuario e senha se não ocorrer erro podemos redirecionar para a nossa homepage da nossa administracao fazendo um header(Locacion: /admin )
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
//E paramos a execução aqui
	exit;

	


});

$app->get('/admin/logout', function() {

User::logout();

header("Location: /admin/login");

exit;

});



$app->run(); // mandando executar o projeto

 ?>