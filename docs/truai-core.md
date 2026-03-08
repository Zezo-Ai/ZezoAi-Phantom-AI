# Tru.Ai Core — Complete Documentation

**Tru.Ai Core (TruAi)** is the mastermind orchestration and governance layer for Phantom.ai. It manages AI routing, self-maintenance authorization, policy enforcement, and system-wide intelligence.

> © 2013 – Present My Deme, LLC · All Rights Reserved · [demewebsolutions.com](https://demewebsolutions.com/phantom-ai)

---

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Core Components](#core-components)
3. [Quick Start](#quick-start)
4. [AI Source Routing](#ai-source-routing)
5. [Self-Maintenance Capability](#self-maintenance-capability)
6. [Policy Enforcement](#policy-enforcement)
7. [Audit & Security](#audit--security)
8. [Usage Examples](#usage-examples)
9. [Integration](#integration)
10. [Configuration](#configuration)
11. [Implementation Status](#implementation-status)
12. [Legal Notice](#legal-notice)

---

## System Architecture

```
User
  ↓
Tru.Ai Core (Mastermind)
  ↓
Phantom.ai (Execution / Workflow Engine)
  ↓
Tools / AI Sources / Artifacts
```

No lateral shortcuts permitted. All operations flow through TruAi Core.

---

## Core Components

### 1. TruAiCore (`phantom-ai/Core/TruAiCore.php`)

The main orchestration engine:
- AI source arbitration
- Self-maintenance authorization
- Policy enforcement
- Maintenance mode management

**Key Methods:**

| Method | Description | Returns |
|--------|-------------|---------|
| `arbitrateAISource(string $taskType, array $context)` | Routes task to correct AI source | `['source' => string, 'reason' => string]` |
| `authorizeMaintenanceOperation(array $request)` | Evaluates and authorizes maintenance | Authorization result with plan |
| `enforcePolicy(string $policy, array $context)` | Enforces immutable policies | bool |
| `setMaintenanceMode(bool $enabled)` | Enable/disable maintenance mode | void |

### 2. AuditLogger (`phantom-ai/Core/AuditLogger.php`)

Comprehensive audit logging:

| Method | Description |
|--------|-------------|
| `logAIInteraction(array $interaction)` | Logs AI interaction with full context |
| `logMaintenanceOperation(array $operation)` | Logs maintenance operations |
| `getAuditEntries(array $filters)` | Retrieves filtered audit entries |
| `getStatistics()` | Returns audit statistics |
| `exportAuditLog(string $path, array $filters)` | Exports audit log to JSON |

### 3. MaintenanceController (`phantom-ai/Core/MaintenanceController.php`)

Handles AI-based self-maintenance:

| Method | Description |
|--------|-------------|
| `processMaintenanceCommand(string $cmd, array $ctx)` | Parses command and generates plan |
| `executeMaintenancePlan(array $plan, array $ctx)` | Executes approved plan |
| `setMaintenanceMode(bool $enabled)` | Controls maintenance mode |

---

## Quick Start

### Setup

```php
require_once __DIR__ . '/vendor/autoload.php';

use PhantomAI\Core\TruAiCore;
use PhantomAI\Core\AuditLogger;
use PhantomAI\Core\MaintenanceController;
```

### AI Source Arbitration

```php
$truAi = new TruAiCore();

$result = $truAi->arbitrateAISource('code_review', [
    'files' => ['auth.php'],
    'complexity' => 'high'
]);

echo "Recommended AI: " . $result['source']; // claude
```

### Maintenance Operations

```php
$controller = new MaintenanceController();
$controller->setMaintenanceMode(true);

$result = $controller->processMaintenanceCommand(
    'Upgrade Phantom.ai dashboard',
    ['user_id' => 'admin']
);

if ($result['success']) {
    $execution = $controller->executeMaintenancePlan(
        $result['plan'],
        ['user_id' => 'admin']
    );
}
```

### Audit Logging

```php
AuditLogger::logAIInteraction([
    'user_id' => 'admin',
    'input_text' => 'Create a product block',
    'context_items' => ['block-template.php'],
    'ai_tier' => 'high',
    'ai_source' => 'copilot',
    'outcome' => 'success'
]);

$stats = AuditLogger::getStatistics();
echo "Total interactions: " . $stats['total_interactions'];
```

### Local Development Server

```bash
php -S localhost:8080 router.php
```

Access endpoints at `http://localhost:8080/api/...` and the AI Screen at `http://localhost:8080/Phantom.ai-screen.html`.

---

## AI Source Routing

### Approved Sources

| Source | Role | Use Cases |
|--------|------|-----------|
| **GitHub** | Reference code | Research, references |
| **GitHub Copilot** | Production code | Code generation, fixes |
| **ChatGPT** | Planning & docs | Planning, design, documentation |
| **Claude** | Long-form reasoning | Code review, refactoring, audits |

### Routing Rules

| Task Type | AI Source |
|-----------|-----------|
| `planning` | ChatGPT |
| `design` | ChatGPT |
| `code_review` | Claude |
| `refactor` | Claude |
| `production_code` | Copilot |
| `research` | GitHub |
| `references` | GitHub |

---

## Self-Maintenance Capability

### Supported Command Patterns

- `Upgrade [component]` — System upgrades
- `Refactor [component]` — Code refactoring
- `Fix [issue]` — Bug fixes
- `Improve [aspect]` — Performance improvements
- `Align [component] with [target]` — Alignment tasks
- `Update [component]` — Updates
- `Prepare [task]` — Preparation tasks

### Command Flow

```
User Command → Command Parsing → Intent Generation
→ TruAi Authorization → User Approval
→ Plan Execution → Output Validation → Audit Log
```

### Risk Levels

| Level | Examples |
|-------|---------|
| **High** | Auth system, security, audit logging, core system |
| **Medium** | Workflow, API, database, configuration |
| **Low** | Documentation, templates, CSS/styling |

---

## Policy Enforcement

### Immutable Policies

| Policy | Description |
|--------|-------------|
| `PHANTOM-UI-001` | UI framework constraints |
| `localhost_only` | Localhost execution only |
| `security_immutability` | Security boundaries |
| `audit_completeness` | Complete audit trails |
| `deterministic_behavior` | Predictable execution |

### Security Boundaries

**AI CANNOT:**
❌ Auto-merge code · ❌ Silent file writes · ❌ Add dependencies without approval  
❌ Modify auth system · ❌ Modify audit logging · ❌ Change security policies  

**AI CAN Propose:**
✅ Code improvements · ✅ Documentation updates · ✅ UI enhancements  
✅ Workflow optimizations · ✅ Setting adjustments  

---

## Audit & Security

### Storage Locations

```
logs/audit.log           — Main audit log
logs/truai-core.log      — TruAi Core operations
logs/maintenance.log     — Maintenance operations
artifacts/ai-history/    — Historical AI interactions (dated JSON)
```

### REST API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/auth/session` | Session info & auth status |
| GET | `/api/audit/list` | Audit log entries (filterable) |
| POST | `/api/audit/log` | Log an audit entry |
| POST | `/api/truai/arbitrate` | Route task to AI source |
| POST | `/api/truai/maintenance` | Process maintenance command |

---

## Usage Examples

### AI Source Arbitration

```php
$truAi = new TruAiCore();
$result = $truAi->arbitrateAISource('code_review', [
    'files' => ['auth.php'],
    'complexity' => 'high'
]);
// ['source' => 'claude', 'reason' => 'Long-form reasoning, audits']
```

### Maintenance Authorization

```php
$truAi = new TruAiCore();
$truAi->setMaintenanceMode(true);

$authorization = $truAi->authorizeMaintenanceOperation([
    'command' => 'Upgrade dashboard layout',
    'summary' => 'Modernize dashboard UI',
    'files_affected' => ['phantom-ai/Templates/dashboard.html'],
    'ai_source' => 'chatgpt',
    'risk_level' => 'low'
]);

if ($authorization['authorized']) {
    echo "Plan: " . json_encode($authorization['plan']);
}
```

### Copilot Escalation Tracking

```php
AuditLogger::logCopilotEscalation([
    'user_id' => 'admin',
    'task_id' => 'task-123',
    'reason' => 'Production code required',
    'task_description' => 'Create WooCommerce block',
    'context_provided' => ['spec.md', 'examples/'],
    'outcome' => 'pending'
]);
```

---

## Integration

TruAi Core integrates with existing Phantom.ai components:

1. **TierManager** → Routes tasks to TruAi for AI source selection
2. **TaskRouter** → Uses TruAi arbitration for workflow decisions
3. **Maintenance Operations** → Governed by TruAi authorization
4. **Setting Changes** → Proposed through TruAi approval system
5. **All Operations** → Audited via TruAi logging

### Composer Autoloading

```json
{
  "autoload": {
    "psr-4": {
      "PhantomAI\\": "phantom-ai/"
    }
  }
}
```

Run `composer dump-autoload` after setup.

---

## Configuration

### `.phantom.yml`

```yaml
truai_core:
  enabled: true
  maintenance_mode: false
  audit_logging: true
  log_path: logs/truai-core.log
  ai_sources:
    github: true
    copilot: true
    chatgpt: true
    claude: true
```

### Web Server (Apache `.htaccess`)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/ router.php [QSA,L]
```

### Web Server (Nginx)

```nginx
location /api/ {
    try_files $uri $uri/ /router.php?$args;
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/router.php;
}
```

---

## Implementation Status

| Component | Status |
|-----------|--------|
| AI Source Arbitration (`TruAiCore::arbitrateAISource`) | ✅ Production Ready |
| Maintenance Authorization (`TruAiCore::authorizeMaintenanceOperation`) | ✅ Production Ready |
| Audit Logging System (`AuditLogger`) | ✅ Production Ready |
| Maintenance Command Parsing (`MaintenanceController`) | ✅ Production Ready |
| AI Screen Interface (`Phantom.ai-screen.html`) | ✅ Complete (requires backend) |
| Policy Enforcement Methods | 🔧 Prototype (placeholder returns `true`) |
| AI Service Integrations | 🔧 Requires implementation per AI service |
| API Authentication | 🔧 Not yet implemented in frontend |

### Design Principles

✅ Human-in-the-loop — All approvals required before execution  
✅ Audit trail — Every operation logged  
✅ Security boundaries — Protected systems identified  
✅ Rollback capability — Strategy generated for all operations  
✅ Deterministic behavior — Predictable authorization flow  

### Known Limitations

1. Policy enforcement currently returns `true` (placeholder)
2. AI execution requires manual implementation per AI service
3. Audit log has no automatic rotation/archiving
4. Limited retry logic for failed operations

---

## Legal Notice

**Tru.Ai Core (TruAi)**  
© 2013 – Present **My Deme, LLC**  
All Rights Reserved · Developed by **DemeWebsolutions.com**

Tru.Ai Core is **proprietary software**, **trade secret protected**, and **not open-source**.

### Prohibited Actions

You may NOT: copy, modify, or distribute · reverse engineer · create derivative works · use commercially without license · share or sublicense · remove copyright notices · access internal logic beyond documented APIs.

### Permitted Use

Use is permitted ONLY by authorized licensees of Phantom.ai, within the scope of the granted license, through documented interfaces.

All Tru.Ai Core operations are logged, audited, and legally defensible.

**Contact:** legal@demewebsolutions.com · https://demewebsolutions.com/phantom-ai

---

**Version:** 1.0.0 · **Last Updated:** January 2026 · **Status:** OPERATIONAL
