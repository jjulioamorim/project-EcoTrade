<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Proteção: Apenas produtores logados
if (!isset($_SESSION['logado']) || $_SESSION['tipo_usuario'] !== 'produtor') {
    header('Location: Login.php');
    exit;
}

include __DIR__ . '/../Model/config/Conexao.php';
$mensagem = '';

// Se o formulário foi enviado (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Obter dados do formulário
    $quantidade = $_POST['quantidade'];
    $origem = $_POST['origem'];
    $data_geracao = $_POST['data_geracao'];
    $usuario_id_logado = $_SESSION['id_usuario'];
    $produtor_id = null;

    // 2. Obter o ID da tabela 'produtores' (baseado no usuario_id da sessão)
    $stmt_find = $conexao->prepare("SELECT id FROM produtores WHERE usuario_id = ?");
    $stmt_find->bind_param("i", $usuario_id_logado);
    $stmt_find->execute();
    $result_find = $stmt_find->get_result();

    if ($result_find->num_rows > 0) {
        $produtor_id = $result_find->fetch_assoc()['id'];
    }
    $stmt_find->close();

    // 3. Inserir na tabela 'creditos_carbono'
    if ($produtor_id) {
        $sql_insert = "INSERT INTO creditos_carbono (produtor_id, quantidade, origem, data_geracao, status) 
                       VALUES (?, ?, ?, ?, 'pendente')";
        $stmt_insert = $conexao->prepare($sql_insert);
        $stmt_insert->bind_param("idss", $produtor_id, $quantidade, $origem, $data_geracao);

        if ($stmt_insert->execute()) {
            $mensagem = "<div style='color: var(--primary); background: var(--secondary); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;'>
                            Créditos registrados com sucesso! Aguardando validação.
                         </div>";
        } else {
            $mensagem = "<div style='color: var(--destructive); background: var(--secondary); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;'>Erro: " . $conexao->error . "</div>";
        }
        $stmt_insert->close();
    } else {
        $mensagem = "<div style='color: var(--destructive); background: var(--secondary); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;'>Erro: Produtor não encontrado.</div>";
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Créditos - EcoTrade</title>
    <link rel="stylesheet" href="../../Public/css/stytle.css">
    <style>
        /* Estilos da página (copiados do Dashboard.php) */
        body { display: block; padding: 0; }
        .navbar { background-color: var(--card); border-bottom: 1px solid var(--border); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: var(--primary); text-decoration: none; }
        .navbar-user { color: var(--muted-foreground); }
        .navbar-user a { color: var(--destructive); text-decoration: none; margin-left: 1rem; }
        .dashboard-container { width: 100%; max-width: 75rem; margin: 2rem auto; padding: 0 2rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="Dashboard.php" class="navbar-brand">EcoTrade</a>
        <div class="navbar-user">
            Olá, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong>
            <a href="Logout.php">Sair</a>
        </div>
    </nav>
    <div class="dashboard-container">
        <div class="card" style="max-width: 42rem; margin: 0 auto;">
            <a href="Dashboard.php" class="link" style="margin-bottom: 1.5rem; display: inline-block;">&larr; Voltar ao Dashboard</a>
            <h2>Registrar Novos Créditos de Carbono</h2>
            <?php echo $mensagem; ?>
            
            <form action="RegistrarCreditos.php" method="POST">
                <div class="form-group">
                    <label for="quantidade">Quantidade (Toneladas CO2)</label>
                    <input type="number" step="0.01" id="quantidade" name="quantidade" placeholder="Ex: 150.50" required>
                </div>
                <div class="form-group">
                    <label for="origem">Origem dos Créditos</label>
                    <input type="text" id="origem" name="origem" placeholder="Ex: Reflorestamento, Energia Limpa" required>
                </div>
                <div class="form-group">
                    <label for="data_geracao">Data de Geração</label>
                    <input type="date" id="data_geracao" name="data_geracao" required>
                </div>
                <button type="submit" class="btn-primary">Enviar para Validação</button>
            </form>
        </div>
    </div>
</body>
</html>