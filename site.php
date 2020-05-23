<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;


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

										//se foi definida a pagina se esta passando na url, fazemos um cast para ver se e um numero que esta sendo passado do getpage, se nao e a pagina 1 mesmo
											$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

									//iremos recuperar o id da funcao com get
							
									$category = new Category();
									//fazemos um cast para int para ter certeza que isso é um numero 
									$category->get((int)$idcategory);
							
										//funcao para paginacao dos products 
										//iremos passar fora porque tem muitas informacoes
																			//passamos como parametro o page da verificacao	
									$pagination = $category->getProductsPage($page);
									
									//crimos um array vazio para pages do loop
									$pages = [];
									//criamos um for ele ira acontecer do numero 1 ate quando for menor ou igual o meu total de paginas, que esta no meu pagination e la eu tenho o total de paginas pages    que o meu 
									for($i=1; $i <= $pagination['pages']; $i++){


										//irei fazer um array push pois iremos adicionar mais um item ao nosso array page 
										array_push($pages, [
											//passamos no link o valor a rota do categories, barra o id da minha categoria que esta carregado, e iremos concatenar com o interrogacao page e passamos o $i que e a variavel de incremento, quando passamos o interrogacao ele permite que voce mande variaveis de query string      
											//entao de fato antes do interrogacao temos o nosso caminho de fato a url
											'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
											'page'=>$i //o numero da pagina para mostrar ao usuario


										]);

									}
									//iremos usar a classe de paginas do site 
							
									$page = new Page();
									//iremos passar os dados dessa categoria
									$page->setTpl("category", [
										'category'=>$category->getValues(),
										//trazendo a lista dos nossos produtos, se eu não passar nada dentro do get ira ser true e ira trazer todos os produtos relacionados as categorias
												//os produtos estao em pagination e um array estao na chave data
										'products'=>$pagination["data"],
										'pages'=>$pages
							
									]);
							
								});


$app->get("/products/:desurl", function($desurl){


	$product = new Product();

	//fazemos um getfrom url pegar a url carregamos do proprio objeto 
	$product->getFromURL($desurl);

	$page = new Page();
//O proprio objeto pega os valores e passa para o nosso template

//o setTPL ira desenhar na tela os detalhes do produto, precisamos passar no layout os produtos e a categoria dos produtos
	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		//para trazer quais as categorias dos produtos
		'categories'=>$product->getCategories()


		

	]);

});


$app->get("/cart", function(){

	$cart = Cart::getFromSession();

$page = new Page();

$page->setTpl("cart", [ //passo as informacoes do meu carrinho

	'cart'=>$cart->getValues(),
	'products'=>$cart->getProducts(),
	'error'=>Cart::getMsgError()

]);

});

						
$app->get("/cart/:idproduct/add", function($idproduct) {


		$product = new Product();
		$product->get((int)$idproduct);

		//recuperamos o carrinho
		$cart = Cart::getFromSession();

			//se for definido informado o get do   qtd, entao a minha variavel qtd sera o cast do int do campo qtd, se nao e o mesmo, ira adicionar a quantidade de 1 
			$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;
//assim ele chama o metodo quantas vezes ele for necessario ser executado, se nao passar uma vez, pelo menos uma vez ele ira adicionar
			for ($i = 0; $i < $qtd; $i++){

				
//inserimos o metodo para adicionar ao carrinho
				$cart->addProduct($product);
			}

			header("Location: /cart");
			exit;

});



	//colocamos na rota minus para remover 1
	$app->get("/cart/:idproduct/minus", function($idproduct) {


	$product = new Product();
	$product->get((int)$idproduct);
					
	//recuperamos o carrinho
	$cart = Cart::getFromSession();
					
	//inserimos o metodo para adicionar ao carrinho
	$cart->removeProduct($product);

	header("Location: /cart");
	exit;	

});

//para remover todos

//colocamos na rota minus para remover 1
$app->get("/cart/:idproduct/remove", function($idproduct) {


$product = new Product();
$product->get((int)$idproduct);
					
//recuperamos o carrinho
$cart = Cart::getFromSession();
					
//inserimos o metodo para adicionar ao carrinho //passamos true para remover todos
$cart->removeProduct($product, true);


header("Location: /cart");
exit;

					
	});
		
	//iremos criar uma rota que ira receber a chamada do envio do formulario com o cep para calcular 
	$app->post("/cart/freight", function(){

		$cart = Cart::getFromSession();
	
		$cart->setFreight($_POST['zipcode']);
	
		header("Location: /cart");
		exit;
	
	});

?>
