<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim(); //criando a instancia do slim

$app->config('debug', true); //deixando no modo debug

$app->get('/', function() {
    
	$sql = new Hcode\DB\Sql();

	$results = $sql->select("SELECT * FROM  tb_users");


	echo json_encode($results);

});

$app->run(); // mandando executar o projeto

 ?>