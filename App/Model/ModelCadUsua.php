<?php
// Busca os cursos do banco
$sqlCursos = "SELECT id, nome FROM cursos ORDER BY nome";
$resultCursos = $conexao->query($sqlCursos);
$opcoesCursos = '';
while ($row = $resultCursos->fetch_assoc()) {
    $opcoesCursos .= "<option value='{$row['id']}'>{$row['nome']}</option>";
}

// Busca as unidades do banco
$sqlUnidades = "SELECT id, nome FROM unidades ORDER BY nome";
$resultUnidades = $conexao->query($sqlUnidades);
$opcoesUnidades = '';
while ($row = $resultUnidades->fetch_assoc()) {
    $opcoesUnidades .= "<option value='{$row['id']}'>{$row['nome']}</option>";
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars(trim($_POST['nome']));
    $matricula = htmlspecialchars(trim($_POST['matricula']));
    $turma = htmlspecialchars(trim($_POST['turma']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];
    $curso_id = htmlspecialchars(trim($_POST['curso']));  // Agora é o ID do curso
    $semestre = htmlspecialchars(trim($_POST['semestre']));
    $unidade_id = htmlspecialchars(trim($_POST['unidade']));  // Agora é o ID da unidade
    $tipo_usuario = 'aluno';
    $turno = htmlspecialchars(trim($_POST['turno']));   
    $data_cadastro = date('Y-m-d'); // Usa a data atual

    // Verifica se todos os dados são válidos   
    if (!$nome || !$matricula || !$turma || !$email || !$senha || !$curso_id || !$semestre || !$unidade_id || !$turno) {
        $_SESSION['status'] = uniqid();
        header("Location: erroCad.php?status=" . $_SESSION['status']);
        exit;
    }
    
    // Verifica se o email já está cadastrado
    $sql_busca = "SELECT email FROM usuarios WHERE email = ?";
    if ($stmt_busca = $conexao->prepare($sql_busca)) {
        $stmt_busca->bind_param("s", $email);
        $stmt_busca->execute();
        $stmt_busca->store_result();
        
        if ($stmt_busca->num_rows > 0) {
            // Email já cadastrado
            $_SESSION['status'] = uniqid();
            header("Location: erroCad.php?status=" . $_SESSION['status']);
            exit;
        }

        $stmt_busca->close();
    } else {
        // Erro ao preparar a consulta
        $_SESSION['status'] = uniqid();
        header("Location: erroCad.php?status=" . $_SESSION['status']);
        exit;
    }

    // Criptografa a senha antes de salvar
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere os dados no banco de dados com o ID do curso e da unidade
    $sql_insert = "INSERT INTO usuarios (nome, email, senha_hash, matricula, tipo_usuario, unidade_id, curso_id, turno, turma, semestre, data_cadastro) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt_insert = $conexao->prepare($sql_insert)) {
        $stmt_insert->bind_param("sssssssssss", $nome, $email, $senha_hash, $matricula, $tipo_usuario, $unidade_id, $curso_id, $turno, $turma, $semestre, $data_cadastro);
          
        if ($stmt_insert->execute()) {
            // Cadastro realizado com sucesso
            $_SESSION['status'] = uniqid();
            header("Location: sucesso.php?status=" . $_SESSION['status']);
            exit;
        } else {
            // Erro ao inserir no banco
            $_SESSION['status'] = uniqid();
            header("Location: erroCad.php?status=" . $_SESSION['status']);
            exit;
        }

        $stmt_insert->close();
    } else {
        // Erro ao preparar a consulta
        $_SESSION['status'] = uniqid();
        header("Location: erroCad.php?status=" . $_SESSION['status']);
        exit;
    }
}
?>
