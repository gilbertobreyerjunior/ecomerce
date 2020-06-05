<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

//iremos listar os nossos produtos
$app->get("/admin/products", function(){

	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if ($search != '') {

		$pagination = Product::getPageSearch($search, $page);

	} else {

		$pagination = Product::getPage($page);

	}

	$pages = [];

	for ($x = 0; $x < $pagination['pages']; $x++)
	{

		array_push($pages, [
			'href'=>'/admin/products?'.http_build_query([
				'page'=>$x+1, //o nosso page
				'search'=>$search // o search
			]),
			'text'=>$x+1 //o text do numero da pagina
		]);

	}

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [
		"products"=>$pagination['data'], 
		"search"=>$search,
		"pages"=>$pages
	]);

});

//iremos cadastrar os nossos produtos
$app->get("/admin/products/create", function(){

    User::verifyLogin();
    
   
    
    $page = new PageAdmin();
    //passamos um array de produtos
    $page->setTpl("products-create");
    


});

//iremos cadastrar os nossos produtos
$app->post("/admin/products/create", function(){

    User::verifyLogin();
    
   
//iremos criar um novo produto 
$product = new Product();

$product->setData($_POST);

$product->save();

header("Location: /admin/products");

    exit;



});


//iremos editar o nosso produto
$app->get("/admin/products/:idproduct", function($idproduct){

    User::verifyLogin();
    
   
//iremos criar um novo produto 
$product = new Product();

//iremos carregar  esse objeto para ter certeza que ele existe la no banco de dados  e estamos dizendo que ele tem que ter inteiro
$product->get((int)$idproduct);


$page = new PageAdmin();


//iremos passar os dados do produto para o template
$page->setTpl("products-update", [
    'product'=>$product->getValues()


]);



});

//iremos editar o nosso produto
$app->post("/admin/products/:idproduct", function($idproduct){

    User::verifyLogin();
    
   
//iremos criar um novo produto 
$product = new Product();

//iremos carregar  esse objeto para ter certeza que ele existe la no banco de dados  e estamos dizendo que ele tem que ter inteiro
$product->get((int)$idproduct);

//temos as informações que vem por post  o que é texto recebos pelo $_POST
$product->setData($_POST);
//e fazemos o save
$product->save();

//e iremos fazer o upload do arquivo //passamos o nome do nosso campo lá no input e o que e arquivo recebemos peo $_FILES
$product->setPhoto($_FILES["file"]);


//fazemos o redirect para a lista de produtos

header('Location: /admin/products');

exit;

});



//iremos excluir o nosso produto
$app->get("/admin/products/:idproduct/delete", function($idproduct){

    User::verifyLogin();
    
   
//iremos criar um novo produto 
$product = new Product();

//iremos carregar  esse objeto para ter certeza que ele existe la no banco de dados  e estamos dizendo que ele tem que ter inteiro
$product->get((int)$idproduct);

//chamo o metodo delete

$product->delete();

//apos um redirect

header('Location: /admin/products');
exit;

});


?>