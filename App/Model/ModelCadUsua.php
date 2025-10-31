<?php
// Este arquivo é incluído por CadUsua.php. $conexao já existe.
$nome  = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$tipo  = $_POST['tipo']; // 'empresa', 'produtor' ou 'admin'

// Inserir usuário na tabela principal
$sqlUsuario = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->bind_param("ssss", $nome, $email, $senha, $tipo);
$stmt->execute();

if ($stmt->errno) {
    echo json_encode(['success' => false, 'message' => 'Erro ao inserir usuário: ' . $stmt->error]);
    exit;
}
$usuario_id = $stmt->insert_id;
$stmt->close();

// Inserir dados específicos conforme o tipo
if ($tipo == 'produtor') {
    $cpf = $_POST['cpf'];
    $nome_fazenda = $_POST['nomeFazenda']; // Corrigido para corresponder ao HTML
    $localizacao = $_POST['localizacao'];

    $sqlProdutor = "INSERT INTO produtores (usuario_id, cpf, nome_fazenda, localizacao) VALUES (?, ?, ?, ?)";
    $stmtProd = $conexao->prepare($sqlProdutor);
    $stmtProd->bind_param("isss", $usuario_id, $cpf, $nome_fazenda, $localizacao);
    $stmtProd->execute();
    $stmtProd->close();

} elseif ($tipo == 'empresa') {
    $cnpj = $_POST['cnpj'];
    $razao_social = $_POST['razaoSocial']; // Corrigido para corresponder ao HTML
    $setor_atuacao = $_POST['setorAtuacao']; // Corrigido para corresponder ao HTML

    $sqlEmpresa = "INSERT INTO empresas (usuario_id, cnpj, razao_social, setor_atuacao) VALUES (?, ?, ?, ?)";
    $stmtEmp = $conexao->prepare($sqlEmpresa);
    $stmtEmp->bind_param("isss", $usuario_id, $cnpj, $razao_social, $setor_atuacao);
    $stmtEmp->execute();
    $stmtEmp->close();
}

echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
$conexao->close();
exit;
?>