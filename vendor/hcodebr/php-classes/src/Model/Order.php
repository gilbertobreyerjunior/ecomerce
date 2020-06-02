<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model {

	const SUCCESS = "Order-Success";
	const ERROR = "Order-Error";

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_orders_save(:idorder, :idcart, :iduser, :idstatus, :idaddress, :vltotal)", [
			':idorder'=>$this->getidorder(),
			':idcart'=>$this->getidcart(),
			':iduser'=>$this->getiduser(),
			':idstatus'=>$this->getidstatus(),
			':idaddress'=>$this->getidaddress(),
			':vltotal'=>$this->getvltotal()
		]);
//se o results foi maior que 0
		if (count($results) > 0) {
			$this->setData($results[0]); //traz o nosso resultado
		}

	}
//metodo para recuperar os pedidos que foram feitos
	public function get($idorder)
	{

		$sql = new Sql();
//recebemos o resuolts do select dos dados
		$results = $sql->select("
			SELECT * 
			FROM tb_orders a 
			INNER JOIN tb_ordersstatus b USING(idstatus) 
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :idorder
		", [
			':idorder'=>$idorder
		]);

		if (count($results) > 0) { //se o results foi maior que 0
			$this->setData($results[0]); //traz o nosso resultado
		}

	}



// metodo traz todos os pedidos no banco de dados lista os pedidos


public static function listAll(){

	$sql = new Sql();

	return $sql->select("
	
	SELECT *
	FROM tb_orders a
	INNER JOIN tb_ordersstatus b USING(idstatus)
	INNER JOIN tb_carts c USING(idcart)
	INNER JOIN tb_users d ON d.iduser = a.iduser
	INNER JOIN tb_addresses e USING(idaddress)
	INNER JOIN tb_persons f ON f.idperson = d.idperson
	ORDER BY a.dtregister DESC
	
	");


}


//metodo delete para deletar pedidos

public function delete()
{


		$sql = new Sql();

		$sql->query("DELETE FROM tb_orders WHERE idorder = :idorder", [
			':idorder'=>$this->getidorder()


		]);


}








}








?>