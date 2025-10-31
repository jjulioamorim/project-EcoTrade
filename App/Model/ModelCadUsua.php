<?php
require_once "config/conexao.php";
$nome  = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$tipo  = $_POST['tipo'];

// Inserir usuário na tabela principal
$sqlUsuario = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->bind_param("ssss", $nome, $email, $senha, $tipo);
$stmt->execute();
$usuario_id = $stmt->insert_id;

// Inserir dados específicos conforme o tipo
if ($tipo == 'produtor') {
    $cpf = $_POST['cpf'];
    $nome_fazenda = $_POST['nome_fazenda'];
    $localizacao = $_POST['localizacao'];

    $sqlProdutor = "INSERT INTO produtores (usuario_id, cpf, nome_fazenda, localizacao) VALUES (?, ?, ?, ?)";
    $stmtProd = $conexao->prepare($sqlProdutor);
    $stmtProd->bind_param("isss", $usuario_id, $cpf, $nome_fazenda, $localizacao);
    $stmtProd->execute();

} elseif ($tipo == 'empresa') {
    $cnpj = $_POST['cnpj'];
    $razao_social = $_POST['razao_social'];
    $setor_atuacao = $_POST['setor_atuacao'];

    $sqlEmpresa = "INSERT INTO empresas (usuario_id, cnpj, razao_social, setor_atuacao) VALUES (?, ?, ?, ?)";
    $stmtEmp = $conexao->prepare($sqlEmpresa);
    $stmtEmp->bind_param("isss", $usuario_id, $cnpj, $razao_social, $setor_atuacao);
    $stmtEmp->execute();
}

echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
exit;

?>
