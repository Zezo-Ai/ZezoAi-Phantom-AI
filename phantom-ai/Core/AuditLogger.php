<?php
/**
 * Audit Logger for Phantom.ai and TruAi Core
 * 
 * Comprehensive audit logging system for AI interactions,
 * maintenance operations, and system changes.
 * 
 * © 2013 – Present My Deme, LLC
 * All Rights Reserved
 */

namespace PhantomAI\Core;

class AuditLogger
{
    private const AUDIT_LOG_PATH = 'logs/audit.log';
    private const AI_HISTORY_PATH = 'artifacts/ai-history/';
    private const MAINTENANCE_LOG_PATH = 'logs/maintenance.log';

    /**
     * Log an AI interaction
     * 
     * @param array $interaction Details of the interaction
     * @return bool Success status
     */
    public static function logAIInteraction(array $interaction): bool
    {
        $required = ['user_id', 'input_text', 'ai_tier', 'context_items'];
        foreach ($required as $field) {
            if (!isset($interaction[$field])) {
                return false;
            }
        }

        $entry = [
            'type' => 'ai_interaction',
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $interaction['user_id'],
            'input_text' => $interaction['input_text'],
            'context_items' => $interaction['context_items'],
            'ai_tier' => $interaction['ai_tier'],
            'ai_source' => $interaction['ai_source'] ?? null,
            'suggested_changes' => $interaction['suggested_changes'] ?? [],
            'user_approvals' => $interaction['user_approvals'] ?? [],
            'outcome' => $interaction['outcome'] ?? 'not_specified' // Require explicit outcome
        ];

        self::writeLog(self::AUDIT_LOG_PATH, $entry);
        self::writeAIHistory($entry);
        
        return true;
    }

    /**
     * Log a maintenance operation
     * 
     * @param array $operation Details of the maintenance operation
     * @return bool Success status
     */
    public static function logMaintenanceOperation(array $operation): bool
    {
        $required = ['user_id', 'command', 'files_affected', 'ai_source', 'risk_level'];
        foreach ($required as $field) {
            if (!isset($operation[$field])) {
                return false;
            }
        }

        $entry = [
            'type' => 'maintenance_operation',
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $operation['user_id'],
            'command' => $operation['command'],
            'summary' => $operation['summary'] ?? '',
            'files_affected' => $operation['files_affected'],
            'ai_source' => $operation['ai_source'],
            'risk_level' => $operation['risk_level'],
            'authorized' => $operation['authorized'] ?? false,
            'plan' => $operation['plan'] ?? [],
            'user_approval' => $operation['user_approval'] ?? null,
            'outcome' => $operation['outcome'] ?? 'pending',
            'rollback_available' => $operation['rollback_available'] ?? true
        ];

        self::writeLog(self::MAINTENANCE_LOG_PATH, $entry);
        self::writeLog(self::AUDIT_LOG_PATH, $entry);
        
        return true;
    }

    /**
     * Log a setting change proposal
     * 
     * @param array $proposal Details of the setting change proposal
     * @return bool Success status
     */
    public static function logSettingProposal(array $proposal): bool
    {
        $entry = [
            'type' => 'setting_proposal',
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $proposal['user_id'] ?? null,
            'setting_name' => $proposal['setting_name'],
            'current_value' => $proposal['current_value'],
            'proposed_value' => $proposal['proposed_value'],
            'reason' => $proposal['reason'] ?? '',
            'ai_source' => $proposal['ai_source'] ?? null,
            'user_decision' => $proposal['user_decision'] ?? 'pending',
            'applied' => $proposal['applied'] ?? false
        ];

        self::writeLog(self::AUDIT_LOG_PATH, $entry);
        
        return true;
    }

    /**
     * Log a policy enforcement check
     * 
     * @param array $check Details of the policy check
     * @return bool Success status
     */
    public static function logPolicyCheck(array $check): bool
    {
        $entry = [
            'type' => 'policy_check',
            'timestamp' => date('Y-m-d H:i:s'),
            'policy_name' => $check['policy_name'],
            'context' => $check['context'] ?? [],
            'result' => $check['result'] ?? false,
            'violations' => $check['violations'] ?? []
        ];

        self::writeLog(self::AUDIT_LOG_PATH, $entry);
        
        return true;
    }

    /**
     * Log an escalation to Copilot
     * 
     * @param array $escalation Details of the escalation
     * @return bool Success status
     */
    public static function logCopilotEscalation(array $escalation): bool
    {
        $entry = [
            'type' => 'copilot_escalation',
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $escalation['user_id'] ?? null,
            'task_id' => $escalation['task_id'] ?? null,
            'reason' => $escalation['reason'] ?? '',
            'task_description' => $escalation['task_description'] ?? '',
            'context_provided' => $escalation['context_provided'] ?? [],
            'outcome' => $escalation['outcome'] ?? 'pending'
        ];

        self::writeLog(self::AUDIT_LOG_PATH, $entry);
        
        return true;
    }

    /**
     * Retrieve audit log entries
     * 
     * @param array $filters Filters to apply
     * @return array Log entries
     */
    public static function getAuditEntries(array $filters = []): array
    {
        if (!file_exists(self::AUDIT_LOG_PATH)) {
            return [];
        }

        $entries = [];
        $lines = file(self::AUDIT_LOG_PATH, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if ($entry && self::matchesFilters($entry, $filters)) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Get audit statistics
     * 
     * @return array Statistics
     */
    public static function getStatistics(): array
    {
        $entries = self::getAuditEntries();
        
        $stats = [
            'total_interactions' => 0,
            'total_maintenance_ops' => 0,
            'total_setting_proposals' => 0,
            'total_copilot_escalations' => 0,
            'by_type' => [],
            'by_user' => [],
            'recent_entries' => array_slice(array_reverse($entries), 0, 10)
        ];

        foreach ($entries as $entry) {
            $type = $entry['type'] ?? 'unknown';
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;
            
            if (isset($entry['user_id'])) {
                $stats['by_user'][$entry['user_id']] = ($stats['by_user'][$entry['user_id']] ?? 0) + 1;
            }

            switch ($type) {
                case 'ai_interaction':
                    $stats['total_interactions']++;
                    break;
                case 'maintenance_operation':
                    $stats['total_maintenance_ops']++;
                    break;
                case 'setting_proposal':
                    $stats['total_setting_proposals']++;
                    break;
                case 'copilot_escalation':
                    $stats['total_copilot_escalations']++;
                    break;
            }
        }

        return $stats;
    }

    /**
     * Write a log entry
     * 
     * @param string $logPath Path to log file
     * @param array $entry Log entry data
     * @return bool Success status
     */
    private static function writeLog(string $logPath, array $entry): bool
    {
        self::ensureLogDirectory($logPath);
        
        $logLine = json_encode($entry) . PHP_EOL;
        return file_put_contents($logPath, $logLine, FILE_APPEND | LOCK_EX) !== false;
    }

    /**
     * Write to AI history archive
     * 
     * @param array $entry Log entry data
     * @return bool Success status
     */
    private static function writeAIHistory(array $entry): bool
    {
        self::ensureLogDirectory(self::AI_HISTORY_PATH);
        
        $date = date('Y-m-d');
        $historyFile = self::AI_HISTORY_PATH . "ai-history-{$date}.json";
        
        $logLine = json_encode($entry) . PHP_EOL;
        return file_put_contents($historyFile, $logLine, FILE_APPEND | LOCK_EX) !== false;
    }

    /**
     * Ensure log directory exists
     * 
     * @param string $path Path to check/create
     * @return void
     */
    private static function ensureLogDirectory(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Check if entry matches filters
     * 
     * @param array $entry Log entry
     * @param array $filters Filters to apply
     * @return bool Whether entry matches
     */
    private static function matchesFilters(array $entry, array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if (!isset($entry[$key]) || $entry[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Export audit log to file
     * 
     * @param string $outputPath Output file path
     * @param array $filters Optional filters
     * @return bool Success status
     */
    public static function exportAuditLog(string $outputPath, array $filters = []): bool
    {
        $entries = self::getAuditEntries($filters);
        
        $export = [
            'exported_at' => date('Y-m-d H:i:s'),
            'total_entries' => count($entries),
            'filters_applied' => $filters,
            'entries' => $entries
        ];

        return file_put_contents($outputPath, json_encode($export, JSON_PRETTY_PRINT)) !== false;
    }
}
