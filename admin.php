<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;


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


?>