<?php
require 'config.php';
redirecionarSeNaoLogado();

header('Content-Type: text/plain');

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'Erro: ID inválido';
    exit;
}

$id     = (int)$_POST['id'];
$titulo = trim($_POST['titulo'] ?? '');
$data   = $_POST['data'] ?? '';
$hora   = $_POST['hora'] ?? '';

if ($titulo === '' || $data === '' || $hora === '') {
    echo 'Erro: preencha todos os campos';
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE agendamentos SET titulo = ?, data = ?, hora = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $data, $hora, $id, $_SESSION['usuario_id']]);
    echo 'OK';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>