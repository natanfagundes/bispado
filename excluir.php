<?php
require 'config.php';
redirecionarSeNaoLogado();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id=? AND usuario_id=?");
$stmt->execute([$id, $_SESSION['usuario_id']]);

header("Location: dashboard.php");
?>