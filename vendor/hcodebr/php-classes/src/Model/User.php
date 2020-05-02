<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;




//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class User extends Model {


    const SESSION = "User";


public static function login($login, $password)
{

    //iremos buscar no banco de dados o foi digitado no login  se ele existe no banco de dados 

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users WHERE  deslogin = :LOGIN", array(
        ":LOGIN"=>$login

    ));
//se nao encontrou login for igual a zero
    if (count($results) === 0)

    {
//dispara um excessao como a excessao esta no escopo principal colocamos um \ para achar a Exception principal
            throw new \Exception("Usuario inexistente ou senha inválida");

    }

    //se passar pelo if sabemos que tem um resultado entao traz o primeiro results registro
    $data = $results[0];
//iremos verificar a senha do usuario
//passamos o $password que veio como parametro, apos o data que e o hash for igual a true
    if(password_verify($password, $data["despassword"]) === true)
    {
        //se der certo iremos criar uma instancia da classe User
//iremos passar os dados do usuario

        $user = new User();
//iremos passar o array inteiro para passar em todos os dados do banco
       $user->setData($data);

//iremos definir a nossa sessao  iremos deixar o nome da constante da propria classe por questao de organizacao com o nome da propria 
       //o $user ira acessar a constante o SESSION 
    //na sessao ira ter os dados do objeto usuario so que como um array
    //ira receber os values que estao vindo da funcao getValues
        $_SESSION[User::SESSION] = $user->getValues();

        return $user;
        //se nao iremos estourar uma exception
    }else {


        throw new \Exception("Usuario inexistente ou senha invalida");
    }

}
//metodo estatico para verificar o login passamo como parametro o inadmin = true dizendo que e admin
public static function verifyLogin($inadmin = true){



    if (  //se essa sessao nao for definida

        !isset($_SESSION[User::SESSION])
|| // ou se ela for falsa
!$_SESSION[User::SESSION]
|| // ou se o iduser nao for maior que 0
!(int)$_SESSION[User::SESSION]["iduser"] > 0
||// ou se o usuario nao for admin
(bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
    )
    {   //se nao for definida a sessao sera redirecionada para a tela de login
            header("Location: /admin/login");
            exit;
    }
}

//criamos o metodo de logout para limpar a session iremos anular a sessao
public static function logout() {


$_SESSION[User::SESSION] = NULL;


}


//essa funcao ira ler todos os dados da tabela
public static function listAll()
{

$sql = new Sql();
//o usuario precisa de uma pessoa para ser criado ele tem um idperson dentro da tabela de usuarios onde temos o e-mail telefone  
                                                //iremos unir as informacoes com o INNER JOIN 
                                                //tabela b  utilizamos o USING se tiver o mesmo nome de campo idperson que tem nas duas tabelas e o ODER BY pela nome da pessoa  
return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

}


//criamos um metodo para salvar os nossos dados no banco
public function save()
{


    $sql = new Sql();

    //iremos chamar uma procedure, pois esssa procedure ira chamar uma pessoa primeiro e entao precisamos saber o id dessa pessoa para poder inserir na tabela de usuarios porque ele precisa do idpessoa vamos pegar o idusuario que retornou e fazer um select com os dados que estão lá no banco de dados agora, a data de cadastro, idusuario  iremos juntar tudo e trazer de volta para isso iremos precisar de uma procedure      

    $results = $sql->select(" CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
       ":desperson" =>$this->getdesperson(),
        ":deslogin"=>$this->getdeslogin(),
        ":despassword"=>$this->getdespassword(),
        ":desemail"=>$this->getdesemail(),
        ":nrphone"=>$this->getnrphone(),
        ":inadmin"=>$this->getinadmin()



    ));


//so nos interessa a primeira linha do resultado, iremos setar no proprio objeto
    $this->setData($results[0]);

}


//iremos criar um metodo para pegar o usuario
public function get($iduser)
{

    //iremos carregar esse usuario do banco

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser
    ", array (
        ":iduser"=>$iduser


    ));

    $this->setData($results[0]);

}

//iremos criar um metodo update

    public function update() {

        $sql = new Sql();


    //iremos chamar uma procedure, pois esssa procedure ira chamar uma pessoa primeiro e entao precisamos saber o id dessa pessoa para poder inserir na tabela de usuarios porque ele precisa do idpessoa vamos pegar o idusuario que retornou e fazer um select com os dados que estão lá no banco de dados agora, a data de cadastro, idusuario  iremos juntar tudo e trazer de volta para isso iremos precisar de uma procedure      

    $results = $sql->select(" CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
       ":iduser"=>$this->getiduser(),
        ":desperson" =>$this->getdesperson(),
         ":deslogin"=>$this->getdeslogin(),
         ":despassword"=>$this->getdespassword(),
         ":desemail"=>$this->getdesemail(),
         ":nrphone"=>$this->getnrphone(),
         ":inadmin"=>$this->getinadmin()
 
 
 
     ));
 
 
 //so nos interessa a primeira linha do resultado, iremos setar no proprio objeto
     $this->setData($results[0]);
    }


    //criamos o metodo delete

    public function delete()
    {


        $sql = new Sql();
        
        $sql->query("CALL sp_users_delete(:iduser)", array(
        ":iduser"=>$this->getiduser()



        ));



    }


}











?>