<?php
require 'config.php';
require 'vendor/autoload.php';
redirecionarSeNaoLogado();

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo 'Erro';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT google_event_id FROM agendamentos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['usuario_id']]);
    $row = $stmt->fetch();

    if ($row && $row['google_event_id']) {
        $client = new Google_Client();
        $client->setAuthConfig('google-credentials.json');
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $service = new Google_Service_Calendar($client);

        $calendarId = 'SEU_ID_DO_CALENDARIO_AQUI'; // ← MUDE AQUI!!
        $service->events->delete($calendarId, $row['google_event_id']);
    }

    $pdo->prepare("DELETE FROM agendamentos WHERE id = ? AND usuario_id = ?")
        ->execute([$id, $_SESSION['usuario_id']]);

    header("Location: dashboard.php");
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
