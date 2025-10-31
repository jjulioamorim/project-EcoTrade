<?php
// Este arquivo é incluído dentro de 'Dashboard.php'
// $conexao, $id_usuario, $tipo_usuario já estão disponíveis.

// 1. Obter o ID da tabela 'produtores'
$produtor_id = null;
$stmt_find = $conexao->prepare("SELECT id FROM produtores WHERE usuario_id = ?");
$stmt_find->bind_param("i", $id_usuario);
$stmt_find->execute();
$result_find = $stmt_find->get_result();
if ($result_find->num_rows > 0) {
    $produtor_id = $result_find->fetch_assoc()['id'];
}
$stmt_find->close();

if ($produtor_id):
    // 2. Buscar créditos para saldo e lista
    $stmt_creditos = $conexao->prepare("SELECT * FROM creditos_carbono WHERE produtor_id = ? ORDER BY data_geracao DESC");
    $stmt_creditos->bind_param("i", $produtor_id);
    $stmt_creditos->execute();
    $result_creditos = $stmt_creditos->get_result();

    $creditos_aprovados = 0;
    $creditos_pendentes = 0;
    if ($result_creditos->num_rows > 0) {
        while($credito_calc = $result_creditos->fetch_assoc()) {
            if ($credito_calc['status'] == 'aprovado') {
                $creditos_aprovados += $credito_calc['quantidade'];
            } elseif ($credito_calc['status'] == 'pendente') {
                $creditos_pendentes += $credito_calc['quantidade'];
            }
        }
        $result_creditos->data_seek(0); // Resetar para o loop da tabela
    }
?>
<div class="card">
    <h2>Painel do Produtor</h2>
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="flex: 1; background: var(--secondary);">
            <h4 style="color: var(--muted-foreground); font-weight: 500;">Saldo Aprovado (Disponível)</h4>
            <p style="font-size: 1.5rem; font-weight: bold;" class="status-aprovado"><?php echo number_format($creditos_aprovados, 2, ',', '.'); ?> CO2</p>
        </div>
        <div class="card" style="flex: 1; background: var(--secondary);">
            <h4 style="color: var(--muted-foreground); font-weight: 500;">Saldo Pendente (Em Análise)</h4>
            <p style="font-size: 1.5rem; font-weight: bold;" class="status-pendente"><?php echo number_format($creditos_pendentes, 2, ',', '.'); ?> CO2</p>
        </div>
    </div>
    
    <a href="RegistrarCreditos.php" class="btn-primary" style="text-decoration: none; text-align: center; display: inline-block; width: 200px;">
        Registrar Novos Créditos
    </a>
    
    <h3 style="margin-top: 2rem;">Meus Créditos Registrados</h3>
    <table>
        <thead>
            <tr><th>Data Geração</th><th>Origem</th><th>Quantidade</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php if ($result_creditos->num_rows > 0): ?>
                <?php while($credito = $result_creditos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime(htmlspecialchars($credito['data_geracao']))); ?></td>
                        <td><?php echo htmlspecialchars($credito['origem']); ?></td>
                        <td><?php echo htmlspecialchars($credito['quantidade']); ?></td>
                        <td class="status-<?php echo htmlspecialchars($credito['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($credito['status'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; color: var(--muted-foreground);">Nenhum crédito registrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php 
$stmt_creditos->close();
else: 
?>
<div class="card"><p class="status-rejeitado">Erro: Dados do produtor não encontrados.</p></div>
<?php endif; ?>