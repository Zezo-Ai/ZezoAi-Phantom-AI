<?php
/**
 * TruAi Maintenance API Endpoint
 * 
 * Processes maintenance commands
 */

use PhantomAI\Core\MaintenanceController;
use PhantomAI\Core\AuditLogger;

// Session already started by router.php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['command'])) {
        throw new Exception('Missing required field: command');
    }
    
    $command = $input['command'];
    $userId = $_SESSION['user_id'] ?? 'anonymous';
    
    // Create maintenance controller
    $controller = new MaintenanceController();
    
    // Check if maintenance mode should be enabled
    if (isset($input['maintenance_mode']) && $input['maintenance_mode'] === true) {
        $controller->setMaintenanceMode(true);
    }
    
    // Process the command
    $result = $controller->processMaintenanceCommand($command, [
        'user_id' => $userId
    ]);
    
    // Return appropriate status code
    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process maintenance command',
        'message' => $e->getMessage()
    ]);
}
