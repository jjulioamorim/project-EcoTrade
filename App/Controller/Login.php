<?php
include '../model/conexao.php';
session_start();
if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
    header('Location: conta.php');
    exit;
}else{
    $htmlarquivo = "../view/login.html";
    $htmlstr = file_get_contents($htmlarquivo);
    
    $htmlarquivo_cabecalho = "../view/cabecalho.html";
    $htmlstr_cabecalho = file_get_contents($htmlarquivo_cabecalho);


    $htmlstr_cabecalho = str_replace('@usuario@', "<a href='login.php'>Login</a> | <a href='cadastrar.php'>Cadastrar</a>", $htmlstr_cabecalho);
    $htmlstr = str_replace('@cabecalho@', $htmlstr_cabecalho, $htmlstr);

    echo $htmlstr;
}
?>
