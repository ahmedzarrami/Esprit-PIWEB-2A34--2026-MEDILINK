<?php


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['messages']) || !isset($input['system'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$messages = array_merge(
    [['role' => 'system', 'content' => $input['system']]],
    $input['messages']
);

$payload = json_encode([
    'model'       => 'llama-3.3-70b-versatile',
    'messages'    => $messages,
    'max_tokens'  => 500,
    'temperature' => 0.7,
]);

$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY,
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => false,
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur réseau : ' . $curlError]);
    exit;
}

$decoded = json_decode($response, true);

// Erreur API Groq
if (isset($decoded['error'])) {
    $msg  = $decoded['error']['message'] ?? 'Erreur inconnue';
    $code = $decoded['error']['code']    ?? '';
    if ($httpCode === 401) $msg = 'Clé API Groq invalide. Vérifiez sur console.groq.com';
    if ($httpCode === 429) $msg = 'Quota temporairement dépassé. Réessayez dans 1 minute.';
    http_response_code(400);
    echo json_encode(['error' => $msg]);
    exit;
}

// ── Extraire le texte ──
$text = $decoded['choices'][0]['message']['content'] ?? null;

if (!$text) {
    http_response_code(500);
    echo json_encode(['error' => 'Réponse vide. Réessayez.']);
    exit;
}

// ── Retourner au format compatible avec le JS ──
http_response_code(200);
echo json_encode([
    'content' => [
        ['type' => 'text', 'text' => $text]
    ]
]);
exit;
?>