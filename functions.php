<?php

use \Hcode\Model\User;
use \Hcode\Model\Cart;

//essa funcao iremos usar dentro do template
function formatPrice($vlprice){


    if (!$vlprice > 0) $vlprice = 0;

//ira retornar o valor formatado do atrib $vlprice, para garantir que e um valor mesmo colocamos o float
                                //o primeiro separador das casas decimais e a virgula, o segundo case de milhar e  . 
return number_format($vlprice, 2, ",", ".");
    
}
    
function formatDate($date)
{

	return date('d/m/Y', strtotime($date));

}
//verificar o login
function checkLogin($inadmin = true)
{

	return User::checkLogin($inadmin);

}
//pegamos o nome do usuario
function getUserName()
{

	$user = User::getFromSession(); //pegamos o usuario da sessao que esta logado

	return $user->getdesperson(); //pegamos o nome do usuario

}

function getCartNrQtd()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return $totals['nrqtd'];

}

function getCartVlSubTotal()
{

	$cart = Cart::getFromSession();

	$totals = $cart->getProductsTotals();

	return formatPrice($totals['vlprice']);

}
    


?>