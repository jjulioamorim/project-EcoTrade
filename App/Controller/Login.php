<?php
include '../Model/config/Conexao.php';
session_start();
if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
    header('Location: conta.php');
    exit;
}else{
    $htmlarquivo = "../view/Login.html";
    $htmlstr = file_get_contents($htmlarquivo);
    echo $htmlstr;
}
?>
