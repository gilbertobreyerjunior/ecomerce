<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Product;
use \Hcode\Model\User;
use \Hcode\Model\Cart;





//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class Cart extends Model {
//criamos uma constante para a sessao do carrinho
    
    const SESSION = "Cart";
    //para usarmos uma constante usamos o ::

    //iremos criar um metodo static para precisar saber se ele precisa inserir um carrinho novo, se temos esse carrinho, ira pegar da sessao, se a sessao foi perdida talvez acabou o tempo mas ainda eu tenho o session id que é a identificacao dessa sessao  
public static function getFromSession(){


$cart = new Cart();

//Se essa sessao existir           e verificar se tem o id do carrinho e se for um inteiro e for maior que 0 esse id
if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) { //se for verdade significa que o meu carrinho ja foi inserido no banco e significa que ele esta na sessao
//iremos carregar o meu carrinho
//passamos um cast dizendo que tem que ser int o id da minha sessao idcart
$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);

    }else //se por acaso ele ainda nao existir
    {
                //iremos tentar recuperar esse carrinho pelo sessionid
                $cart->getFromSessionID();

            //iremos ver se conseguiu carregar o meu carrinho, fazemos um cast esse id tem que ser int e maior que 0
           //se isso aqui nao for maior que 0 significa que nao conseguiu
            if (!(int)$cart->getidcart() > 0) {

                    $data =  [
                        'dessessionid'=>session_id()

                    ];

                    //verificamos o login se ele for verdadeiro quer dizer que esta logado
                        if (User::checkLogin(false)) {

                    //iremos ver se tem um usuario logado na sessao
                    $user = User::getFromSession();


                    //se for verdade traz o usuario
                            $data['iduser'] = $user->getiduser();

                        }

                        $cart->setData($data);
                        $cart->save();
                        $cart->setToSession();

            }
        

    }
    return $cart;


}



public function setToSession() {

$_SESSION[Cart::SESSION] = $this->getValues();


}



// metodo para tentar recuperar o carrinho pelo sessionid
public function getFromSessionID()
{

    $sql = new Sql();
//se isso aqui carregar os dados, se isso aqui retornar algum registro
    $results = $sql->select("SELECT * FROM tb_carts WHERE dessessionid = :dessessionid", [
        ':dessessionid'=>session_id() //sera setada a data


    ]);
           //fazemos um if se o count de results for maior que 0 
if (count($results) > 0){ //se for maior que 0 faz o setdata
    //trazer a partir da primeira linha os dados retornados
            $this->setData($results[0]);
    
    }

}



public function get(int $idcart){


    $sql = new Sql();
    $results = $sql->select("SELECT * FROM tb_carts WHERE idcart = :idcart", [
        ':idcart'=>$idcart


    ]);
//fazemos um if se o count de results for maior que 0 
if (count($results) > 0){ //se for maior que 0 faz o setdata
//trazer a partir da primeira linha os dados retornados
        $this->setData($results[0]);

}

}





//iremos criar o metodo save do carrinho

public function save(){


    

    $sql = new Sql();

    $results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight, :nrdays)", [

        ':idcart'=>$this->getidcart(),
        ':dessessionid'=>$this->getdessessionid(),
        ':iduser'=>$this->getiduser(),
        ':deszipcode'=>$this->getdeszipcode(),
        ':vlfreight'=>$this->getvlfreight(),
        ':nrdays'=>$this->getnrdays()
    ]);

//trazer a primeira linha

    $this->setData($results[0]);

}










         }



   

















?>