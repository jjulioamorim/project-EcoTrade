<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1. Proteger a página: Verificar se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: Login.php');
    exit;
}

// 2. Incluir conexão (será usada pelos includes)
include __DIR__ . '/../Model/config/Conexao.php';

// 3. Obter dados da sessão
$nome = $_SESSION['nome'];
$tipo_usuario = $_SESSION['tipo_usuario'];
$id_usuario = $_SESSION['id_usuario']; // Essencial para as views

// 4. Carregar o conteúdo específico baseado no tipo de usuário
$conteudo_dashboard = '';

if ($tipo_usuario == 'produtor') {
    ob_start();
    include '../View/dashboard/dashboard_produtor.php';
    $conteudo_dashboard = ob_get_clean();
} elseif ($tipo_usuario == 'empresa') {
    ob_start();
    include '../View/dashboard/dashboard_empresa.php';
    $conteudo_dashboard = ob_get_clean();
} elseif ($tipo_usuario == 'admin') {
    ob_start();
    include '../View/dashboard/dashboard_admin.php';
    $conteudo_dashboard = ob_get_clean();
}

// Fechar a conexão aberta no início (e usada pelos includes)
$conexao->close();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EcoTrade</title>
    <link rel="stylesheet" href="../../Public/css/stytle.css">
    <style>
        body { display: block; padding: 0; }
        .navbar {
            background-color: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: var(--primary); text-decoration: none; }
        .navbar-user { color: var(--muted-foreground); }
        .navbar-user a { color: var(--destructive); text-decoration: none; margin-left: 1rem; }
        .dashboard-container {
            width: 100%;
            max-width: 75rem; /* 1200px */
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .welcome-header h1 { font-size: 2rem; font-weight: 600; }
        .welcome-header p { font-size: 1.125rem; color: var(--muted-foreground); margin-bottom: 1.5rem;}
        
        /* Estilos da Tabela */
        table {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 1rem;
        }
        table th, table td {
            padding: 0.75rem; 
            text-align: left;
            border-bottom: 1px solid var(--secondary);
        }
        table th {
            border-bottom-width: 2px;
            border-color: var(--border);
            color: var(--muted-foreground);
            font-size: 0.875rem;
            text-transform: uppercase;
        }
        table tr:last-child td { border-bottom: none; }
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            border: none;
            cursor: pointer;
        }
        .btn-approve { background-color: var(--primary); color: var(--primary-foreground); }
        .btn-reject { background-color: var(--destructive); color: var(--foreground); }
        .status-pendente { color: #e0b041; }
        .status-aprovado { color: var(--primary); }
        .status-vendido { color: var(--muted-foreground); }
        .status-rejeitado { color: var(--destructive); }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="Dashboard.php" class="navbar-brand">EcoTrade</a>
        <div class="navbar-user">
            Olá,<a href="Perfil.php"> <strong><?php echo htmlspecialchars($nome); ?></strong></a>
            (<?php echo htmlspecialchars($tipo_usuario); ?>)
            <a href="Logout.php">Sair</a>
        </div>
    </nav>
    <div class="dashboard-container">
        <div class="welcome-header">
            <h1>Seu Dashboard</h1>
            <p>Gerencie seus créditos de carbono e transações.</p>
        </div>
        <div class="content-wrapper">
            <?php echo $conteudo_dashboard; ?>
        </div>
    </div>
</body>
</html>