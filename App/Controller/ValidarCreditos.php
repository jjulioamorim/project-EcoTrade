<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// 1. Proteção: Apenas Admins
if (!isset($_SESSION['logado']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: Login.php');
    exit;
}
// 2. Verificar se os dados vieram via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credito_id']) && isset($_POST['acao'])) {
    
    include __DIR__ . '/../Model/config/Conexao.php';

    $credito_id = $_POST['credito_id'];
    $admin_id = $_SESSION['id_usuario']; // ID do admin logado (da tabela 'usuarios')
    $status = ($_POST['acao'] == 'aprovado') ? 'aprovado' : 'rejeitado';

    // 3. Atualizar o status na tabela 'creditos_carbono'
    $stmt_update = $conexao->prepare("UPDATE creditos_carbono SET status = ? WHERE id = ?");
    $stmt_update->bind_param("si", $status, $credito_id);
    $stmt_update->execute();
    
    // 4. Registrar a validação na tabela 'validacoes'
    $stmt_log = $conexao->prepare("INSERT INTO validacoes (credito_id, admin_id, status) VALUES (?, ?, ?)");
    $stmt_log->bind_param("iis", $credito_id, $admin_id, $status);
    $stmt_log->execute();

    $stmt_update->close();
    $stmt_log->close();
    $conexao->close();
}

// 5. Redirecionar de volta para o Dashboard
header('Location: Dashboard.php');
exit;
?>