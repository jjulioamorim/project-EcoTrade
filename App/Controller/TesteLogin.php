<?php
// Verifica se a sessão já foi iniciada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['logado']) && $_SESSION['logado'] == true) {
    header('Location: Dashboard.php'); // Redireciona para o novo Dashboard
    exit;
} else {
    if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
        include '../Model/config/Conexao.php'; 

        $email = trim($_POST['email']);
        $senha = $_POST['senha'];
        
        include '../Model/ModelLogin.php'; 

        // $result é criado dentro de ModelLogin.php
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verifica a senha com password_verify
            if (password_verify($senha, $row["senha"])) {
                $_SESSION['id_usuario'] = $row['id'];
                $_SESSION['nome'] = $row['nome'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['tipo_usuario'] = $row['tipo'];
                $_SESSION['logado'] = true;
                header('Location: Dashboard.php'); // Sucesso, vai para o Dashboard
                exit;
            } else {
                // Senha incorreta
                header("Location: Login.php?erro=senha");
                exit();
            }
        } else {
            // Email não encontrado
            header("Location: Login.php?erro=email");
            exit();
        }
    } else {
        header('Location: Login.php');
        exit();
    }
}
?>