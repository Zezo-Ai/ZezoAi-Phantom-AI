<?php
/**
 * Audit List API Endpoint
 * 
 * Returns audit log entries with optional filtering
 */

use PhantomAI\Core\AuditLogger;

// Session already started by router.php
$response = [];

try {
    // Get query parameters for filtering
    $filters = [];
    
    if (isset($_GET['type'])) {
        $filters['type'] = $_GET['type'];
    }
    
    if (isset($_GET['user_id'])) {
        $filters['user_id'] = $_GET['user_id'];
    }
    
    // Get audit entries
    $entries = AuditLogger::getAuditEntries($filters);
    
    // Apply pagination if requested
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
    
    $total = count($entries);
    $offset = ($page - 1) * $perPage;
    $pagedEntries = array_slice($entries, $offset, $perPage);
    
    $response = [
        'success' => true,
        'entries' => $pagedEntries,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ],
        'filters' => $filters
    ];
    
    http_response_code(200);
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Failed to retrieve audit entries',
        'message' => $e->getMessage()
    ];
    
    http_response_code(500);
}

echo json_encode($response);
