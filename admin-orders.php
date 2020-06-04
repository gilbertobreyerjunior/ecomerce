<?php


use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;


//editar status pedido
$app->get("/admin/orders/:idorder/status", function($idorder){


    User::verifyLogin();
    $order = new Order();

    $order->get((int)$idorder);

    $page = new PageAdmin();


    $page->setTpl("order-status", [
        'order'=>$order->getValues(), //traz os values do pedido
        'status'=>OrderStatus::listAll(), //e todos os status possiveis
        'msgSuccess'=>Order::getSuccess(), //passamos message Success
        'msgError'=>Order::getError() //passamos o message Error

    ]);


});

//rota post para atulizar o status

$app->post("/admin/orders/:idorder/status", function($idorder){


        User::verifyLogin();

//se nao for mandado
//se nao existir, e nao for maior que 0
    if (!isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0) {

//dispara um erro
        Order::setError("Informe o status atual");

        header("Location: /admin/orders/".$idorder."/status");

      exit;
    }

        $order = new Order();

     

        $order->get((int)$idorder);
//setamos o novo id status  com o que eu receber do meu post
        $order->setidstatus((int)$_POST['idstatus']);


        $order->save();

        Order::setSuccess("Status atualizado");

        header("Location: /admin/orders/".$idorder."/status");

        exit;
});






//rota para exclui pedido

$app->get("/admin/orders/:idorder/delete", function($idorder){


    User::verifyLogin();

    $order = new Order();

    $order->get((int)$idorder);

    $order->delete();

    header("Location: /admin/orders");
    exit;


});


//rota para exibir detalhes do pedido
$app->get("/admin/orders/:idorder", function($idorder){


    User::verifyLogin();
    $order = new Order();
    $order->get((int)$idorder);
    //precisamos pegar o carrinho do pedido que queremos os detalhes
    $cart = $order->getCart();
    $page = new PageAdmin();
    $page->setTpl("order", [
        'order'=>$order->getValues(),
        'cart'=>$cart->getValues(),
        'products'=>$cart->getProducts()
    ]);



});


//rota para listar os pedidos
$app->get("/admin/orders", function(){

    User::verifyLogin();

    $page = new PageAdmin();

//passamos um array com a lista dos pedidos, passamos a funcao listall como value para trazer os pedidos
    $page->setTpl("orders", [
       "orders"=>Order::listAll()


    ]);


});






?>