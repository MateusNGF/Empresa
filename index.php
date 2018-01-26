<?php 
include 'bin/aplicaçoes_web.class.php';

$NomeClient = 'MateusNGF';
$EmailClient = 'nome@servidor.com';
$SenhaClient = 'm@t3u2';
$CpfClient = '111.866.185-01';

$Client = new Client();
$INSERCAO = $Client->setClient($NomeClient, $EmailClient, $SenhaClient, $CpfClient);
if ($INSERCAO){
    if ($Client->registerClient()){
        echo nl2br("<span style='color:green;padding:0 50px;'>SUCESSO</span>\n");
        if($Client->ClientNow()){
            echo $Client->ClientNow();
        }else{
            ERRO($Client->ERR_PRINT());   
        }
    }else{
        ERRO($Client->ERR_PRINT());  
    }
}else{
    ERRO($Client->ERR_PRINT());  
}



// FUNÇÃO PARA RETORNA ERROS POSSIVEIS
function ERRO($ERRO){ 
    if (empty($ERRO)){
        echo "PARAMETRO NÃO PASSADO PARA ERRO";
    }else{
        $ERRO = strtoupper($ERRO);
        echo "ALGO DEU ERRADO <a href='?Detalhes' style='color:red;'>MAIS DETALHES</a>";
        if (isset($_GET['Detalhes'])){
            echo nl2br("\n\n\nO ERRO FOI CAUSADO PELO SEGUITE FATO : \n\n<b>====/ {$ERRO} \====</b>");
        }
    }
}
?>