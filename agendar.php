<?php
require 'config.php';
require 'vendor/autoload.php';
redirecionarSeNaoLogado();

header('Content-Type: text/plain');

$titulo = trim($_POST['titulo'] ?? '');
$data   = $_POST['data'] ?? '';
$hora   = $_POST['hora'] ?? '';

if (!$titulo || !$data || !$hora) {
    echo 'Erro: preencha todos os campos';
    exit;
}

try {
    // Salva no banco
    $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, titulo, data, hora) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $titulo, $data, $hora]);
    $agendamentoId = $pdo->lastInsertId();

    // === ENVIA PRO GOOGLE CALENDAR ===
    $client = new Google_Client();
    $client->setAuthConfig('google-credentials.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);

    $service = new Google_Service_Calendar($client);

    $calendarId = 'SEU_ID_DO_CALENDARIO_AQUI'; // ← MUDE AQUI!!

    $start = new Google_Service_Calendar_EventDateTime();
    $start->setDateTime("{$data}T{$hora}:00");
    $start->setTimeZone('America/Sao_Paulo');

    $end = new Google_Service_Calendar_EventDateTime();
    $endTime = date('H:i:s', strtotime($hora) + 3600); // +1h
    $end->setDateTime("{$data}T{$endTime}");
    $end->setTimeZone('America/Sao_Paulo');

    $event = new Google_Service_Calendar_Event();
    $event->setSummary($titulo . " - Bispado");
    $event->setDescription("Agendado pelo sistema de entrevistas");
    $event->setStart($start);
    $event->setEnd($end);

    $createdEvent = $service->events->insert($calendarId, $event);

    // Salva o ID do evento do Google no banco (pra editar/excluir depois)
    $pdo->prepare("UPDATE agendamentos SET google_event_id = ? WHERE id = ?")
        ->execute([$createdEvent->getId(), $agendamentoId]);

    echo 'OK';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
