<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;


//Rota para listar os usuarios
$app->get('/admin/users', function() {

	//verificando se o usuario esta logado com um metodo estatico
User::verifyLogin();

//se o atrib existir vem ela mesmo, se ela nao tiver traz vazio
$search = (isset($_GET['search'])) ? $_GET['search'] : "";
//se for definido na minha url um page entao sera o int desse page, se nao for definida a minha pagina atual 
$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;



//se o search for diferente de vazio iremos fazer a chamada de um metodo
	if ($search != '') {

		$pagination = User::getPageSearch($search, $page, 1);
		
	} else {
//se nao 
//pagination ira receber o getPage de usuarios a pagina atual e a 1 mesmo
$pagination = User::getPage($page);

	}

//criamos um array chamado pages para poder adicionar elementos nele
$pages = [];

//fazemos um for para percorrer essas pages
for ($x = 0; $x < $pagination['pages']; $x++)
{

//fazemos um array push para adicionar essas informacoes dentro do atrib pages que e o novo array
	array_push($pages, [
		'href'=>'/admin/users?'.http_build_query([
			'page'=>$x+1, //a pagina
			'search'=>$search //o nosso search

		]),
		'text'=>$x+1 //e o texto que e o numero da pagina colocamos +1 para iniciar na pagina 1
	]);



}



$page = new PageAdmin();
//criamos um array passamos uma chave chamada users com o valor da minha variavel users que é uma lista com um monte de array dentro que e a nossa lista de usuarios
$page->setTpl("users", array(
	"users"=>$pagination['data'],
	"search"=>$search,
	"pages"=>$pages
));

});

//rota de tela create

$app->get('/admin/users/create', function() {

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

$page = new PageAdmin();
$page->setTpl("users-create");
});

//iremos colocar antes do iduser get porque o slimframework ira ver que esse tem mais o /delete, entao ele nunca ira executar o iduser/delete se ele ficar abaixo do iduser pois ira entender que e a memsa coisa
$app->get("/admin/users/:iduser/delete", function($iduser) {

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

	$user = new User();
	//iremos trazer tudo do banco para alterar
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	

	exit;
	});




//rota de tela update
				// estamos entendendo que a sessao do via get /users/  :iduser estamos solicitando os dados de um usuario especifico para visualizar
$app->get('/admin/users/:iduser', function($iduser) { //iremos receber na funcao o $iduser 

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

	//iremos carregar o usuario

$user = new User();

//convertemos para numerico para ter certeza que e numero

$user->get((int)$iduser);


$page = new PageAdmin();     //iremos passar um arrray user no singular
$page->setTpl("users-update", array(
	//passamos os nossos valores para a chave user
"user"=>$user->getValues()


));


});

$app->post("/admin/users/create", function(){

//verificando se o usuario esta logado com um metodo estatico
User::verifyLogin();

$user = new User();

//se ele foi definido falamos que o valor dele e um se nao for definido o valor sera 0
$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;


//invovamos a funcao setdata e criar automaticamente as variaveis para o DAO
$user->setData($_POST);

//iremos chamar o metodo save
//a ideia do save e executar um insert dentro do banco
$user->save();
//iremos redirecionar de volta para os users quem foi cadastrado
header("Location: /admin/users");

exit;

});

//salvarmos a edicao
$app->post("/admin/users/:iduser", function($iduser) {

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

	//iremos carregar os dados atuais
	$user = new User();

//se ele foi definido falamos que o valor dele e um se nao for definido o valor sera 0
$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	//iremos trazer tudo do banco para alterar
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();

	header("Location: /admin/users");
	exit;
	});


?>