<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;
use \Hcode\Page;

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = Category::getPageSearch($search, $page);

	} else {

		$pagination = Category::getPage($page);

	}

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++)
	{

		array_push($pages, [
			'href'=>'/admin/categories?'.http_build_query([
				'page'=>$x+1, //a pagina
				'search'=>$search //o search
			]),
			'text'=>$x+1 //e o texto do numero da pagina
		]);

	}

	$page = new PageAdmin();

	$page->setTpl("categories", [
		"categories"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	]);	


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


	$app->post("/admin/categories/:idcategory", function($idcategory) {


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





			

	

//iremos criar uma rota para acessar produtos categorias
$app->get("/admin/categories/:idcategory/products", function($idcategory){


		//verificando se o usuario esta logado com um metodo estatico	
		User::verifyLogin();

		//iremos recuperar o id da funcao com get

		$category = new Category();
		//fazemos um cast para int para ter certeza que isso é um numero 
		$category->get((int)$idcategory);

		//iremos usar a classe de paginas do site 

		$page = new PageAdmin();
		//iremos passar os dados dessa categoria
		$page->setTpl("categories-products", [
			'category'=>$category->getValues(),
			'productsRelated'=>$category->getProducts(), //traz os produtos que estão relacionados
			'productsNotRelated'=>$category->getProducts(false) //traz os produtos que não estão relcionados

		]);
 

});





//Para adicionar produtos

																			//primeiro recebemos o id da categoria, apos o id do produto
$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){


	//verificando se o usuario esta logado com um metodo estatico	
	User::verifyLogin();

	//iremos recuperar o id da funcao com get

	$category = new Category();
	//fazemos um cast para int para ter certeza que isso é um numero 
	$category->get((int)$idcategory);

	$product = new Product();


		//fazemos um cast para int para ter certeza que isso é um numero 
	$product->get((int)$idproduct);

//esse metodo ja ira receber uma classe instanciada do produto
	$category->addProduct($product);
//Ira redirecionar de volta para a lista da relação dos produtos com a categoria
	header("Location: /admin/categories/".$idcategory."/products");
exit;

});



//para remover
//primeiro recebemos o id da categoria, apos o id do produto
$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){


//verificando se o usuario esta logado com um metodo estatico	
User::verifyLogin();
																			
//iremos recuperar o id da funcao com get
																			
$category = new Category();
//fazemos um cast para int para ter certeza que isso é um numero 
$category->get((int)$idcategory);
																			
$product = new Product();
																			
																			
//fazemos um cast para int para ter certeza que isso é um numero 
$product->get((int)$idproduct);
																			
//esse metodo ja ira receber uma classe instanciada do produto
$category->removeProduct($product);
//Ira redirecionar de volta para a lista da relação dos produtos com a categoria
header("Location: /admin/categories/".$idcategory."/products");
exit;
																			
});






?>