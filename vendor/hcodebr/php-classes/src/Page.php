<?php

namespace Hcode; //precisamos especificar em qual namespace essa classe esta 

use Rain\Tpl; // aqui iremos usar uma outra classe que esta em outro namespace e passamos o namespace do Rain Tpl, para ele saber que quando iremos dar um new Tpl e do namespace Rain  

//criamos a classe page
class Page {
//As variaveis irao vir de acordo com a rota, entao dependendo da rota que estivermos chamando lá no slim é que iremos passar os dados para a classe page


    private $tpl;
    private $options = [];
    //iremos ter algumas opcoes padroes uns defaults que sera um array
    private $defaults = [
//por padrão estamos falando que o header e o footer são true
        "header"=>true,
        "footer"=>true,        
//iremos passar um array dados
        "data"=>[] // o array data sera vaziu por padrão, então se sobrescrevermos esse data, ele ira criar ou ira passar para o nosso template, 
    ];

    //As variaveis irao vir com a rota, dependendo da rota que iremos chamando no slim e que iremos passar os dados para a classe page
                             //iremos receber algumas opcoes da classe
//criamos o metodo magico construtor, passamos como parametro no metodo construtor que serao opcoes da classe sera um array
                                            //passamos um segundo parametro dentro do construct uma variavel$tpl_dir que recebe o caminho da nossa  views
public function __construct($opts = array(), $tpl_dir = "/views/"){

$this->options = array_merge($this->defaults, $opts); // com o array merge ira mesclar dois arrays siginifica juntar dois arrays, ai  temos um segredo pois iremos colocar os dois arrays para realizar a mesclagem, lembrando que o ultimo array ira sobrescrever os anteriores, entao queremos com que o que a pessoa tenha informado no construct sobrescreva no default


        $config = array(
         "tpl_dir"  => $_SERVER["DOCUMENT_ROOT"].$tpl_dir, //A partir do nosso diretorio root do nosso projeto procura a pasta tal se não ele ira procurar a partir desse repositorio  das nossas classes, entao usamos a variavel de ambiente no $_SERVER[DOCUMENT_ROOT] ele ira trazer onde esta a pasta o diretorio root do nosso servidor, apos iremos falar onde esta o template concatenamos com o $tpl_dir 
         "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/views-cache/", //e apartir da parte do cache podemos usar  views-cache,  entao usamos a variavel de ambiente no $_SERVER[DOCUMENT_ROOT] ele ira trazer onde esta a pasta o diretorio root do nosso servidor
            "debug" => false // set to false to improve the speed
           );
//Passou as configurações para o Tpl, ele ja sabe que essa  classe e do Rain que configuramos no inicio
            Tpl::configure( $config );
//para esse $tpl termos acesso nos outros metodos, é mais interessante  ser um atributo da nossa classe 
                $this->tpl = new Tpl;



           //apos chamamos a nossa funcao setData que faz o foreach do $data e o assign
            $this->setData($this->options["data"]);

// //os nossos dados vao estar na chave data desse options, entao iremos fazer um foreach no $this->options "data"
//                                                //iremos ter a chave e o valor desse array data
//                     foreach ($this->options["data"] as $key => $value) {
//                         //iremos passar o nosso template que instanciamos na linha 36 e iremos usar o metodo assign e ele espera justamente uma chave e o valor  
//                         $this->tpl->assign($key, $value);



//iremos desenhar o template na tela, o draw espera o nome do arquivo que queremos chamar 
//Fazemos um if se isso aqui o $option com header for true entao adicionamos desenhamos o header no template
if ($this->options["header"] === true) $this->tpl->draw("header");
                   

}




        //ira receber como parametro os nossos dados com o array data
private function setData($data = array())   {

//os nossos dados vao estar no array data , que entao iremos percorrer as chaves e os valores do array data
                                               //iremos ter a chave e o valor desse array data
    foreach ($data as $key => $value) {
        //iremos passar o nosso template que instanciamos na linha 36 e iremos usar o metodo assign e ele espera justamente uma chave e o valor  
        $this->tpl->assign($key, $value);


}
}


    //iremos adicionar o corpo da pagina iremos fazer um metodo so para o html do conteudo
            //iremos receber qual o nome do template, iremos recebe os dados as variaveis que queremos passar esse e um array vazio, o terceiro parametro passamos se queremos o retorno do  html na tela ou não
    public function setTpl($name, $data = array(), $returnHTML = false){



        //chamamos a nossa funcao setdata para fazer o foreach para percorrer cada chave e valor dos nossos dados 

        $this->setData($data);
//draw funcao de desenhar
        //iremos desenhar um template na tela que e o que iremos passar pelo name, o primeiro parametro o name do template e o segundo parametro para retornar o HTML
        //como essa linha faz o draw se precisar armazenar em outro lugar podemos colocar o return 
        return $this->tpl->draw($name, $returnHTML);
    }



//criamos o metodo magico destruct
   public function __destruct() {

//quando essa classe sair da memoria do php, iremos adicionar o footer 
//Fazemos um if se isso aqui o $option com footer for true entao adicionamos desenhamos o footer no template

if($this->options["footer"] === true)$this->tpl->draw("footer");


    }




}



?>