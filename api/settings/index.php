<?php
/**
 * Phantom.ai Settings API Endpoint
 *
 * GET  /api/settings  — Load settings from session
 * POST /api/settings  — Save settings to session
 *
 * AI API keys are stored in the server session only (never logged).
 */

// Session already started by router.php via phantom_start_session()

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Return settings from session (never return raw keys in production — omit if needed)
    $settings = isset($_SESSION['phantom_settings']) ? $_SESSION['phantom_settings'] : [];

    // Redact API keys from GET response for safety; client uses its own localStorage copy
    $safe = $settings;
    if (isset($safe['openaiApiKey']))    $safe['openaiApiKey']    = $settings['openaiApiKey'] ? '••••••••' : '';
    if (isset($safe['anthropicApiKey'])) $safe['anthropicApiKey'] = $settings['anthropicApiKey'] ? '••••••••' : '';

    http_response_code(200);
    echo json_encode(['success' => true, 'settings' => $safe]);

} elseif ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $incoming = isset($body['settings']) ? $body['settings'] : $body;

    // Allowlist only known setting keys
    $allowed = [
        'username', 'theme', 'fontSize', 'keybinding', 'defaultModel',
        'openaiApiKey', 'anthropicApiKey', 'streaming', 'dataSharing'
    ];

    $current = isset($_SESSION['phantom_settings']) ? $_SESSION['phantom_settings'] : [];

    foreach ($allowed as $key) {
        if (array_key_exists($key, $incoming)) {
            // Basic sanitization
            $val = $incoming[$key];
            if (is_string($val)) {
                $val = trim(htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            } elseif (is_numeric($val)) {
                $val = $val + 0;
            } elseif (is_bool($val)) {
                $val = (bool)$val;
            }
            $current[$key] = $val;
        }
    }

    $_SESSION['phantom_settings'] = $current;

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Settings saved']);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
