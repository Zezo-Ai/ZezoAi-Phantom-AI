<?php
/**
 * TruAi Core Integration Example
 * 
 * Demonstrates how to integrate TruAi Core with existing Phantom.ai workflow
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhantomAI\Core\TruAiCore;
use PhantomAI\Core\AuditLogger;
use PhantomAI\Core\MaintenanceController;

// Example 1: AI Source Arbitration
echo "=== Example 1: AI Source Arbitration ===\n\n";

$truAi = new TruAiCore();

// Classify different task types
$taskTypes = [
    'code_review' => 'Review authentication logic in auth.php',
    'production_code' => 'Generate a WordPress block for product listings',
    'planning' => 'Plan architecture for new feature',
    'refactor' => 'Refactor dashboard components'
];

foreach ($taskTypes as $type => $description) {
    $result = $truAi->arbitrateAISource($type, ['description' => $description]);
    echo "Task: {$description}\n";
    echo "Type: {$type}\n";
    echo "Recommended AI Source: {$result['source']}\n";
    echo "Reason: {$result['reason']}\n\n";
}

// Example 2: Maintenance Authorization
echo "\n=== Example 2: Maintenance Authorization ===\n\n";

$truAi->setMaintenanceMode(true);

$maintenanceRequest = [
    'command' => 'Upgrade dashboard layout',
    'summary' => 'Modernize the dashboard UI with improved responsiveness',
    'files_affected' => ['phantom-ai/Templates/dashboard.html', 'assets/css/dashboard.css'],
    'ai_source' => 'chatgpt',
    'risk_level' => 'low'
];

$authorization = $truAi->authorizeMaintenanceOperation($maintenanceRequest);

if ($authorization['authorized']) {
    echo "✅ Maintenance operation authorized!\n";
    echo "Plan:\n";
    echo "  - Summary: {$authorization['plan']['summary']}\n";
    echo "  - AI Source: {$authorization['plan']['ai_source']}\n";
    echo "  - Risk Level: {$authorization['plan']['risk_level']}\n";
    echo "  - Estimated Duration: {$authorization['plan']['estimated_duration']}\n";
    echo "  - Approval Required: " . ($authorization['requires_approval'] ? 'Yes' : 'No') . "\n";
    echo "\nRollback Strategy:\n";
    echo "  - Method: {$authorization['rollback_strategy']['method']}\n";
    echo "  - Backup Required: " . ($authorization['rollback_strategy']['backup_required'] ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Maintenance operation denied!\n";
    echo "Reason: {$authorization['reason']}\n";
}

// Example 3: Maintenance Command Processing
echo "\n\n=== Example 3: Maintenance Command Processing ===\n\n";

$controller = new MaintenanceController($truAi);

$commands = [
    'Upgrade Phantom.ai dashboard',
    'Refactor review workflow',
    'Fix security issue in auth.php',
    'Update documentation for TruAi Core'
];

foreach ($commands as $command) {
    echo "Processing: {$command}\n";
    $result = $controller->processMaintenanceCommand($command, ['user_id' => 'admin']);
    
    if ($result['success']) {
        echo "✅ Command processed successfully\n";
        echo "   Risk Level: {$result['intent']['risk_level']}\n";
        echo "   AI Source: {$result['intent']['ai_source']}\n";
    } else {
        echo "❌ Command failed: {$result['error']}\n";
    }
    echo "\n";
}

// Example 4: Audit Logging
echo "\n=== Example 4: Audit Logging ===\n\n";

// Log an AI interaction
AuditLogger::logAIInteraction([
    'user_id' => 'admin',
    'input_text' => 'Create a product grid block',
    'context_items' => ['block-template.php', 'style.css'],
    'ai_tier' => 'high',
    'ai_source' => 'copilot',
    'outcome' => 'success'
]);

// Log a setting proposal
AuditLogger::logSettingProposal([
    'user_id' => 'admin',
    'setting_name' => 'ai_tier',
    'current_value' => 'mid',
    'proposed_value' => 'high',
    'reason' => 'Task complexity requires high-tier processing',
    'ai_source' => 'chatgpt',
    'user_decision' => 'accepted',
    'applied' => true
]);

// Log a Copilot escalation
AuditLogger::logCopilotEscalation([
    'user_id' => 'admin',
    'task_id' => 'task-12345',
    'reason' => 'Production code generation required',
    'task_description' => 'Create WooCommerce product block',
    'context_provided' => ['product-block-spec.md', 'existing-blocks/'],
    'outcome' => 'pending'
]);

// Get audit statistics
$stats = AuditLogger::getStatistics();

echo "Audit Statistics:\n";
echo "  Total AI Interactions: {$stats['total_interactions']}\n";
echo "  Total Maintenance Operations: {$stats['total_maintenance_ops']}\n";
echo "  Total Setting Proposals: {$stats['total_setting_proposals']}\n";
echo "  Total Copilot Escalations: {$stats['total_copilot_escalations']}\n\n";

echo "Recent Entries:\n";
foreach (array_slice($stats['recent_entries'], 0, 3) as $entry) {
    echo "  - [{$entry['type']}] {$entry['timestamp']}\n";
}

// Example 5: Policy Enforcement
echo "\n\n=== Example 5: Policy Enforcement ===\n\n";

$policies = [
    'PHANTOM-UI-001',
    'localhost_only',
    'security_immutability',
    'audit_completeness',
    'deterministic_behavior'
];

foreach ($policies as $policy) {
    $enforced = $truAi->enforcePolicy($policy);
    echo "Policy: {$policy}\n";
    echo "Status: " . ($enforced ? "✅ Enforced" : "❌ Not enforced") . "\n\n";
}

// Example 6: Integration with Task Router
echo "\n=== Example 6: Integration with Existing Workflow ===\n\n";

// Simulate a task coming through the workflow
$task = [
    'description' => 'Review security vulnerabilities in payment processing',
    'files' => ['payment-gateway.php', 'transaction-handler.php']
];

echo "Task: {$task['description']}\n";

// Step 1: Classify task type
$taskType = 'code_review'; // Would normally come from TierManager

// Step 2: Get AI source recommendation from TruAi
$aiSource = $truAi->arbitrateAISource($taskType, [
    'files' => $task['files'],
    'complexity' => 'high'
]);

echo "Task Type: {$taskType}\n";
echo "Recommended AI Source: {$aiSource['source']}\n";
echo "Reason: {$aiSource['reason']}\n";

// Step 3: Log the decision
AuditLogger::logAIInteraction([
    'user_id' => 'system',
    'input_text' => $task['description'],
    'context_items' => $task['files'],
    'ai_tier' => 'mid',
    'ai_source' => $aiSource['source'],
    'outcome' => 'routed'
]);

echo "✅ Task routed through TruAi Core and logged\n";

// Clean up - disable maintenance mode
$truAi->setMaintenanceMode(false);

echo "\n\n=== Examples Complete ===\n";
echo "All examples executed successfully!\n";
echo "Check logs/audit.log and logs/truai-core.log for audit trails.\n";
