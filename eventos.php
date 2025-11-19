<?php
require 'config.php';
redirecionarSeNaoLogado();

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.titulo, a.data, a.hora, u.nome 
        FROM agendamentos a 
        JOIN usuarios u ON a.usuario_id = u.id 
        WHERE a.usuario_id = ?
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($eventos as $e) {
        $result[] = [
            'id'    => $e['id'],
            'title' => $e['titulo'] . ' - ' . $e['nome'],
            'start' => $e['data'] . 'T' . $e['hora']
        ];
    }

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([]);
}
?>