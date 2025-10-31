<?php
$sql_senha = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql_senha);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
?>