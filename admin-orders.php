<?php


use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Order;


//rota para exclui pedido

$app->get("/admin/orders/:idorder/delete", function($idorder){


    User::verifyLogin();

    $order = new Order();

    $order->get((int)$idorder);

    $order->delete();

    header("Location: /admin/orders");
    exit;


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