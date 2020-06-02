<?php 

session_start();

require_once("vendor/autoload.php");
//Usamos os seguintes namespaces
use \Slim\Slim;
//Iremos fazer o use da classe page que estamos usando agora
// use \Hcode\Page;

// use \Hcode\PageAdmin;

// use \Hcode\Model\User;

// use \Hcode\Model\Category;


$app = new Slim(); //criando a instancia do slim estamos criando uma nova aplicacao


$app->config('debug', true); //deixando no modo debug

require_once("site.php");

require_once("functions.php");

require_once("admin.php");

require_once("admin-users.php");

require_once("admin-categories.php");

require_once("admin-products.php");

require_once("admin-orders.php");



$app->run(); // mandando executar o projeto

 ?>