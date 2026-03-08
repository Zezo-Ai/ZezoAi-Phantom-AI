<?php
/**
 * Maintenance Controller for Phantom.ai
 * 
 * Handles AI-based self-maintenance, upgrade, and modification operations
 * in a controlled, command-driven manner.
 * 
 * © 2013 – Present My Deme, LLC
 * All Rights Reserved
 */

namespace PhantomAI\Core;

class MaintenanceController
{
    private TruAiCore $truAiCore;
    private bool $maintenanceMode = false;

    /**
     * Supported maintenance command patterns
     */
    private const COMMAND_PATTERNS = [
        'upgrade' => '/upgrade\s+(.+)/i',
        'refactor' => '/refactor\s+(.+)/i',
        'fix' => '/fix\s+(.+)/i',
        'improve' => '/improve\s+(.+)/i',
        'align' => '/align\s+(.+)/i',
        'update' => '/update\s+(.+)/i',
        'prepare' => '/prepare\s+(.+)/i'
    ];

    /**
     * Risk level thresholds based on file patterns
     */
    private const RISK_PATTERNS = [
        'high' => ['auth', 'login', 'security', 'audit', 'core'],
        'medium' => ['workflow', 'api', 'database', 'config'],
        'low' => ['documentation', 'template', 'css', 'style']
    ];

    public function __construct(?TruAiCore $truAiCore = null)
    {
        $this->truAiCore = $truAiCore ?? new TruAiCore();
    }

    /**
     * Process a maintenance command
     * 
     * @param string $command The maintenance command
     * @param array $context Additional context
     * @return array Result of the processing
     */
    public function processMaintenanceCommand(string $command, array $context = []): array
    {
        // Check if maintenance mode is enabled
        if (!$this->maintenanceMode && !$this->truAiCore->isMaintenanceModeEnabled()) {
            return [
                'success' => false,
                'error' => 'Maintenance mode is not enabled',
                'message' => 'Please enable maintenance mode before executing maintenance commands'
            ];
        }

        // Parse the command
        $parsedCommand = $this->parseCommand($command);
        if (!$parsedCommand['valid']) {
            return [
                'success' => false,
                'error' => 'Invalid command format',
                'message' => 'Command must follow pattern: [action] [target]',
                'examples' => [
                    'Upgrade Phantom.ai dashboard',
                    'Refactor review workflow',
                    'Fix security issue in auth.php',
                    'Improve performance',
                    'Update documentation'
                ]
            ];
        }

        // Analyze the command and generate maintenance intent
        $intent = $this->generateMaintenanceIntent($parsedCommand, $context);

        // Request authorization from TruAi Core
        $authorization = $this->truAiCore->authorizeMaintenanceOperation($intent);

        if (!$authorization['authorized']) {
            AuditLogger::logMaintenanceOperation([
                'user_id' => $context['user_id'] ?? 'system',
                'command' => $command,
                'summary' => $intent['summary'],
                'files_affected' => $intent['files_affected'],
                'ai_source' => $intent['ai_source'],
                'risk_level' => $intent['risk_level'],
                'authorized' => false,
                'outcome' => 'rejected',
                'rejection_reason' => $authorization['reason']
            ]);

            return [
                'success' => false,
                'error' => 'Authorization denied',
                'reason' => $authorization['reason'],
                'violations' => $authorization['violations'] ?? []
            ];
        }

        // Log the authorized operation
        AuditLogger::logMaintenanceOperation([
            'user_id' => $context['user_id'] ?? 'system',
            'command' => $command,
            'summary' => $intent['summary'],
            'files_affected' => $intent['files_affected'],
            'ai_source' => $intent['ai_source'],
            'risk_level' => $intent['risk_level'],
            'authorized' => true,
            'plan' => $authorization['plan'],
            'outcome' => 'awaiting_approval'
        ]);

        // Return the plan for user approval
        return [
            'success' => true,
            'requires_approval' => true,
            'intent' => $intent,
            'plan' => $authorization['plan'],
            'rollback_strategy' => $authorization['rollback_strategy'],
            'message' => 'Maintenance plan generated. Please review and approve to proceed.'
        ];
    }

    /**
     * Execute an approved maintenance plan
     * 
     * @param array $plan The approved maintenance plan
     * @param array $context Execution context
     * @return array Execution result
     * 
     * @note This is a prototype implementation. In production, this would:
     *       1. Route to the appropriate AI source (Copilot, ChatGPT, Claude, GitHub)
     *       2. Execute the maintenance operation with full context
     *       3. Validate the results against expected outcomes
     *       4. Return detailed output for human review
     *       5. Support rollback on failure
     * 
     * @todo Implement actual AI source integration and execution logic
     */
    public function executeMaintenancePlan(array $plan, array $context = []): array
    {
        // Verify plan structure
        if (!isset($plan['ai_source']) || !isset($plan['summary'])) {
            return [
                'success' => false,
                'error' => 'Invalid plan structure'
            ];
        }

        // Log execution start
        AuditLogger::logMaintenanceOperation([
            'user_id' => $context['user_id'] ?? 'system',
            'command' => $plan['summary'],
            'summary' => $plan['summary'],
            'files_affected' => $plan['files_to_modify'] ?? [],
            'ai_source' => $plan['ai_source'],
            'risk_level' => $plan['risk_level'] ?? 'medium',
            'authorized' => true,
            'plan' => $plan,
            'user_approval' => true,
            'outcome' => 'executing'
        ]);

        // TODO: In production implementation, this would:
        // - Connect to the appropriate AI service (based on plan['ai_source'])
        // - Send the maintenance request with full context
        // - Receive and validate the AI response
        // - Apply changes if validated
        // - Create rollback point before applying
        // - Return detailed execution report

        return [
            'success' => true,
            'status' => 'execution_started',
            'message' => 'Maintenance operation is being processed. This is a prototype - actual AI integration pending.',
            'plan' => $plan,
            'next_step' => 'Review output and validate changes'
        ];
    }

    /**
     * Enable or disable maintenance mode
     * 
     * @param bool $enabled
     * @return void
     */
    public function setMaintenanceMode(bool $enabled): void
    {
        $this->maintenanceMode = $enabled;
        $this->truAiCore->setMaintenanceMode($enabled);
    }

    /**
     * Parse a maintenance command
     * 
     * @param string $command
     * @return array Parsed command
     */
    private function parseCommand(string $command): array
    {
        foreach (self::COMMAND_PATTERNS as $action => $pattern) {
            if (preg_match($pattern, $command, $matches)) {
                return [
                    'valid' => true,
                    'action' => $action,
                    'target' => $matches[1],
                    'original' => $command
                ];
            }
        }

        return [
            'valid' => false,
            'original' => $command
        ];
    }

    /**
     * Generate maintenance intent from parsed command
     * 
     * @param array $parsedCommand
     * @param array $context
     * @return array Maintenance intent
     */
    private function generateMaintenanceIntent(array $parsedCommand, array $context): array
    {
        $action = $parsedCommand['action'];
        $target = $parsedCommand['target'];

        // Determine AI source based on action type
        $aiSource = $this->determineAISource($action);

        // Estimate affected files based on target
        $affectedFiles = $this->estimateAffectedFiles($target);

        // Calculate risk level
        $riskLevel = $this->calculateRiskLevel($affectedFiles, $action);

        return [
            'command' => $parsedCommand['original'],
            'summary' => "Action: {$action}, Target: {$target}",
            'action_type' => $action,
            'target' => $target,
            'files_affected' => $affectedFiles,
            'ai_source' => $aiSource,
            'risk_level' => $riskLevel,
            'user_id' => $context['user_id'] ?? 'system',
            'context' => $context
        ];
    }

    /**
     * Determine appropriate AI source for action
     * 
     * @param string $action
     * @return string AI source
     */
    private function determineAISource(string $action): string
    {
        $sourceMap = [
            'upgrade' => 'copilot',
            'refactor' => 'claude',
            'fix' => 'copilot',
            'improve' => 'claude',
            'align' => 'chatgpt',
            'update' => 'chatgpt',
            'prepare' => 'chatgpt'
        ];

        return $sourceMap[$action] ?? 'chatgpt';
    }

    /**
     * Estimate files that will be affected
     * 
     * @param string $target
     * @return array List of file patterns
     */
    private function estimateAffectedFiles(string $target): array
    {
        $files = [];

        // Parse target for file mentions
        if (preg_match('/(\S+\.(?:php|js|html|css|json))/', $target, $matches)) {
            $files[] = $matches[1];
        }

        // Check for component mentions
        if (stripos($target, 'dashboard') !== false) {
            $files[] = 'phantom-ai/Templates/*.html';
        }

        if (stripos($target, 'workflow') !== false) {
            $files[] = 'phantom-ai/Workflow/*.php';
        }

        if (stripos($target, 'documentation') !== false) {
            $files[] = '*.md';
        }

        // Default if no specific files identified
        if (empty($files)) {
            $files[] = 'TBD - will be determined during analysis';
        }

        return $files;
    }

    /**
     * Calculate risk level for maintenance operation
     * 
     * @param array $files
     * @param string $action
     * @return string Risk level (low|medium|high)
     */
    private function calculateRiskLevel(array $files, string $action): string
    {
        // Check file patterns against risk thresholds
        foreach ($files as $file) {
            $fileLower = strtolower($file);
            
            foreach (self::RISK_PATTERNS['high'] as $pattern) {
                if (stripos($fileLower, $pattern) !== false) {
                    return 'high';
                }
            }

            foreach (self::RISK_PATTERNS['medium'] as $pattern) {
                if (stripos($fileLower, $pattern) !== false) {
                    return 'medium';
                }
            }
        }

        // Action-based risk assessment
        if (in_array($action, ['upgrade', 'fix'])) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get maintenance mode status
     * 
     * @return bool
     */
    public function isMaintenanceModeEnabled(): bool
    {
        return $this->maintenanceMode || $this->truAiCore->isMaintenanceModeEnabled();
    }

    /**
     * Get maintenance statistics
     * 
     * @return array Statistics
     */
    public function getMaintenanceStatistics(): array
    {
        $auditStats = AuditLogger::getStatistics();
        
        return [
            'total_operations' => $auditStats['total_maintenance_ops'] ?? 0,
            'maintenance_mode_enabled' => $this->isMaintenanceModeEnabled(),
            'recent_operations' => array_filter(
                $auditStats['recent_entries'] ?? [],
                fn($entry) => ($entry['type'] ?? '') === 'maintenance_operation'
            )
        ];
    }
}
