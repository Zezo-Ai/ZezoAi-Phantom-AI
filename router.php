<?php
/**
 * TruAi Core API Router
 * 
 * Main routing file for TruAi Core API endpoints
 * © 2013 – Present My Deme, LLC
 * All Rights Reserved
 */

// Ensure session is started only once (idempotent)
function phantom_start_session() {
    if (session_status() === PHP_STATUS_NONE) {
        session_name('PHANTOM_SESSION');
        session_start();
    }
}

// Start session idempotently
phantom_start_session();

// Load autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Route the request
try {
    switch ($path) {
        case 'api/auth/session':
            require __DIR__ . '/api/auth/session.php';
            break;
            
        case 'api/audit/list':
            require __DIR__ . '/api/audit/list.php';
            break;
            
        case 'api/truai/arbitrate':
            require __DIR__ . '/api/truai/arbitrate.php';
            break;
            
        case 'api/truai/maintenance':
            require __DIR__ . '/api/truai/maintenance.php';
            break;
            
        case 'api/audit/log':
            require __DIR__ . '/api/audit/log.php';
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Endpoint not found',
                'path' => $path,
                'available_endpoints' => [
                    '/api/auth/session',
                    '/api/audit/list',
                    '/api/truai/arbitrate',
                    '/api/truai/maintenance',
                    '/api/audit/log'
                ]
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
