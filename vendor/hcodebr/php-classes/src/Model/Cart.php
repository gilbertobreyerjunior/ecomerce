<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;
use \Hcode\Model\Cart;





//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class Cart extends Model {
//criamos uma constante para a sessao do carrinho
    
    const SESSION = "Cart";

    //criamos uma constante para mensagem erro de carrinho
    const SESSION_ERROR = "CartError";




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


//metodo para adicionar ao carrinho

public function addProduct(Product $product){

    $sql = new Sql();

    $sql->query("INSERT INTO tb_cartsproducts (idcart, idproduct) VALUES(:idcart, :idproduct)", [
        ':idcart'=>$this->getidcart(),
        ':idproduct'=>$product->getidproduct()

    ]);

//atualizando o total
    $this->getCalculateTotal();

}


//metodo para remover o produto o carinho
                                                //adicionamos como parametro o $all false porque iremos deixar padrão de o cliente não remover completamente todos os mesmos produtos de um carrinho, e o normal e diminuir a quantidade de produtos
public function removeProduct(Product $product, $all = false){


    $sql = new Sql();

//se all = todos deste produto for igual a true entao faça

if ($all)
 {
                                                //usamos a funcao do Sql NOW para pegar a data e hora do momento,
                                                //o que quer dizer todos o produtos com esse id e neste carrinho serao removidos neste momento
    //aqui ira remover todos
      $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL",[
        
        ':idcart'=>$this->getidcart(),
        ':idproduct'=>$product->getidproduct()
    
    ]);

    } else { //se não
//aqui ira remover 1 produto
        $sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL LIMIT 1",[
        
            ':idcart'=>$this->getidcart(),
            ':idproduct'=>$product->getidproduct()
        ]);

    }
//atualizando o total
$this->getCalculateTotal();

}

//quero pegar todos os produtos que estao dentro desse carrinho

public function getProducts()
{

    $sql = new Sql();
                                                                                            //caso tenha mais de um produto com o mesmo tipo soma com o COUNT ele ira pegar no total, e também ira somar o valor total com o SUM
    $rows = $sql->select("                                                                   
    SELECT b.idproduct, b.desproduct , b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal
    FROM tb_cartsproducts a
    INNER JOIN tb_products b ON a.idproduct = b.idproduct
    WHERE a.idcart = :idcart AND a.dtremoved IS NULL
    GROUP BY b.idproduct, b.desproduct, b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl
    ORDER BY b.desproduct
    ", [

        ':idcart'=>$this->getidcart()

    ]);

        return Product::checkList($rows);


}


// o metodo da soma de todos os itens de cada atributo  dos produtos que estao no carrinho
public function getProductsTotals(){


    $sql = new Sql();
                                //ira somar, valor do produto, largura, altura, comprimento, peso,
    $results = $sql->select("
    SELECT SUM(vlprice) AS vlprice, SUM(vlwidth) AS vlwidth, SUM(vlheight) AS vlheight, SUM(vllength) AS vllength, SUM(vlweight) AS vlweight, COUNT(*) AS nrqtd
    FROM tb_products a
    INNER JOIN tb_cartsproducts b ON a.idproduct = b.idproduct
    WHERE b.idcart = :idcart AND dtremoved IS NULL;
", [
    //traz o id cart
    ':idcart'=>$this->getidcart()
]);

//iremos fazer um if para ver o que esta retornando se o count results for maior que 0 traz alguma coisa 
if (count($results) > 0) {

return $results[0]; //força para trazer na posicao 0

}else {

    return []; //se nao traz um array vazio
}

}





//metodo para definir o frete do nosso carrinho
public function setFreight($nrzipcode)
{

//iremos certificar que tem so os numero tirando os traços, nesse caso iremos trocar o traço caso ele exista
    $nrzipcode = str_replace('-', '',$nrzipcode);





    //pegar as informacoes totais do meu carrinho
        $totals = $this->getProductsTotals();

        //iremos verificar se tem algum produto dentro do carrinho
    if ($totals['nrqtd'] > 0)
    {
        //fazemos uma regra se for menor que 2, entao vou falar que e 2 
        if ($totals['vlheight'] < 2) $totals['vlheight'] = 2;
        if ($totals['vllength'] < 16) $totals['vllength'] = 16;

        //http_build_query — Gera a string de consulta (query) em formato URL
        $qs = http_build_query([ //ela espera um array, entao cada variavel que tivermos iremos colocar ai dentro 
            //passamos os dados que iremos precisar passar da webservice
            'nCdEmpresa'=>'',
            'sDsSenha'=>'',
            'nCdServico'=>'40010', //o codigo do servico
            'sCepOrigem'=>'95650000', //cep de origem
            'sCepDestino'=>$nrzipcode, //cep destino que vem pelo nosso atrib nrzipcode
            'nVlPeso'=>$totals['vlweight'], //peso
            'nCdFormato'=>'1', //formato da encomenda, colocamos fixo que e formato caixa/pacote conforme a documentacao
            'nVlComprimento'=>$totals['vllength'], //comprimento
            'nVlAltura'=>$totals['vlheight'], //altura
            'nVlLargura'=>$totals['vlwidth'], //largura
            'nVlDiametro'=>'0', //colocamos 0 pois nao se aplica
            'sCdMaoPropria'=>'S',
            'nVlValorDeclarado'=>$totals['vlprice'], //valor total do meu carrinho
            'sCdAvisoRecebimento'=>'S'
        ]);

//passando as informacoes para o webservice
        //iremos usar uma funcao para ler xml que e a funcao simplexmll, pois o webservice ele retorna em xml a resposta
    //passamos o caminho desse arquivo a url, podemos passar tanto o caminho de um endereço fisico quando uma url, passa o / e a funcao que iremos utilizar apos concatenando com as variaveis que queremos passar
    $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?".$qs);
           //iremos salvar todas essas informacoes no carrinho de compras


            //o result sera um xml
            $result = $xml->Servicos->cServico;

                //caso retorne alguma mensagem de erro no frete o if significa se a mensagem retornar diferente de vazio
            if ($result->MsgErro != '') {

                //entao definimos a mensagem de erro
                Cart::setMsgError($result->MsgErro);

            }// nao tiver mensagem de erro ira limpar
            else {

                Cart::clearMsgError();
            }

           $this->setnrdays($result->PrazoEntrega); //iremos salvar o prazo entrega
           $this->setvlfreight(Cart::formatValueToDecimal($result->Valor)); // iremos salvar o valor
           $this->setdeszipcode($nrzipcode); //iremos salvar o cep


           //iremos salvar as informacoes no banco
           $this->save();
           return $result;

    }else {//se nao iremos zerar as informacoes   



    }


    


}


    //iremos criar um metodo para formatar o valor do frete
       
    public static function formatValueToDecimal($value):float
    {
            //trocando o primeiro ponto por nada
                $value = str_replace('.', '', $value);
                //trocando a virgula por ponto
                return str_replace(',', '.', $value);
        
    }




    //metodo para sessao setar sessao de mensagens

public static function setMsgError($msg){



    $_SESSION[Cart::SESSION_ERROR] = $msg;
}

//metodo par pegar o erro
public static function getMsgError()
{



        //iremos validar se isso esta definido iremos pegar a mensagem que esta na sessao 
    $msg =  (isset($_SESSION[Cart::SESSION_ERROR])) ? $_session[Cart::SESSION_ERROR] : "";

    //nos limpamos a mensagem da sessao 
    Cart::clearMsgError();

    return $msg;

}


    // metodo para limpar a sessao  as informacoes


    public static function clearMsgError()
    {

            $_SESSION[Cart::SESSION_ERROR] = NULL;



    }


//metodo para atualizar o frete automaticamente quando for add mais de um product


public function updateFreight(){



        if ($this->getdeszipcode() != '') {


            $this->setFreight($this->getdeszipcode());

        }
}




//metodo para trazer o total com frete e produtos o total que subtotal mais o frete

//esse metodo tera a insteligencia de ver  qual e o total somar tanto o subtotal quanto o total colocar essa informacao em nosso objeto, e fazer o que o getvalues ja sabe fazer 
public function getValues(){


    $this->getCalculateTotal();


    return parent::getValues();



}

//no metodo a seguir iremos precisar pegar as informacoes os valores totais do nosso carrinho 

public function getCalculateTotal(){

//tornando o carrinho dinamico atualizando o frete
$this->updateFreight();

//trazer os valores totais do carrinho
$totals = $this->getProductsTotals();

$this->setvlsubtotal($totals['vlprice']);  //soma dos meus produtos que estao dentro do carrinho
  $this->setvltotal($totals['vlprice'] + $this->getvlfreight());     //e a soma dos produtos que estao em meu carrinho mais o valor do frete

}


         }



   

















?>