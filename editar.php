<?php
require 'config.php';
require 'vendor/autoload.php';
redirecionarSeNaoLogado();

header('Content-Type: text/plain');

$id = $_POST['id'] ?? 0;
$titulo = trim($_POST['titulo'] ?? '');
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';

if (!$id || !$titulo || !$data || !$hora) {
    echo 'Erro: dados inválidos';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT google_event_id FROM agendamentos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
    $row = $stmt->fetch();

    // Atualiza no banco
    $pdo->prepare("UPDATE agendamentos SET titulo=?, data=?, hora=? WHERE id=? AND usuario_id=?")
        ->execute([$titulo, $data, $hora, $id, $_SESSION['usuario_id']]);

    // Atualiza no Google
    if ($row && $row['google_event_id']) {
        $client = new Google_Client();
        $client->setAuthConfig('google-credentials.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $service = new Google_Service_Calendar($client);

        $calendarId = 'SEU_ID_DO_CALENDARIO_AQUI'; // ← MUDE AQUI!!

        $event = $service->events->get($calendarId, $row['google_event_id']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime("{$data}T{$hora}:00");
        $start->setTimeZone('America/Sao_Paulo');
        $event->setStart($start);

        $endTime = date('H:i:s', strtotime($hora) + 3600);
        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime("{$data}T{$endTime}");
        $end->setTimeZone('America/Sao_Paulo');
        $event->setEnd($end);

        $event->setSummary($titulo . " - Bispado");

        $service->events->update($calendarId, $event->getId(), $event);
    }

    echo 'OK';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
