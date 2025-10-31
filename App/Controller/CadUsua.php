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

    function carregarConteudoHTML($arquivo) {
        return file_get_contents($arquivo);
    }

    function atualizarCabecalho($htmlCabecalho) {
        if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
            $nome = htmlspecialchars($_SESSION['primeiro_nome']);
            return str_replace('@usuario@', "<a href='conta.php'>{$nome}</a>", $htmlCabecalho);
        } else {
            return str_replace('@usuario@', "<a href='login.php'>Login</a> | <a href='cadastrar.php'>Cadastrar</a>", $htmlCabecalho);
        }
    }

    $htmlArquivo = __DIR__ . '/../View/Cadastro.html';
    $htmlCabecalhoArquivo = __DIR__ . '/../View/cabecalho.html';

    $htmlStr = carregarConteudoHTML($htmlArquivo);
    $htmlStrCabecalho = carregarConteudoHTML($htmlCabecalhoArquivo);

    $htmlStrCabecalho = atualizarCabecalho($htmlStrCabecalho);
    $htmlStr = str_replace('@cabecalho@', $htmlStrCabecalho, $htmlStr);

    echo $htmlStr;
    $conexao->close();
} else {
    header('Location: conta.php');
    exit;
}
?>
