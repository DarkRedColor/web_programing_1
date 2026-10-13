<?php
use App\Models\Joke;

require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['ok' => false]));
}

csrf_check();

$joke = Joke::find((int) ($_POST['id'] ?? 0));

if (!$joke) {
    http_response_code(404);
    exit(json_encode(['ok' => false]));
}

$joke->delete();
echo json_encode(['ok' => true]);