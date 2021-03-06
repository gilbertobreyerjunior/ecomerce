<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;




//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class Product extends Model {

//essa funcao ira ler todos os dados da tabela
public static function listAll()
{

$sql = new Sql();
//ira retornar da tabela categories por ordem pelo descategory
return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

}


//iremos criar um metodo para criar um objeto chamar o getvalues e retornar esses objetos tratados
public static function checkList($list){ //fazendo uma checagem na lista de produtos

//percorre cada linha da minha lista
//passamos o atrib com & comercial para mnipular o mesmo atrib na memoria

                //como adicionamos o & comercial ao row ele foi alterado por conta do & comercial e tambem alterou dentro do array list
    foreach($list as &$row){

        $p = new Product();
        $p->setData($row);
        $row = $p->getValues(); //nesse momento passou pelo getvalues  que ira chamar e verificar se possui a foto ou nao


    }

    //agora iremos retornar o arraylist com os dados de cada produto já formatado
    return $list;
}



//criamos um metodo estatico para formatar o preço




//criamos o metodo save

public function save()
{
    $sql = new Sql();



    // var_dump( $this->getidproduct(), 
    // $this->getdesproduct(),
    // $this->getvlprice(),
    // $this->getvlwidth(),
    // $this->getvlheight(),
    // $this->getvllength(),
    // $this->getvlweigth(),
    // $this->getdesurl());



    
    //iremos chamar uma procedure, pois esssa procedure ira chamar uma pessoa primeiro e entao precisamos saber o id dessa pessoa para poder inserir na tabela de usuarios porque ele precisa do idpessoa vamos pegar o idusuario que retornou e fazer um select com os dados que estão lá no banco de dados agora, a data de cadastro, idusuario  iremos juntar tudo e trazer de volta para isso iremos precisar de uma procedure      

    $results = $sql->select(" CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
       ":idproduct" =>$this->getidproduct(),
        ":desproduct"=>$this->getdesproduct(),
        ":vlprice"=>$this->getvlprice(),
        ":vlwidth"=>$this->getvlwidth(),
        ":vlheight"=>$this->getvlheight(),
        ":vllength"=>$this->getvllength(),
        ":vlweight"=>$this->getvlweight(),
        ":desurl"=>$this->getdesurl()
    


    ));

    
// //so nos interessa a primeira linha do resultado, iremos setar no proprio objeto
//     $this->setData($results[0]);
$this->setData($results[0]);

}

public function get($idproduct){

$sql = new Sql();
//iremos trazer o id do produto
$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
    ':idproduct'=>$idproduct


]);
//o primeiro resultado do banco que inicia em 0
$this->setData($results[0]);

}

public function delete()
{



    $sql = new Sql();

//iremos fazer o delete no banco de dados
    $sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
        ':idproduct'=>$this->getidproduct() // fizemos o bind dos parameters fazemos um get para pegar do proprio objeto

    ]);


}

public function checkPhoto(){

//se conter essa foto

if (file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
 "resource" . DIRECTORY_SEPARATOR . 
 "site" . DIRECTORY_SEPARATOR .
 "img" . DIRECTORY_SEPARATOR . 
 "products" . DIRECTORY_SEPARATOR .  // se encontrar um arquivo com o id do produto na pasta products ira retornar esse caminho para foto
 $this->getidproduct() . ".jpg" //concatenamos com o id do produto como o nome da foto sera o id do nosso produto e dizemos que o padrão das nossas fotos sera .jpg
)) {
      //então
    $url = "/resource/site/img/products/" . $this->getidproduct() . ".jpg";//eu faço um return //aqui é url
} else{ //se essa foto nao possuir

    $url = "/resource/site/img/product.jpg";

}
//a gente retorna e seta dentro do objeto
return $this->setdesphoto($url);





}




//iremos reescrever o metodo getValues pegar os valores dessa classe
public function getValues(){
//a gente reescreveu o metodo getValues porque eu não quero chamar toda hora o metodo checkPhoto, entao dessa maneira aqui quando eu chamo o getValues da classe produto ele faz o checkPhoto e faz o getValues padrão da classe model 
//iremos criar um metodo para verificar se tem uma foto nesse produto, assim nao vem sem uma foto, assim vindo uma foto padrao
$this->checkPhoto();

//ira fazer o que a classe principal faz e ira retornar o values
$values = parent::getValues();

return $values;


}


public function setPhoto($file)
{


    //iremos detectar qual é o tipo da extensão do  arquivo que está sendo mandado agora
            // o $extension ira receber atrib  do arquivo e o nome que está dentro dele 
            //ele pegou o nome do arquivo onde tem ponto e fez um array dele 
 $extension =explode('.', $file['name']);
//a extensao e a ultima posicao que ele achou desse array e então ele ira so pegar as 3 ou 4  letras que e a extensao do nome do meu arquivo
$extension = end($extension);
//agora fazemos um switch no atrib extension

switch ($extension) {

    case "jpg":
    case "jpeg":            //utilizamos a funcao da biblioteca GD e como parametro na funcao passamos o nome temporario tmp_name que esta no servidor
         $image = imagecreatefromjpeg($file["tmp_name"]);
    break; // irei fazer uma coisa caso seja um gif, e caso seja um png

    case "gif": 
        $image = imagecreatefromgif($file["tmp_name"]);
    break;

    case "png": 
        $image = imagecreatefrompng($file["tmp_name"]);
    break;


}

//appos colocamos onde queremos salvar o arquivo

$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
 "resource" . DIRECTORY_SEPARATOR . 
 "site" . DIRECTORY_SEPARATOR .
 "img" . DIRECTORY_SEPARATOR . 
 "products" . DIRECTORY_SEPARATOR .  // se encontrar um arquivo com o id do produto na pasta products ira retornar esse caminho para foto
 $this->getidproduct() . ".jpg";
//passamos

//o nome da imagem como primeiro parametro da funcao, e segundo o destino da imagem
imagejpeg($image, $dist);

imagedestroy($image);

//por fim para o dado ficar carregado, assim ira para memoria para o desPhoto

$this->checkPhoto();

}


//criamos o metodo para trazer pela url

public function getFromURL($desurl){

$sql = new Sql();
//ira trazer as linhas
$rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [

':desurl'=>$desurl

]);

//iremos trazer as informacoes da primeira linha
$this->setData($rows[0]);


}


public function getCategories(){

    $sql = new Sql();

    return $sql->select(" 
    SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct
    ",  [

        ':idproduct'=>$this->getidproduct()

    ]);

}

//metodo para pegar a pagina
public static function getPage($page = 1, $itemsPerPage = 10)
{

    $start = ($page - 1) * $itemsPerPage;

    $sql = new Sql();

    $results = $sql->select("
        SELECT SQL_CALC_FOUND_ROWS *
        FROM tb_products 
        ORDER BY desproduct
        LIMIT $start, $itemsPerPage;
    ");

    $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

    return [
        'data'=>$results,
        'total'=>(int)$resultTotal[0]["nrtotal"],
        'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
    ];

}
//metodo para pegar o resultado
public static function getPageSearch($search, $page = 1, $itemsPerPage = 10)
{

    $start = ($page - 1) * $itemsPerPage;

    $sql = new Sql();
//trazer pelo nome do produto
    $results = $sql->select("
        SELECT SQL_CALC_FOUND_ROWS *
        FROM tb_products 
        WHERE desproduct LIKE :search
        ORDER BY desproduct
        LIMIT $start, $itemsPerPage;
    ", [
        ':search'=>'%'.$search.'%'
    ]);

    $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

    return [
        'data'=>$results,
        'total'=>(int)$resultTotal[0]["nrtotal"],
        'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
    ];

}



}

   