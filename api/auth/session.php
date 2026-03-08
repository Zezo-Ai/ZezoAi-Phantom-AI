<?php
/**
 * Session Management API Endpoint
 * 
 * Handles session information and authentication status
 */

// Session already started by router.php via phantom_start_session()

$response = [
    'success' => true,
    'session_id' => session_id(),
    'session_name' => session_name(),
    'session_status' => session_status() === PHP_SESSION_ACTIVE ? 'active' : 'inactive',
    'timestamp' => date('Y-m-d H:i:s')
];

// Add user info if authenticated
if (isset($_SESSION['user_id'])) {
    $response['user'] = [
        'id' => $_SESSION['user_id'],
        'authenticated' => true
    ];
} else {
    $response['user'] = [
        'authenticated' => false
    ];
}

http_response_code(200);
echo json_encode($response);
