<?php
/**
 * Audit Log API Endpoint
 * 
 * Logs audit entries from the frontend
 */

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
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Determine log type and call appropriate logger
    $logType = $input['type'] ?? 'ai_interaction';
    
    switch ($logType) {
        case 'ai_interaction':
            $success = AuditLogger::logAIInteraction($input);
            break;
            
        case 'maintenance_operation':
            $success = AuditLogger::logMaintenanceOperation($input);
            break;
            
        case 'setting_proposal':
            $success = AuditLogger::logSettingProposal($input);
            break;
            
        case 'copilot_escalation':
            $success = AuditLogger::logCopilotEscalation($input);
            break;
            
        default:
            throw new Exception('Unknown log type: ' . $logType);
    }
    
    if ($success) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Audit entry logged successfully'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to log audit entry (missing required fields)'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to log audit entry',
        'message' => $e->getMessage()
    ]);
}
