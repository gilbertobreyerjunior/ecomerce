<?php  
//classe responsavel por mandar os emails
namespace Hcode;

//iremos usar o reenderizador de template html que no caso RainTpl
use Rain\Tpl;

class Mailer {

//criamos a constante para o usuario e senha  para ficar mais facil quando quisermos mudar o email que e utilizado para enviar pelo sistema

    const USERNAME = "suportcodebr@gmail.com";
    const PASSWORD = "Tacografo90";
    //Criamos uma constante para o remetente
    const NAME_FROM = "Loja Tech";

//criamos o $email com atrib pois para usarmos o metodo send a parte enviamos so o momento que quisermos esse email apos ele created
    private $mail;
                         //no metodo construtor passamos como parametro o endereço que iremos mandar, segundo parametro quem ira receber o destinatario, terceiro parametro o assunto, o quarto parametro  e o nome do arquivo de template que iremos mandar para o Rain TPL, o quinto parametro sao os dados que queremos passar   
public function __construct($toAddress, $toName, $subject, $tplName, $data = array() ){


    $config = array(
        "tpl_dir"  => $_SERVER["DOCUMENT_ROOT"]."/views/email/", //A partir do nosso diretorio root do nosso projeto procura a pasta tal se não ele ira procurar a partir desse repositorio  das nossas classes, entao usamos a variavel de ambiente no $_SERVER[DOCUMENT_ROOT] ele ira trazer onde esta a pasta o diretorio root do nosso servidor, apos iremos falar onde esta o template /views/email/ 
        "cache_dir" => $_SERVER["DOCUMENT_ROOT"]."/views-cache/", //e apartir da parte do cache podemos usar  views-cache,  entao usamos a variavel de ambiente no $_SERVER[DOCUMENT_ROOT] ele ira trazer onde esta a pasta o diretorio root do nosso servidor
           "debug" => false // set to false to improve the speed
          );
//Passou as configurações para o Tpl, ele ja sabe que essa  classe e do Rain que configuramos no inicio
           Tpl::configure( $config );
//para esse $tpl termos acesso nos outros metodos, é mais interessante  ser um atributo da nossa classe 
           $tpl = new Tpl;

//Agora iremos passar os dados para o template
foreach ($data as $key =>  $value) {


    $tpl->assign($key, $value);


}
//desenhamos o nosso template e colocamos true para jogar no atrib que ira mandar no corpo da mensagem
$html = $tpl->draw($tplName, true);



//Crio a variável $mail que irá receber a instância do objeto PHPMailer

//Create a new PHPMailer instance
$this->mail = new \PHPMailer;

//O $mail esta invocando a função isSMTP() que é o que voce irá fazer com o PHPMAILER, voce está enviando e-mail? voce está recebendo e-mail?
//Tell PHPMailer to use SMTP Realmente é um método que já prepara o PHPMailer para enviar um e-mail, ele sabe quais são os métodos que precisam ser carregados dessa classe
$this->mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use) Se colocar 0 ele desliga, ou seja na produção ele não ira fazer nada de debug, não vai jogar um vardump lá na tela ou um comentário que serve para o desenvolvedor 
// 1 = client messages  Ele deixa mensagens para o cliente  menssagens simplificadas só para ver o que está acontecendo dentro da classe 
// 2 = client and server messages São mensagens que tem no cliente e no servidor, são mensagens do usuário, e mensagens do ambiente do server 

//0 Quando estiver em produção
//1 Quando estivermos fazendo testes
//2  Quando estivermos desenvolvendo

$this->mail->SMTPDebug = 0;
//Onde está o servidor de e-mail
//Set the hostname of the mail server

$this->mail->Debugoutput = 'html';


$this->mail->Host = 'smtp.gmail.com';
//Posso colocar outros servidores de e-mail se tiver ocupado pula para o próximo e utiliza o que não está ocupado






//Porta
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$this->mail->Port = 587;
//Segurança
//Set the encryption system to use - ssl (deprecated) or tls
$this->mail->SMTPSecure = 'tls';
//Se irá ser autenticado que no caso está true = verdadeiro
//Whether to use SMTP authentication
$this->mail->SMTPAuth = true;
//E-amail do gmail
//No usuario iremos usar a constante que criamos 
$this->mail->Username = Mailer::USERNAME;
//Senha do gmail
//No usuario iremos usar a constante que criamos 
$this->mail->Password = Mailer::PASSWORD;
//Quem é o remetente desse e-mail
//Set who the message is to be sent from
$this->mail->setFrom('Mailer::USERNAME', 'Mailer::NAME_FROM');
//Responder para
//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');
//Quais endereços de e-mail que voce deseja envir
//Set who the message is to be sent to

//Para quem iremos mandar

//passamos os atrib do metodo construtor  que e para qual endereco e o Nome
$this->mail->addAddress($toAddress, $toName);
//Assunto do e-mail
//Set the subject line
//Passamos o assunto que e o parametro que esta vindo no metodo construtor
$this->mail->Subject = $subject;
//É o nosso layout do nosso e-mail em HTML
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$this->mail->msgHTML($html);
//O corpo alteranativo, se ele não tiver com um leitor de e-mails que suporte HTML, então ele irá só aparecer como texto, e colocamos o texto para exibir se o HTML não funcionar
//Replace the plain text body with one created manually
$this->mail->AltBody = 'Seu exame esta disponivel';
//Se voce quiser adicionar anexos
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//Se não enviou o e-mail ele ira exiber Mailer Error
//Else se enviou exibe a mensagem Message sent



//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}






}
//criamos a funcao que faz o envio do email
public function send(){


    return $this->mail->send();


}



}







?>