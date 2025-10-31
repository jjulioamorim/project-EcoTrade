<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// 1. Proteção: Apenas Empresas
if (!isset($_SESSION['logado']) || $_SESSION['tipo_usuario'] !== 'empresa') {
    header('Location: Login.php');
    exit;
}

// 2. Verificar se os dados vieram via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credito_id'])) {
    
    include __DIR__ . '/../Model/config/Conexao.php';

    $credito_id = $_POST['credito_id'];
    $produtor_id = $_POST['produtor_id'];
    $quantidade = $_POST['quantidade']; // Quantidade total do lote
    $usuario_id_logado = $_SESSION['id_usuario'];
    $empresa_id = null;

    // 3. Obter o ID da tabela 'empresas'
    $stmt_find = $conexao->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
    $stmt_find->bind_param("i", $usuario_id_logado);
    $stmt_find->execute();
    $result_find = $stmt_find->get_result();
    if ($result_find->num_rows > 0) {
        $empresa_id = $result_find->fetch_assoc()['id'];
    }
    $stmt_find->close();

    if ($empresa_id) {
        // Iniciar transação
        $conexao->begin_transaction();
        
        try {
            // 4. Mudar status do crédito para 'vendido'
            $stmt_update = $conexao->prepare("UPDATE creditos_carbono SET status = 'vendido' WHERE id = ? AND status = 'aprovado'");
            $stmt_update->bind_param("i", $credito_id);
            $stmt_update->execute();

            // 5. Se a atualização foi bem-sucedida, registrar na tabela 'negociacoes'
            if ($stmt_update->affected_rows > 0) {
                // Valor fictício, pois não foi definido no requisito
                $valor_ficticio = $quantidade * 50; // Ex: R$ 50 por crédito

                $sql_insert = "INSERT INTO negociacoes (produtor_id, empresa_id, credito_id, quantidade, valor) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conexao->prepare($sql_insert);
                $stmt_insert->bind_param("iiidd", $produtor_id, $empresa_id, $credito_id, $quantidade, $valor_ficticio);
                $stmt_insert->execute();
                
                // Commit da transação
                $conexao->commit();
                $stmt_insert->close();
            } else {
                // Se 'affected_rows' for 0, o crédito já foi vendido ou não estava aprovado.
                $conexao->rollback();
            }
            $stmt_update->close();

        } catch (Exception $e) {
            $conexao->rollback(); // Desfaz em caso de erro
        }
    }
    $conexao->close();
}

// 6. Redirecionar de volta para o Dashboard
header('Location: Dashboard.php');
exit;
?>