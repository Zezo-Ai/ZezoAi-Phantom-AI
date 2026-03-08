<?php
/**
 * TruAi Arbitration API Endpoint
 * 
 * Routes tasks to appropriate AI sources
 */

use PhantomAI\Core\TruAiCore;
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
    
    if (!$input || !isset($input['task_type'])) {
        throw new Exception('Missing required field: task_type');
    }
    
    $taskType = $input['task_type'];
    $context = $input['context'] ?? [];
    
    // Create TruAi Core instance
    $truAi = new TruAiCore();
    
    // Arbitrate AI source
    $result = $truAi->arbitrateAISource($taskType, $context);
    
    // Log the arbitration
    if (isset($_SESSION['user_id'])) {
        AuditLogger::logAIInteraction([
            'user_id' => $_SESSION['user_id'],
            'input_text' => $input['description'] ?? $taskType,
            'context_items' => $context['files'] ?? [],
            'ai_tier' => $context['tier'] ?? 'mid',
            'ai_source' => $result['source'],
            'outcome' => 'arbitrated'
        ]);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'task_type' => $taskType,
        'recommended_source' => $result['source'],
        'reason' => $result['reason'],
        'context' => $context
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to arbitrate AI source',
        'message' => $e->getMessage()
    ]);
}
