<?php

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;


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


	//criamos a rota de checkout, essa rota só pode ser acessada se o login foi realizado

	$app->get("/checkout", function(){


			//iremos fazer a validacao do nosso login
			//verificando se o usuario esta logado com um metodo estatico
			User::verifyLogin(false);



			$address = new Address();

			//iremos trazer o carrinho da sessao
			$cart = Cart::getFromSession();

		//verificamos se o get nao existe e aproveitar o cep que ja esta no carrinho
				if (!isset($_GET['zipcode'])) {

					$_GET['zipcode'] = $cart->getdeszipcode();
			}


			//iremos detectar se o cep foi mandado
			if (isset($_GET['zipcode'])) {


				//se foi mandado iremos carregar o objeto endereço
					//iremos forçar para carregar esse endereço com os campos certos
					$address->loadFromCEP($_GET['zipcode']);
				 //setamos o cep
				 $cart->setdeszipcode($_GET['zipcode']);
				//salvamos
				$cart->save();
					//forçamos para atualizar o total do frete
					$cart->getCalculateTotal();

			}
//fazemos as validações se existe determinado endereco se esta vazio
				if (!$address->getdesaddress()) $address->setdesaddress('');
				if (!$address->getdesnumber()) $address->setdesnumber('');
				if (!$address->getdescomplement()) $address->setdescomplement('');
				if (!$address->getdesdistrict()) $address->setdesdistrict('');
				if (!$address->getdescity()) $address->setdescity('');
				if (!$address->getdesstate()) $address->setdesstate('');
				if (!$address->getdescountry()) $address->setdescountry('');
				if (!$address->getdeszipcode()) $address->setdeszipcode('');
			
				$page = new Page();

				$page->setTpl("checkout", [
				//pegando os valores do carrinho e do endereço
				'cart'=>$cart->getValues(),
				'address'=>$address->getValues(),
				'products'=>$cart->getProducts(),
				'error'=>Address::getMsgError() //trazemos o error a msg para o template


			]);



	});


//rota post de checkout iremos salvar o endereço
$app->post("/checkout", function(){

	User::verifyLogin(false);


// fazemos as validações
if (!isset($_POST['zipcode']) || $_POST['zipcode'] === '') {
	Address::setMsgError("Informe o CEP.");
	header('Location: /checkout');
	exit;
}

if (!isset($_POST['desaddress']) || $_POST['desaddress'] === '') {
	Address::setMsgError("Informe o endereço.");
	header('Location: /checkout');
	exit;
}

if (!isset($_POST['desdistrict']) || $_POST['desdistrict'] === '') {
	Address::setMsgError("Informe o bairro.");
	header('Location: /checkout');
	exit;
}

if (!isset($_POST['descity']) || $_POST['descity'] === '') {
	Address::setMsgError("Informe a cidade.");
	header('Location: /checkout');
	exit;
}

if (!isset($_POST['desstate']) || $_POST['desstate'] === '') {
	Address::setMsgError("Informe o estado.");
	header('Location: /checkout');
	exit;
}

if (!isset($_POST['descountry']) || $_POST['descountry'] === '') {
	Address::setMsgError("Informe o país.");
	header('Location: /checkout');
	exit;
}
		//iremos pegar o usuario da sessao
		$user = User::getFromSession();

		$address = new Address();
//iremos receber o post do formulario 
$_POST['deszipcode'] = $_POST['zipcode'];
$_POST['idperson'] = $user->getidperson();

$address->setData($_POST);
// var_dump($address);
$address->save();

$cart = Cart::getFromSession();

 $cart->getCalculateTotal();


$order = new Order();
//iremos passar os dados nesse array
$order->setData([

	'idcart'=>$cart->getidcart(), //id do carrinho
	'idaddress'=>$address->getidaddress(), //o id do endereço
	'iduser'=>$user->getiduser(), //id o usuario
	'idstatus'=>OrderStatus::EM_ABERTO, // qual o status do pedido
	//'vltotal'=>$totals['vlprice'] + $cart->getvlfreight() // e o valor do frete
	'vltotal'=>$cart->getvltotal()

]);
//salvamos o pedido
$order->save();

//redirecionamos para a nossa ordem de compra por isso concatenamos com o id da nossa order
header("Location: /order/".$order->getidorder());
exit;



});

	//criamos a rota de checkout, essa rota só pode ser acessada se o login foi realizado

	$app->get("/login", function(){


	
		$page = new Page();
//agora passamos o erro para o template
$page->setTpl("login", [
	'error'=>User::getError(),
	'errorRegister'=>User::getErrorRegister(),//iremos mostrar o erro na tela
//iremos passar para os valores também para a tela do login, para quando eu inserir os dados de cadastro e quem sabe deixar um campo vazio nao perder esses dados
'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=>'', 'email'=>'', 'phone'=>'']
]);

});




$app->post("/login", function(){

try { //tenta nesse cara aqui

//passamos o login do usuario e a senha dele
User::login($_POST['login'], $_POST['password']);

} catch(Exception $e) { //capturar o erro



	User::setError($e->getMessage());
}
//redirecionamos o usuario para a proxima tela

header("Location: /checkout");
exit;

});

//rota para logout

$app->get("/logout", function(){


	User::logout();

	header("Location: /login");
	exit;


});


//criando a rota register
$app->post("/register", function(){
//Todos os dados que eu receber no meu post vou colocar em uma sessao 
	$_SESSION['registerValues'] = $_POST;

	if (!isset($_POST['name']) || $_POST['name'] == '') {

		User::setErrorRegister("Preencha o seu nome.");
		header("Location: /login");
		exit;

	}

	if (!isset($_POST['email']) || $_POST['email'] == '') {

		User::setErrorRegister("Preencha o seu e-mail.");
		header("Location: /login");
		exit;

	}
//o post não foi definido,  ou for igual a vazio mandamos uma mensagem de erro  com o setErrorRegister, apos isso redireciona para a pagina Login, e fecha o formulario

	if (!isset($_POST['password']) || $_POST['password'] == '') {

		User::setErrorRegister("Preencha a senha.");
		header("Location: /login");
		exit;

	}
//se isso for true, se esse usuario ja contem
	if (User::checkLoginExist($_POST['email']) === true) {
//dispara um erro para o usuario sobre o email que ja contem
		User::setErrorRegister("Este endereço de e-mail já está sendo usado por outro usuário.");
		header("Location: /login");
		exit;

	}

	$user = new User();

	$user->setData([
		'inadmin'=>0,
		'deslogin'=>$_POST['email'], //o login vem do post do email
		'desperson'=>$_POST['name'], //o nome da pessoa
		'desemail'=>$_POST['email'], //repete o email
		'despassword'=>$_POST['password'], //coloca o campo senha
		'nrphone'=>$_POST['phone']
	]);
 //assim salvamos o usuario
	$user->save();
 //autenticamos o usuario quando esta logado
	User::login($_POST['email'], $_POST['password']);
//e mandamos para a tela do checkout
	header('Location: /checkout');
	exit;

});







//recuperação de senha

$app->get("/forgot", function() {

	$page = new Page();

	$page->setTpl("forgot");	

});

$app->post("/forgot", function(){

	$user = User::getForgot($_POST["email"], false);

	header("Location: /forgot/sent");
	exit;

});

$app->get("/forgot/sent", function(){

	$page = new Page();

	$page->setTpl("forgot-sent");	

});


$app->get("/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);	

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = User::getPasswordHash($_POST["password"]);

	$user->setPassword($password);

	$page = new Page();

	$page->setTpl("forgot-reset-success");

});



//rotas para acessar a conta

$app->get("/profile", function(){
		//forçamos o login para false que nao e admin
	User::verifyLogin(false);
//recuperamos o usuario na sessao
	$user = User::getFromSession();

	$page = new Page();
//mandamos um array dentro do nosso template  com algumas informacoes
	$page->setTpl("profile", [
		'user'=>$user->getValues(), //passamos o usuario para o template
		'profileMsg'=>User::getSuccess(),  //passamos a mensagem que conseguiu alterar
		'profileError'=>User::getError()//passamos o erro para o template
	]);

});
//criamos a rota post para salvar a edicao de um usuario
$app->post("/profile", function(){
	//forçamos o login para false que nao e admin
	User::verifyLogin(false);
//fazemos uma validacao caso o POST desperson se ele nao for definido ou for igual a vazio
	if (!isset($_POST['desperson']) || $_POST['desperson'] === '') {
		User::setError("Preencha o seu nome."); //dispara um erro
		header('Location: /profile'); //redireciona para a pagina dos dados
		exit;
	}
	//outro campo obrigatorio e o email
	if (!isset($_POST['desemail']) || $_POST['desemail'] === '') {
		User::setError("Preencha o seu e-mail.");
		header('Location: /profile');
		exit;
	}

	$user = User::getFromSession();
//fazemos uma verificacao se tem um outro usuario utilizando este mesmo email login, precisamos verificar caso ele tenha alterado o email
		//se o email foi diferente do que temos atualmente, entendemos que ele alterou
	if ($_POST['desemail'] !== $user->getdesemail()) {
//se ele alterou o email precisamos verificar se esse email esta sendo usado usamos o CheckLoginExist
		if (User::checkLoginExist($_POST['desemail']) === true) {
//se for true dispara a mensagem que o email já está cadastrado
			User::setError("Este endereço de e-mail já está cadastrado.");
			header('Location: /profile');
			exit;

		}

	}

	$_POST['inadmin'] = $user->getinadmin(); //sobreescrevemos o indadmin do post que esta vindo atualmente com o que extatamente esta no objeto que esta vindo da sessao
	$_POST['despassword'] = $user->getdespassword(); //fazemos a mesma coisa com a senha mantendo a mesma senha
	$_POST['deslogin'] = $_POST['desemail'];

	$user->setData($_POST);
//se ele chegou ate o save quer dizer que foi alterado
	$user->update();
//mensagem que conseguiu alterar os dados
	User::setSuccess("Dados alterados com sucesso!");

	header('Location: /profile');
	exit;

});


//apos fazer o checkout criamos a rota order para os pedidos, para carregar o pedido e mostrar para a pessoa passamos o idorder
$app->get("/order/:idorder", function($idorder){

	User::verifyLogin(false);


	$order = new Order();

	$order->get((int)$idorder);

	$page = new Page();

	$page->setTpl("payment", [

			'order'=>$order->getValues()

	]);



});


$app->get("/boleto/:idorder", function($idorder){




	User::verifyLogin(false);
//carregamos o nosso pedido
	$order = new Order();
//fazemos o cast para inteiro
	$order->get((int)$idorder);

	// DADOS DO BOLETO PARA O SEU CLIENTE
	$dias_de_prazo_para_pagamento = 10;
	$taxa_boleto = 5.00;
	$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
			//valor total do pedido
	$valor_cobrado = formatPrice($order->getvltotal()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
	$valor_cobrado = str_replace(".", "", $valor_cobrado);
	$valor_cobrado = str_replace(",", ".",$valor_cobrado);
	$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
										//informamos qual o pedido
	$dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!
	$dadosboleto["numero_documento"] = $order->getidorder();	// Num do pedido ou nosso numero
	$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
	$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE
	$dadosboleto["sacado"] = $order->getdesperson(); //o get do nome da pessoa
	$dadosboleto["endereco1"] = $order->getdesaddress() . " " . $order->getdesdistrict(); //endereco
	$dadosboleto["endereco2"] = $order->getdescity() . " - " . $order->getdesstate() . " - " . $order->getdescountry() . " -  CEP: " . $order->getdeszipcode(); 

	// INFORMACOES PARA O CLIENTE
	$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Tech";
	$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
	$dadosboleto["demonstrativo3"] = "";
	$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
	$dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
	$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@lojatech.com.br";
	$dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Tech Informática";

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] = "";
	$dadosboleto["valor_unitario"] = "";
	$dadosboleto["aceite"] = "";		
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


	// DADOS DA SUA CONTA - ITAÚ
	$dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "48781";	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

	// DADOS PERSONALIZADOS - ITAÚ
	$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

	// SEUS DADOS
	$dadosboleto["identificacao"] = "Loja Tech";
	$dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
	$dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
	$dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
	$dadosboleto["cedente"] = "Loja Tech Informática";

	
	$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "resource" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;

	require_once($path . "funcoes_itau.php");
	require_once($path . "layout_itau.php");

});


//visualização para o usuario do que ele comprou
$app->get("/profile/orders", function(){

	//se está logada dizemos que nao precisa fazer a verificacao
		User::verifyLogin(false);


			$user = User::getFromSession();


			$page = new Page();
			//definindo o template
			$page->setTpl("profile-orders",[
			//passando um array das informacoes desse template
		
				'orders'=>$user->getOrders()
		]);
});



//detalhes do pedido
$app->get("/profile/orders/:idorder", function($idorder){

User::verifyLogin(false);

$order = new Order();

$order->get((int)$idorder);


$cart = new Cart();


$cart->get((int)$order->getidcart());


$cart->getCalculateTotal();

$page = new Page();

$page->setTpl("profile-orders-detail", [

		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts()

]);


});



?>
