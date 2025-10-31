<?php
// Este arquivo é incluído dentro de 'Dashboard.php'
// $conexao, $id_usuario, $tipo_usuario já estão disponíveis.

// 1. Buscar créditos pendentes para validação
$sql_pendentes = "SELECT c.*, u.nome AS nome_produtor 
                  FROM creditos_carbono c
                  JOIN produtores p ON c.produtor_id = p.id
                  JOIN usuarios u ON p.usuario_id = u.id
                  WHERE c.status = 'pendente'
                  ORDER BY c.data_geracao ASC";
$result_pendentes = $conexao->query($sql_pendentes);

// 2. Buscar transações recentes (Dashboard Req 1.4)
$sql_recentes = "SELECT n.*, u_emp.nome AS nome_empresa, c.origem
                 FROM negociacoes n
                 JOIN empresas e ON n.empresa_id = e.id
                 JOIN usuarios u_emp ON e.usuario_id = u_emp.id
                 JOIN creditos_carbono c ON n.credito_id = c.id
                 ORDER BY n.data_negociacao DESC
                 LIMIT 10";
$result_recentes = $conexao->query($sql_recentes);
?>
<div class="card">
    <h2>Painel do Administrador</h2>
    <p>Aprove ou rejeite os créditos de carbono pendentes de validação.</p>
    
    <h3 style="margin-top: 2rem;">Créditos Pendentes de Validação</h3>
    <table>
        <thead>
            <tr><th>Data</th><th>Produtor</th><th>Origem</th><th>Qtd</th><th>Ação</th></tr>
        </thead>
        <tbody>
            <?php if ($result_pendentes && $result_pendentes->num_rows > 0): ?>
                <?php while($credito = $result_pendentes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d/m/Y", strtotime(htmlspecialchars($credito['data_geracao']))); ?></td>
                        <td><?php echo htmlspecialchars($credito['nome_produtor']); ?></td>
                        <td><?php echo htmlspecialchars($credito['origem']); ?></td>
                        <td><?php echo htmlspecialchars($credito['quantidade']); ?></td>
                        <td>
                            <form action="ValidarCreditos.php" method="POST" style="display: flex; gap: 0.5rem;">
                                <input type="hidden" name="credito_id" value="<?php echo $credito['id']; ?>">
                                <button type="submit" name="acao" value="aprovado" class="btn-small btn-approve">Aprovar</button>
                                <button type="submit" name="acao" value="rejeitado" class="btn-small btn-reject">Rejeitar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; color: var(--muted-foreground);">Nenhum crédito pendente.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3 style="margin-top: 2rem;">Últimas Transações (Dashboard Req 1.4)</h3>
    <table>
        <thead>
            <tr><th>Data</th><th>Empresa</th><th>Origem Crédito</th><th>Qtd</th></tr>
        </thead>
        <tbody>
            <?php if ($result_recentes && $result_recentes->num_rows > 0): ?>
                <?php while($transacao = $result_recentes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d/m/Y H:i", strtotime(htmlspecialchars($transacao['data_negociacao']))); ?></td>
                        <td><?php echo htmlspecialchars($transacao['nome_empresa']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['origem']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['quantidade']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align: center; color: var(--muted-foreground);">Nenhuma transação recente.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>