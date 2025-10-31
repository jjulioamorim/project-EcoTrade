<?php
session_start();
if (!isset($_SESSION['logado']) && $_SESSION['logado'] != true) {
include '../model/conexao.php';
    // Função para carregar o conteúdo de um arquivo HTML
    function carregarConteudoHTML($arquivo) {
        return file_get_contents($arquivo);
    }
    
    // Função para atualizar o cabeçalho com base no login do usuário
    function atualizarCabecalho($htmlCabecalho) {
        if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
            $nome = htmlspecialchars($_SESSION['primeiro_nome']);  // Sanitizando a entrada
            return str_replace('@usuario@', "<a href='conta.php'>{$nome}</a>", $htmlCabecalho);
        } else {
            return str_replace('@usuario@', "<a href='login.php'>Login</a> | <a href='cadastrar.php'>Cadastrar</a>", $htmlCabecalho);
        }
    }
    
    // Carrega o conteúdo do HTML
    $htmlArquivo = "../view/cadastrar.html";
    $htmlCabecalhoArquivo = "../view/cabecalho.html";
    
    // Carregar os arquivos HTML
    $htmlStr = carregarConteudoHTML($htmlArquivo);
    $htmlStrCabecalho = carregarConteudoHTML($htmlCabecalhoArquivo);
    
    // Atualizar o cabeçalho conforme o login
    $htmlStrCabecalho = atualizarCabecalho($htmlStrCabecalho);
    
    // Substituir a marcação @cabecalho@ pelo cabeçalho atualizado
    $htmlStr = str_replace('@cabecalho@', $htmlStrCabecalho, $htmlStr);
    
    include '../model/conexao.php';
    include '../model/modelCadastrar.php';

    // Substituir os placeholders pelos dados de cursos e unidades
    $htmlStr = str_replace('@@cursos@@', $opcoesCursos, $htmlStr);
    $htmlStr = str_replace('@@unidades@@', $opcoesUnidades, $htmlStr);    


    echo $htmlStr;

$conexao->close();  v  

}else{
    header('Location: conta.php');
    exit;
}
?>
