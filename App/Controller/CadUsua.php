<?php
session_start();
include __DIR__ . '/../Model/config/conexao.php';

// Se for uma requisição AJAX (POST), responde em JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    include __DIR__ . '/../Model/ModelCadUsua.php';
    exit;
}

// Se não for POST (ou seja, o usuário abriu a página no navegador normalmente)
if (!isset($_SESSION['logado']) || $_SESSION['logado'] != true) {

    // Carrega o HTML da view
    $htmlArquivo = __DIR__ . '/../View/Cadastro.html';
    $htmlStr = file_get_contents($htmlArquivo);

    // Remove o placeholder @cabecalho@ que não estamos usando
    $htmlStr = str_replace('@cabecalho@', '', $htmlStr);

    echo $htmlStr;
    $conexao->close();
} else {
    header('Location: Dashboard.php'); // Redireciona para o Dashboard se já logado
    exit;
}
?>