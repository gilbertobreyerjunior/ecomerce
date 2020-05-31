<?php 
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;






//entendemos que essa class User e um model, e entendemos que todo model tera getrs e seters, entao iremos criar uma classe Model que teremos o geters e seters e cada classe  DAO User Categorias, Produtos ira extender de um modelo que ira saber fazer os getters e seters automaticamente 
class Address extends Model {


        const SESSION_ERROR = "AddressError";

        public static function getCEP($nrcep)
        {


                                //utilizamos o str replace para substituir - por vazio
                                $nrcep = str_replace("-", "", $nrcep);
                                $ch = curl_init();

                                            //passamos a url que queremos usar fazer a chamada que e a viacep
                                            curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");
                //definimos que queremos que traga um retorno
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //informamos que nao tera uma verificacao de SSL
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                //executamos e transformamos em um json decode de um json para um array o resultado
                $data = json_decode(curl_exec($ch), true);
            //fechamos o curl
            curl_close($ch);

                    return $data;

        }




//metodo para forçar para carregar o endereço com os campos certos
public function loadFromCEP($nrcep) {



                    //iremos carregar o data
                    $data = Address::getCEP($nrcep);


                    //iremos fazer uma verificação se ele retornou alguma coisa, se ele existe ou retornou vazio

                    if (isset($data['logradouro']) && $data['logradouro']) {

                                        //iremos fazer os setters pega pelos nomes dos campos do banco de dados
                                        $this->setdesaddress($data['logradouro']);
                                        $this->setdescomplement($data['complemento']);
                                        $this->setdesdistrict($data['bairro']);
                                        $this->setdescity($data['localidade']);
                                        $this->setdesstate($data['uf']);
                                        $this->setdescountry('Brasil');
                                        $this->setdeszipcode($nrcep);
                            


                    }



        }

        //metodo para salvar o endereço os dados para quando finalizar a compra

        public function save(){


                $sql = new Sql();
                                        //fazemos o select dessa nossa procedure trazendo todos os dados, iremos salvar todos os dados que estão vindo pela procedure
                 $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
                //passamos o bind dos nossos parametros
                ':idaddress'=>$this->getidaddress(),
                ':idperson'=>$this->getidperson(),
                ':desaddress'=>utf8_decode($this->getdesaddress()),
                //':desnumber'=>$this->getdesnumber(),
                ':descomplement'=>utf8_decode($this->getdescomplement()),
                ':descity'=>utf8_decode($this->getdescity()),
                ':desstate'=>utf8_decode($this->getdesstate()),
                ':descountry'=>utf8_decode($this->getdescountry()),
                ':deszipcode'=>$this->getdeszipcode(),
                ':desdistrict'=>$this->getdesdistrict()
        ]);
                        //fizemos uma verificação do index se é maior que 0
                        if (count($results) > 0) {
                        //entao traz a partir da primeira linha
			$this->setData($results[0]);
		}
        }


    //metodo para sessao setar sessao de mensagens

    public static function setMsgError($msg){



        $_SESSION[Address::SESSION_ERROR] = $msg;
    }
    
    //metodo par pegar o erro
    public static function getMsgError()
    {
    
    
    
            //iremos validar se isso esta definido iremos pegar a mensagem que esta na sessao 
            $msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";

    
        //nos limpamos a mensagem da sessao 
        Address::clearMsgError();
    
        return $msg;
    
    }
    
    
        // metodo para limpar a sessao  as informacoes
    
    
        public static function clearMsgError()
        {
    
                $_SESSION[Address::SESSION_ERROR] = NULL;
    
    
    
        }




         }



   

















?>