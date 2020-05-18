<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;




//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class User extends Model {


    const SESSION = "User";
    //criamos uma constante para criptografia com 16 caracteres, essa chave iremos precisar para criptografar e descriptografar
   //e necessario ter no minimo 16 caracteres nesse chave ou mais
    const SECRET = "HcodePhp7_Secret";



    const SECRET_IV = "HcodePhp7_Secret_IV";


//iremos verificar se essa sessao existe, se o id do usuario e maior que 0
public static function getFromSession(){

    $user = new User();


//se existir essa sessao, e for inteiro esse id e esse id for maior que 0
    if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0){
//entao iremos conseguir retornar o novo usuario

        $user->setData($_SESSION[User::SESSION]);

    }


    return $user;

} 


//metodo para verificar se o usuario esta logado
public static function checkLogin($indadmin = true){

//iremos verificar se a sessao do usuario nao esta definida, se nao esta definida nao esta logado, ou se esta definida mas esta vazia

    if (

    !isset($_SESSION[User::SESSION])
    || // ou se ela for falsa
    !$_SESSION[User::SESSION]
    || // ou se o iduser nao for maior que 0
    !(int)$_SESSION[User::SESSION]["iduser"] > 0

    ){

//nao esta logado
        return false;

    }else { //estou fazendo uma verificacao de uma rota da administracao, se eu estiver fazendo isso 
//esse if ira acontecer so se ele acessar uma rota de administrador
        if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin']){

            return true;
//se nao se, ele esta logado mas nao necessariamente precisa ser um administrador
        } else if($indadmin === false){

                //esta logado também

                return true;


        }else { //nao esta logado

                return false;

        }

    }

}



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



    if (User::checkLogin($indadmin))  //se essa sessao nao for definida

      //se nao for definida a sessao sera redirecionada para a tela de login
            header("Location: /admin/login");
            exit;
   
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

// //iremos criar o metodo estatico que recebe como parametro o email
//    public static function getForgot($email){

// //iremos verificar se o e-mail esta cadastrado no banco de dados

//     $sql = new Sql();

//     $results = $sql->select("
//     SELECT * 
//     FROM tb_persons a
//     INNER JOIN tb_users b USING(idperson)
//     WHERE a.desemail = :email;
//     ", array(
//         ":email"=>$email

//     ));

// //se nao retornou nenhum email caso não encontre nenhum email disparamos uma exception
// if (count($results) === 0)
// {

//     throw new \Exception("Não foi possivel recuperar a senha");

// } else 
// {
    
    
//     //se ele encontrar
// //iremos criar um novo registro naquela tabela de recuperacao de senha 

// $data = $results[0];

// $results2 = $sql->select("CALL_ sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
//     ":iduser"=>$data["iduser"],
//     ":desip"=>$_SERVER["REMOTE_ADDR"]

// ));
// //Se não ocorreu nenhum problema se o results  for sera ira disparar uma exception
//  if (count($results2) === 0)
//  {

//      throw new \Exception("Não foi possivel recuperar a senha");

//  }
//  else
// {//se tudo ok criou 
// //Iremos receber os dados que foram gerados a partir da posicao 0
// $dataRecovery = $results2[0];


// //agora precisamos gerar um codigo criptografa essa procedure ira retornar o id recover que foi a chave primaria autoincrement que foi gerada de um banco de dados agora irmeos pegar esse numero encriptar isso  para o usuario nao conseguir ver que numero que e, ou alterar ou tentar buscar outros numeros possiveis e ira mandar como um link dentro do email essa criptografia
// //primeiramente iremos transformar em base 64


//             //usa a funcao de critografia que recebe quatro parametros, primeiro parametro o tipo de criptografia que e uma constante do php que sera em 128 bits, o segundo parametro passamos a chave de criptografia passamos a nossa constante para a criptografia, apos o terceiro parametro e os dados que queremos criptografar, o terceiro parametro a constante do modo de criptografia que tipo de criptografia que iremos usar, usamos o tipo ECB Eletronic Code book ela procura um range de dados randomicos para usar o modo de criptografia nosso  
// $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));
 
// //iremos montar o nosso link que e o endereco onde ira esse codigo e esse link sera mandado por email
// $link = "http://www.lojatech.com.br/admin/forgot/reset?code=$code"; //passamos o codigo por get entao : ?code=$code

// ///iremos mandar por email pela classe Mailer
//                  //passamos como parametro dentro do construtor os seguintes parametros  primeiro parametro o email, segundo parametro o nome do usuario, terceiro parametro o assunto, quarto parametro o nome do nosso template
// $mailer = new Mailer($data["desemail"], $data["desperson"],  "Redefinir Senha da Hcode Store", "forgot", 
// array(
// //Passamos os dados que queremos renderizar dentro desse template quais são os dados que ele precisa 
// "name"=>$data["desperson"],
// "link"=>$link

// ));

// $mailer->send();
// //fazer o return dos dados do usuario que foi recuperado
// return $data;


// }

// }

// }






public static function getForgot($email, $inadmin = true)
	{

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha.");

		}
		else
		{

			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data['iduser'],
				":desip"=>$_SERVER['REMOTE_ADDR']
			));

			if (count($results2) === 0)
			{

				throw new \Exception("Não foi possível recuperar a senha.");

			}
			else
			{

				$dataRecovery = $results2[0];

				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

				if ($inadmin === true) {

					$link = "http://www.lojatech.com.br/admin/forgot/reset?code=$code";

				} else {

					$link = "http://www.lojatech.com.br/forgot/reset?code=$code";
					
				}				

				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
					"name"=>$data['desperson'],
					"link"=>$link
				));				

				$mailer->send();

				return $link;

			}

		}

	}



    public static function validForgotDecrypt($code)
	{

		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{

			return $results[0];

		}

	}























// public static function validForgotDecrypt($code)
// {

// //iremos fazer o processo inverso como o codigo foi criptografado por fim em base 64
// //iremos tirar do base 64 
// base64_decode($code);
// //primeiro parametro o tipo de criptografia que e uma constante do php que sera em 128 bits, o segundo parametro passamos a chave de criptografia passamos a nossa constante para a criptografia, terceiro parametro os dados vem do base 64, o quarto parametro  a constante do modo de criptografia que tipo de criptografia que iremos usar, usamos o tipo ECB Eletronic Code book ela procura um range de dados randomicos para usar o modo de criptografia nosso  

// //iremos receber no atrib idrecovery
// $idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);


// $sql = new Sql();

// //iremos fazer uma verificacao, se o id existe, se foi usado, se valido questao do tempo
// $results  = $sql->select("
// SELECT * 
// FROM tb_userspasswordsrecoveries a
// INNER JOIN tb_users b USING(iduser)
// INNER JOIN tb_persons c USING(idpserson)
// WHERE
// a.idrecovery = :idrecovery
// AND
// a.dtrecovery IS NULL
// AND
// DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
// ", array(

//     ":idrecovery"=>$idrecovery

// ));
// //se trazer o resultado igual a 0 dispara uma exception
// if (count($results) === 0)
// {

//    throw new \Exception("Não foi possivel recuperar a senha"); 
// }
// else //se conseguiu iremos devolver os dados do usuario que recuperamos
// {

//     //iremos fazer um return do meu result na posicao 0
//     return $results[0];

// }



// }

//precisamos criar um metodo para dar um update no banco para dizer aquela coluna, o metodo que ira salvar que ira falar para o banco de dados que essa recuperação, já foi usado, para não recuperar novamente, mesmo que esteja dentro dessa uma hora  
public static function setForgotUsed($idrecovery)
{

        $sql = new Sql();
        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
            ":idrecovery"=>$idrecovery


        ));
    
}

                            //passamos como parametro a nova senha que iremos passar
public function setPassword($password)
{

$sql = new Sql();

$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
":password"=>$password,
":iduser"=>$this->getiduser()


));

}


}

   

















?>