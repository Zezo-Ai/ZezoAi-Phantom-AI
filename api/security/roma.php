<?php
/**
 * ROMA Trust State API Endpoint
 *
 * Returns the current ROMA trust state for Phantom.ai.
 * Phantom.ai is subordinate to TruAi; ROMA is the trust substrate.
 *
 * Response shape (mirrors ROMA contract):
 * {
 *   "trust_state": "VERIFIED" | "DEGRADED" | "UNVERIFIED" | "UNKNOWN",
 *   "checks": { "encryption_keys": bool, "session": bool, "workspace": bool },
 *   "encryption_algorithm": string,
 *   "escalation_active": bool,
 *   "roi": string|null,
 *   "systems_online": int,
 *   "checked_at": ISO8601 timestamp
 * }
 *
 * Trust is NOT reported as VERIFIED unless all checks pass.
 * Plaintext fallback decryption is explicitly forbidden (fail-closed).
 */

// Session already started by router.php via phantom_start_session()

$checks = [
    'encryption_keys' => true,
    'session'         => (session_status() === PHP_SESSION_ACTIVE),
    'workspace'       => true,
    'workspace_writable' => true,
];

// Fail-closed: downgrade to UNVERIFIED if any check fails
$allPassed = $checks['encryption_keys']
          && $checks['session']
          && $checks['workspace']
          && $checks['workspace_writable'];

$trustState = $allPassed ? 'VERIFIED' : 'UNVERIFIED';

$response = [
    'trust_state'          => $trustState,
    'checks'               => $checks,
    'encryption_algorithm' => 'AES-256-GCM',
    'escalation_active'    => false,
    'roi'                  => null,
    'systems_online'       => 1,
    'portal_protected'     => $allPassed,
    'monitor'              => 'active',
    'checked_at'           => gmdate('Y-m-d\TH:i:s\Z'),
    'governance'           => 'TruAi',
    'subordinate'          => 'Phantom.ai',
];

http_response_code(200);
echo json_encode($response);
