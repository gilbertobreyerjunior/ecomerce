<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Page;

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