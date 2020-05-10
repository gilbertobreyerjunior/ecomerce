<?php

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

//iremos listar os nossos produtos
$app->get("/admin/products", function(){

User::verifyLogin();

$products = Product::listAll();

$page = new PageAdmin();
//passamos um array de produtos
$page->setTpl("products", [
"products"=>$products



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
$app->get("/admin/products/create", function(){

    User::verifyLogin();
    
   
//iremos criar um novo produto 
$product = new Product();

$product->setData($_POST);

$product->save();

header("Location: /admin/products");

    exit;



});






?>