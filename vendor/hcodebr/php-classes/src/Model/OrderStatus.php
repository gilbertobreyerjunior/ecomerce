<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;



// classe ira trazer so os status dos pedidos
class OrderStatus extends Model {

	const EM_ABERTO = 1;
	const AGUARDANDO_PAGAMENTO = 2;
	const PAGO = 3;
	const ENTREGUE = 4;
//ira listar os pedidos ordenando pelos status
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_ordersstatus ORDER BY desstatus");

	}

}








?>