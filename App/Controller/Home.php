<?php
$nome = "julio";
$arquivo = file_get_contents( '../View/Index.html');
$strarquivo = str_replace('@nome@', $nome, $arquivo);
echo $strarquivo; 