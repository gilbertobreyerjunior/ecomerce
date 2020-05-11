<?php
//essa funcao iremos usar dentro do template
function formatPrice(float $vlprice){
//ira retornar o valor formatado do atrib $vlprice, para garantir que e um valor mesmo colocamos o float
                                //o primeiro separador das casas decimais e a virgula, o segundo case de milhar e  . 
return number_format($vlprice, 2, ",", ".");
    
    
    
    }
    


?>