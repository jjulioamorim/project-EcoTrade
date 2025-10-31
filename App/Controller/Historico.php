<?php
include __DIR__ . '/../Model/config/Conexao.php';

// 1. Buscar transações públicas (sem dados sensíveis)
// Juntando tabelas para obter nomes, conforme schema
$sql_historico = "SELECT n.data_negociacao, n.quantidade, c.origem, p_user.nome AS nome_produtor, e_user.nome AS nome_empresa
                  FROM negociacoes n
                  JOIN creditos_carbono c ON n.credito_id = c.id
                  JOIN produtores p ON n.produtor_id = p.id
                  JOIN usuarios p_user ON p.usuario_id = p_user.id
                  JOIN empresas e ON n.empresa_id = e.id
                  JOIN usuarios e_user ON e.usuario_id = e_user.id
                  ORDER BY n.data_negociacao DESC";
$result_historico = $conexao->query($sql_historico);
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Transações - EcoTrade</title>
    <link rel="stylesheet" href="../../Public/css/stytle.css"> 
    <style>
        /* Estilos da página (copiados do Dashboard.php) */
        body { display: block; padding: 2rem; }
        .container { max-width: 75rem; margin: 0 auto; }
        .header { text-align: left; margin-bottom: 2rem; }
        .logo h1 { font-size: 2rem; }
        .subtitle { font-size: 1.125rem; color: var(--muted-foreground);}
        .card { padding: 2rem; }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <h1><span style="color: var(--primary);">Eco</span>Trade</h1>
            </div>
            <p class="subtitle">Histórico Público de Transações</p>
            <a href="Home.php" class="link" style="margin-top: 1rem; display: inline-block;">&larr; Voltar para Home</a>
        </div>

        <div class="card">
            <h2>Transações Realizadas</h2>
            <p style="color: var(--muted-foreground); margin-bottom: 1.5rem;">
                Esta é uma lista de todas as negociações concluídas na plataforma,
                conforme o Requisito 2.3 de transparência.
            </p>
            <table>
                <thead>
                    <tr>
                        <th>Data da Transação</th>
                        <th>Produtor</th>
                        <th>Empresa Compradora</th>
                        <th>Origem do Crédito</th>
                        <th>Quantidade (CO2)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_historico && $result_historico->num_rows > 0): ?>
                        <?php while($transacao = $result_historico->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date("d/m/Y H:i", strtotime(htmlspecialchars($transacao['data_negociacao']))); ?></td>
                                <td><?php echo htmlspecialchars($transacao['nome_produtor']); ?></td>
                                <td><?php echo htmlspecialchars($transacao['nome_empresa']); ?></td>
                                <td><?php echo htmlspecialchars($transacao['origem']); ?></td>
                                <td><?php echo htmlspecialchars($transacao['quantidade']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--muted-foreground);">Nenhuma transação concluída ainda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>