<?php
// Este arquivo é incluído dentro de 'Dashboard.php'
// $conexao, $id_usuario, $tipo_usuario já estão disponíveis.

// 1. Obter o ID da tabela 'empresas'
$empresa_id = null;
$stmt_find = $conexao->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
$stmt_find->bind_param("i", $id_usuario);
$stmt_find->execute();
$result_find = $stmt_find->get_result();
if ($result_find->num_rows > 0) {
    $empresa_id = $result_find->fetch_assoc()['id'];
}
$stmt_find->close();

// 2. Buscar créditos APROVADOS para o marketplace
$sql_aprovados = "SELECT c.*, u.nome AS nome_produtor 
                  FROM creditos_carbono c
                  JOIN produtores p ON c.produtor_id = p.id
                  JOIN usuarios u ON p.usuario_id = u.id
                  WHERE c.status = 'aprovado'
                  ORDER BY c.data_geracao ASC";
$result_aprovados = $conexao->query($sql_aprovados);

// 3. Buscar transações recentes DA EMPRESA (Req 1.4)
$sql_recentes = "SELECT n.*, c.origem, p_user.nome as nome_produtor
                 FROM negociacoes n
                 JOIN creditos_carbono c ON n.credito_id = c.id
                 JOIN produtores p ON n.produtor_id = p.id
                 JOIN usuarios p_user ON p.usuario_id = p_user.id
                 WHERE n.empresa_id = ?
                 ORDER BY n.data_negociacao DESC
                 LIMIT 10";
$stmt_recentes = $conexao->prepare($sql_recentes);
$stmt_recentes->bind_param("i", $empresa_id);
$stmt_recentes->execute();
$result_recentes = $stmt_recentes->get_result();

// 4. Calcular Saldo de Créditos Comprados (Req 1.4)
$saldo_comprado = 0;
$sql_saldo = $conexao->prepare("SELECT SUM(quantidade) AS total FROM negociacoes WHERE empresa_id = ?");
$sql_saldo->bind_param("i", $empresa_id);
$sql_saldo->execute();
$result_saldo = $sql_saldo->get_result();
if($result_saldo->num_rows > 0) {
    $saldo_comprado = $result_saldo->fetch_assoc()['total'] ?? 0;
}
$sql_saldo->close();

if ($empresa_id):
?>
<div class="card">
    <h2>Painel da Empresa</h2>
    
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="flex: 1; background: var(--secondary);">
            <h4 style="color: var(--muted-foreground); font-weight: 500;">Total de Créditos Adquiridos</h4>
            <p style="font-size: 1.5rem; font-weight: bold;" class="status-aprovado"><?php echo number_format($saldo_comprado, 2, ',', '.'); ?> CO2</p>
        </div>
    </div>
    
    <h3 style="margin-top: 2rem;">Marketplace (Créditos Disponíveis)</h3>
    <table>
        <thead>
            <tr><th>Data</th><th>Produtor</th><th>Origem</th><th>Qtd Disponível</th><th>Ação</th></tr>
        </thead>
        <tbody>
            <?php if ($result_aprovados && $result_aprovados->num_rows > 0): ?>
                <?php while($credito = $result_aprovados->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime(htmlspecialchars($credito['data_geracao']))); ?></td>
                        <td><?php echo htmlspecialchars($credito['nome_produtor']); ?></td>
                        <td><?php echo htmlspecialchars($credito['origem']); ?></td>
                        <td><?php echo htmlspecialchars($credito['quantidade']); ?></td>
                        <td>
                            <form action="ComprarCreditos.php" method="POST">
                                <input type="hidden" name="credito_id" value="<?php echo $credito['id']; ?>">
                                <input type="hidden" name="produtor_id" value="<?php echo $credito['produtor_id']; ?>">
                                <input type="hidden" name="quantidade" value="<?php echo $credito['quantidade']; ?>">
                                <button type="submit" class="btn-small btn-approve">Comprar Lote</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: var(--muted-foreground);">Nenhum crédito disponível no momento.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin-top: 2rem;">Meu Histórico de Compras (Req 1.4)</h3>
    <table>
        <thead>
            <tr><th>Data Compra</th><th>Produtor</th><th>Origem Crédito</th><th>Qtd</th></tr>
        </thead>
        <tbody>
            <?php if ($result_recentes && $result_recentes->num_rows > 0): ?>
                <?php while($transacao = $result_recentes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d/m/Y H:i", strtotime(htmlspecialchars($transacao['data_negociacao']))); ?></td>
                        <td><?php echo htmlspecialchars($transacao['nome_produtor']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['origem']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['quantidade']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; color: var(--muted-foreground);">Nenhuma compra realizada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php 
$stmt_recentes->close();
else: 
?>
<div class="card"><p class="status-rejeitado">Erro: Dados da empresa não encontrados.</p></div>
<?php endif; ?>