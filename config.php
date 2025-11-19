<?php
session_start();

// CONFIGURAÇÃO QUE FUNCIONA EM 100% DOS CASOS NO XAMPP
$host = '127.0.0.1';        // mudei de localhost para 127.0.0.1
$port = '3306';             // porta padrão do MySQL
$dbname = 'entrevistas';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (Exception $e) {
    die("Erro de conexão com o banco: " . $e->getMessage());
}

// Funções de segurança
function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function redirecionarSeNaoLogado() {
    if (!estaLogado()) {
        header("Location: index.php");
        exit;
    }
}
?>