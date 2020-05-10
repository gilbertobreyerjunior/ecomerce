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
        ":vlweight"=>$this->getvlweigth(),
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





}

   