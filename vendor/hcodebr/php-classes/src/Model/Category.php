<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;




//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class Category extends Model {

//essa funcao ira ler todos os dados da tabela
public static function listAll()
{

$sql = new Sql();
//ira retornar da tabela categories por ordem pelo descategory
return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

}


//criamos o metodo save

public function save()
{
    $sql = new Sql();

    //iremos chamar uma procedure, pois esssa procedure ira chamar uma pessoa primeiro e entao precisamos saber o id dessa pessoa para poder inserir na tabela de usuarios porque ele precisa do idpessoa vamos pegar o idusuario que retornou e fazer um select com os dados que estão lá no banco de dados agora, a data de cadastro, idusuario  iremos juntar tudo e trazer de volta para isso iremos precisar de uma procedure      

    $results = $sql->select(" CALL sp_categories_save(:idcategory, :descategory)", array(
       ":idcategory" =>$this->getidcategory(),
        ":descategory"=>$this->getdescategory()
    


    ));


//so nos interessa a primeira linha do resultado, iremos setar no proprio objeto
    $this->setData($results[0]);

//chamamos o metodo updateFile quando fazemos um save
Category::updateFile();

}


public function get($idcategory){

$sql = new Sql();
//iremos trazer o id da categoria
$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
    ':idcategory'=>$idcategory


]);
//o primeiro resultado do banco que inicia em 0
$this->setData($results[0]);

}

public function delete()
{



    $sql = new Sql();

//iremos fazer o delete no banco de dados
    $sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
        ':idcategory'=>$this->getidcategory() // fizemos o bind dos parameters fazemos um get para pegar do proprio objeto

    ]);

    //chamamos o metodo updateFile quando fazemos um delete
    Category::updateFile();
}

//Para atualizar o arquivo de categorias iremos criar um metodo updateFile
public static function updateFile()
{
//precisamos saber quais são as categorias que estão no banco de dados
//trazemos todas as categorias

$categories = Category::listAll();

//iremos montar o nosso html 
//criamos um atrib html dizendo que e um array
$html = [];
//ira criar as nossas categorias dinamicamente

//iremos percorrer cada linha do nosso categories
foreach ($categories as $row) {

//iremos adicionar no array o codigo html que queremos adicionar ao arquivo

    array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');

}
//iremos salvar o arquivo
//preciso do caminho do arquivo, o caminho fisico, iremos utilizar o atrib $_SERVER que tem as variaveis de ambientes inclusive o diretorio onde o site esta sendo executado 
//file_put_contents — Escreve uma string para um arquivo
//primeiro parametro o caminho fisico do arquivo, apos a barra DIRECTORY_SEPARATOR, apos onde esta o arquivo html, o ultimo parametro e o conteudo sao as informacoes que quero colocar dentro do arquivo, iremos converter o nosso $html para uma string utilizando o implode, iremos fazer o implode para o nada do meu atrib $html  
file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

}

//iremos criar um metodo que traga todos os produtos //passamos um booleano para saber se queremos trazer os produtos que estão relacionados com essa categoria  ou os que não estão relacionados com essa categoria 
public function getProducts($related = true){


$sql = new Sql();

//se for produtos relacionados
if ($related === true){

//ira retornar do banco os produtos que estão relacionados a categoria
  return $sql->select("
       SELECT * FROM tb_products WHERE idproduct IN(

        SELECT a.idproduct
        FROM tb_products a
        INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
        WHERE b.idcategory = :idcategory
        
        );
        //iremos passar o nosso id no proprio objeto instanciado
    ", [
        ':idcategory'=>$this->getidcategory()
    ]);

} else {

    //ira retornar do banco os produtos que não estão relacionados a categoria
     return $sql->select("SELECT * FROM tb_products WHERE idproduct  NOT IN(

        SELECT a.idproduct
        FROM tb_products a
        INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
        WHERE b.idcategory = :idcategory
        
        );
        ", [

        ':idcategory'=>$this->getidcategory()

        ]);


}

    }
    //adicionando produto
                            //Passamos a classe dizendo que e do tipo produto estamos forçando a passagem do parametro
public function addProduct(Product $product)
{


$sql = new Sql();

$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES(:idcategory, :idproduct)", [
    ':idcategory'=>$this->getidcategory(),
    ':idproduct'=>$product->getidproduct()


]);



}

//removendo produto

   
                            //Passamos a classe dizendo que e do tipo produto estamos forçando a passagem do parametro
public function removeProduct(Product $product)
{
                            
                            
$sql = new Sql();
                            
$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
':idcategory'=>$this->getidcategory(),
':idproduct'=>$product->getidproduct()
                            
                            
]);
                            
                            
                            
}













         }



   

















?>