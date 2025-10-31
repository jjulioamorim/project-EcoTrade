<?php
include '../Model/config/Conexao.php';
session_start();    





$arquivo = file_get_contents('../View/Perfil.html'); 
$arquivo = str_replace('@nome@', htmlspecialchars($_SESSION['nome']), $arquivo);
$arquivo = str_replace('@email@', htmlspecialchars($_SESSION['email']), $arquivo);
$arquivo = str_replace('@tipo_usuario@', htmlspecialchars($_SESSION['tipo_usuario']), $arquivo);

echo $arquivo; 
 
 ?>