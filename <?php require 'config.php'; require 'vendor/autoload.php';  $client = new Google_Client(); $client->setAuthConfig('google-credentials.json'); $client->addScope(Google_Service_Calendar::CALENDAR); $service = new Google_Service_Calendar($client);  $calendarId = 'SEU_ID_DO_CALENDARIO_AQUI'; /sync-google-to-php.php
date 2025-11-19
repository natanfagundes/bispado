<?php
require 'config.php';
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('google-credentials.json');
$client->addScope(Google_Service_Calendar::CALENDAR);
$service = new Google_Service_Calendar($client);

$calendarId = 'SEU_ID_DO_CALENDARIO_AQUI'; // ← MUDE AQUI!!

$events = $service->events->listEvents($calendarId, ['timeMin' => date('c')])->getItems();

foreach ($events as $event) {
    if (strpos($event->getSummary(), 'Bispado') === false) continue;

    $titulo = str_replace(' - Bispado', '', $event->getSummary());
    $data = substr($event->getStart()->dateTime, 0, 10);
    $hora = substr($event->getStart()->dateTime, 11, 8);

    $googleId = $event->getId();

    $stmt = $pdo->prepare("SELECT id FROM agendamentos WHERE google_event_id = ?");
    $stmt->execute([$googleId]);
    if ($stmt->rowCount() == 0) {
        $pdo->prepare("INSERT INTO agendamentos (usuario_id, titulo, data, hora, google_event_id) VALUES (1, ?, ?, ?, ?)")
            ->execute([$titulo, $data, $hora, $googleId]);
    }
}
echo 'Sync OK';
?>
