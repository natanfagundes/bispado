<?php
require 'config.php';
$mensagem = '';

if ($_POST) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
    $stmt->execute([$email, $senha]);   // compara senha em texto puro
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['usuario_id']   = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        header("Location: dashboard.php");
        exit;
    } else {
        $mensagem = "Email ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-5 text-center">
                    <h3>Login</h3>
                    <?php if($mensagem) echo "<div class='alert alert-danger'>$mensagem</div>"; ?>
                    <form method="post">
                        <input type="email" name="email" class="form-control mb-3" value="joao@teste.com" required>
                        <input type="password" name="senha" class="form-control mb-3" value="123456" required>
                        <button type="submit" class="btn btn-success btn-lg w-100">ENTRAR AGORA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>