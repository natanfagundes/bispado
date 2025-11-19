<?php
require 'config.php';
redirecionarSeNaoLogado();

header('Content-Type: text/plain');

$titulo = trim($_POST['titulo'] ?? '');
$data   = $_POST['data'] ?? '';
$hora   = $_POST['hora'] ?? '';

if ($titulo === '' || $data === '' || $hora === '') {
    echo 'Erro: preencha todos os campos';
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, titulo, data, hora) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $titulo, $data, $hora]);
    echo 'OK';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>