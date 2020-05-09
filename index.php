<?php 

session_start();

require_once("vendor/autoload.php");
//Usamos os seguintes namespaces
use \Slim\Slim;
//Iremos fazer o use da classe page que estamos usando agora
use \Hcode\Page;

use \Hcode\PageAdmin;

use \Hcode\Model\User;

use \Hcode\Model\Category;


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
	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();


	//instanciamos a classe PageAdmin e entao ira chamar o construct e ira adicionar o header a tela

	$page = new PageAdmin();
//quando chamar o setTpl("index") passando o nome do template que o index ele ira adicionar o arquivo que tem o h1, apos isso ira chamar o destruct que tem o footer que sera incluido na pagina
	$page->setTpl("index");


});


//rota para acessar o login

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


//criamos uma rota post para enviar
$app->post('/admin/login', function() {
	//Iremos validar o login criamos uma nova classe que o nosso model do nosso Usuario, e iremos criar um metodo estatico chamado login ele ira receber o post do login do formulario login com o usuario e senha se não ocorrer erro podemos redirecionar para a nossa homepage da nossa administracao fazendo um header(Locacion: /admin )
	User::login($_POST["login"], $_POST["password"]);
//sera redicerionado para a pagina admin
	header("Location: /admin");
//E paramos a execução aqui
	exit;

	


});
//rota para logout
$app->get('/admin/logout', function() {

User::logout();

header("Location: /admin/login");

exit;

});

//Rota para listar os usuarios
$app->get('/admin/users', function() {

	//verificando se o usuario esta logado com um metodo estatico
User::verifyLogin();
//Users ira receber listAll de usuarios
$users = User::listAll();
$page = new PageAdmin();
//criamos um array passamos uma chave chamada users com o valor da minha variavel users que é uma lista com um monte de array dentro que e a nossa lista de usuarios
$page->setTpl("users", array(
	"users"=>$users
	
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

//iremos criar a rota do esqueceu a senha
$app->get("/admin/forgot", function()
{
//ela nao tera o header e o footer padrao do sistema
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]); //abrindo com o template forgot
		$page->setTpl("forgot");
});
//rota que sera mandada o nosso formulario de recuperacao de email
$app->post("/admin/forgot", function(){

//iremos criar um metodo que saiba receber todas as verificacoes,
	$user = User::getForgot($_POST["email"]);

//fazemos um redirect informando para a pessoa que o email foi mandado com sucesso
	header("Location: /admin/forgot/sent");
	exit;



});

$app->get("/admin/forgot/sent", function(){

//iremos renderizar o template do sent
//ela nao tera o header e o footer padrao do sistema
$page = new PageAdmin([
	"header"=>false,
	"footer"=>false

]); //abrindo com o template forgot
	$page->setTpl("forgot-sent");



});
//criamos a rota para resetar a senha

$app->get("/admin/forgot/reset", function(){

//iremos recuperar de qual usuario e o codigo
$user = User::validForgotDecrypt($_GET["code"]);

//iremos renderizar o template do sent
//ela nao tera o header e o footer padrao do sistema
$page = new PageAdmin([
	"header"=>false,
	"footer"=>false

]); //abrindo com o template forgot //passamos um array com o name desse usuario e o codigo que vem criptografado
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]          //pois ele ira precisar validar tudo isso na proxima pagina 

	));


});

//precisamos a rota post para o forgot-reset iremos recuperar por post

$app->post("/admin/forgot/reset", function(){
//iremos verificar novamente para impedir se houve alguma brecha de segurança nessa transição
$forgot = User::validForgotDecrypt($_POST["code"]);
//o metodo que ira salvar que ira falar para o banco de dados que essa recuperação, já foi usado, para não recuperar novamente, mesmo que esteja dentro dessa uma hora  
User::setForgotUsed($forgot["idrecovery"]);

//agora iremos carregar o objeto usuario
$user = new User();
$user->get((int)$forgot["iduser"]);

//iremos criptografar a senha
						//primeiro parametro qual a senha que queremos criptografar, segundo parametro o modo de codificacao, terceiro parametro e o cost quanto de processamento voce quer utilizar no servidor para gerar essa criptografia, quanto mais mais segura ira ficar a criptografia, se tiver mais mais processamento ele ira utilizar para fazer essa criptografia   para sua senha para ser criptografada
$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [
"cost"=>12

]);


//iremos chamar o metodo setPassword pois precisamos colocar o hash dessa senha, entao iremos informar a senha para o metodo mas ele ira gerar, ele ira salvar o hash no banco de dados 
      //passamos como parametro o $password que iremos colocar para salvar
$user->setPassword($password);


$page = new PageAdmin([
	"header"=>false,
	"footer"=>false

]); //abrindo com o template forgot //passamos um array com o name desse usuario e o codigo que vem criptografado
	$page->setTpl("forgot-reset-success");


});

$app->get("/admin/categories", function(){

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

//A classe category acessa o metodo estatico listAll
$categories = Category::listAll();

$page = new PageAdmin();
//abrindo o template categories
$page->setTpl("categories",[
				//iremos receber essa lista esse array
'categories'=>$categories
]);


});

$app->get("/admin/categories/create", function(){
	
	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

$page = new PageAdmin();
$page->setTpl("categories-create");

});


$app->post("/admin/categories/create", function(){

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

	//Instanciamos a classe Category
	$category = new Category();
	//	Iremos setar o nosso $_POST ira pegar os mesmos names que tem dentro do array global post e ira colocar em nosso objeto
	$category->setData($_POST);
	
//iremos salvar
$category->save();

header('Location: /admin/categories');

exit;

});

//criamos a rota de exclusao do categorias

$app->get("/admin/categories/:idcategory/delete", function($idcategory) {

	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();


$category = new Category();
//iremos carregar  esse objeto para ter certeza que ele existe la no banco de dados  e estamos dizendo que ele tem que ter inteiro
$category->get((int)$idcategory);

$category->delete();

header('Location: /admin/categories');
exit;


});

$app->get("/admin/categories/:idcategory", function($idcategory) {


	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

//instanciamos a nossa classe 
	$category = new Category();
//iremos fazer um cast para numerico da do atrib que esta vindo da url entao iremos converter para numerico
	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		//passamos para o nosso template como um array, para converter esse objeto para um array iremos usar o $category->getValues
		'category'=>$category->getValues()

	]);
	
	
	});


	$app->POST("/admin/categories/:idcategory", function($idcategory) {


	//verificando se o usuario esta logado com um metodo estatico
	User::verifyLogin();

		
		//instanciamos a nossa classe 
			$category = new Category();
		//iremos fazer um cast para numerico da do atrib que esta vindo da url entao iremos converter para numerico
			$category->get((int)$idcategory);
		

			//iremos carregar os dados atuais, e iremos colocar os novos dados que vou receber do formulario
			$category->setData($_POST);

			$category->save();


			header('Location: /admin/categories');
exit;
			});

$app->run(); // mandando executar o projeto

 ?>