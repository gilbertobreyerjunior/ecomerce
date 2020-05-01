<?php 


namespace Hcode;


class PageAdmin extends Page {

                            //no metodo construtor passamos como parametro as nossas opcoes que e um array, apos o segundo parametro caminho da nossa pasta views/admin
                            //entao agora tudo que eu precisar de layout, eu nao vou mais pegar daquela pasta views agora vou pegar da pasta view/admin
public function __construct($opts = array(), $tpl_dir = "/views/admin/")
{


//estou dizendo com o parente nao chama o construtor daqui mas da classe base que e page
            //possamos como parametro o $opts o nosso array de opcoes, apos o nosso $tpl_dir que e o nosso caminho onde esta o nosso template
parent::__construct($opts, $tpl_dir);

}


}


?>