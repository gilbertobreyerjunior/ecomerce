<?php 

namespace Hcode;


class Model {
// o $values tera todos os values dos nossos campos do objeto
private $values = [];

//metodo que sera chamado a todo instante
//primeiro parametro o name do metodo que foi chamado, o segundo parametro os argumentos que foram chamados

public function __call($name, $args) 
{


//se for get iremos trazer a informacao
//se for set teremos que atribuir valores dos atrib da informacao que foi passada
//utilizamos a funcao substr passamos o $name e queremos a partir da posicao 0 e traz a quantidade 3
$method = substr($name, 0, 3);
//iremos descobrir o nome do campo que foi chamado iremos descartar os 3 primeiros e nesse caso ira iniciar na posicao 3 porque 0 , 1 ,2 ja veio e agora ira ter que ate o final da nossa palavra, utilizamos a funcao strlen para contar  
$fieldName = substr($name, 3, strlen($name));
//iremos fazer um switch do method 
switch ($method) 
{



//se for get ira fazer alguma coisa
     case "get": 
        //iremos procurar esse campo dentro do values em nosso fieldname se ele encontrar da um return
                    //Iremos fazer uma validação: se foi definido se existe entao retorna ele, se não foi definido ? pode retornar nulo
        return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
     break;
//se for o set
     case "set": // iremos procurar dentro do values em nosso fieldname e iremos aplicar o valor dentro do args o primeiro argumento na posicao 0
        $this->values[$fieldName] = $args[0];
     break;
    }


}
//iremos criar um metodo que ira fazer um foreach que ira ser passado como parametro um atrib array que e para os nossos dados que vem do banco
//Esse metodo e como por exemplo o banco quantos campos voce retornou para mim? a retornei 5 entao para cada um deles que voce retornou voce ira criar um atrib com o valor de cada uma dessas informacoes
public function setData($data = array()) 
{

//ira percorrer cada linha do nosso banco nas chaves e valores

    foreach ($data as $key => $value) {
//ira chamar cada um dos metodos automaticamente essa string ela apos sera executada como um metodo que o set
       //estamos criando dinamicamente os campos que estao vindo do banco de dados
       //iremos colocar uma string que depois sera chamada como um metodo e concatenamos com o valor que esta vindo na variavel $key e na frente os parametro a chamada desse metodo e passamos o value ira chamar cada um dos metodos automaticamente 
$this->{"set".$key}($value);

    }

}

public function getValues(){


//retorna os values
        return $this->values;
}




}


?>