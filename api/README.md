# TruAi Core API Endpoints

This directory contains the REST API endpoints for TruAi Core functionality.

## Session Management

The `router.php` file provides idempotent session handling via the `phantom_start_session()` function, which ensures sessions are only started once per request, preventing the error:
```
session_name(): Session name cannot be changed when a session is active
```

## Available Endpoints

### Authentication

#### GET /api/auth/session
Returns current session information and authentication status.

**Response:**
```json
{
  "success": true,
  "session_id": "...",
  "session_name": "PHANTOM_SESSION",
  "session_status": "active",
  "timestamp": "2026-01-08 05:40:00",
  "user": {
    "id": "admin",
    "authenticated": true
  }
}
```

### Audit System

#### GET /api/audit/list
Returns audit log entries with optional filtering and pagination.

**Query Parameters:**
- `type` - Filter by log type (ai_interaction, maintenance_operation, etc.)
- `user_id` - Filter by user ID
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 50)

**Response:**
```json
{
  "success": true,
  "entries": [...],
  "pagination": {
    "page": 1,
    "per_page": 50,
    "total": 100,
    "total_pages": 2
  }
}
```

#### POST /api/audit/log
Logs an audit entry from the frontend.

**Request Body:**
```json
{
  "type": "ai_interaction",
  "user_id": "admin",
  "input_text": "Create product block",
  "context_items": ["template.php"],
  "ai_tier": "high",
  "ai_source": "copilot",
  "outcome": "success"
}
```

### TruAi Core

#### POST /api/truai/arbitrate
Routes a task to the appropriate AI source.

**Request Body:**
```json
{
  "task_type": "code_review",
  "description": "Review security in auth.php",
  "context": {
    "files": ["auth.php"],
    "complexity": "high"
  }
}
```

**Response:**
```json
{
  "success": true,
  "task_type": "code_review",
  "recommended_source": "claude",
  "reason": "Long-form reasoning, audits"
}
```

#### POST /api/truai/maintenance
Processes a maintenance command.

**Request Body:**
```json
{
  "command": "Upgrade Phantom.ai dashboard",
  "maintenance_mode": true
}
```

**Response:**
```json
{
  "success": true,
  "requires_approval": true,
  "intent": {...},
  "plan": {...},
  "rollback_strategy": {...}
}
```

## Usage with PHP Built-in Server

For local development:

```bash
php -S localhost:8080 router.php
```

Then access endpoints at:
- http://localhost:8080/api/auth/session
- http://localhost:8080/api/audit/list
- etc.

## Apache/Nginx Configuration

For production deployment, configure your web server to route all API requests to `router.php`:

### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/ router.php [QSA,L]
```

### Nginx
```nginx
location /api/ {
    try_files $uri $uri/ /router.php?$args;
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/router.php;
}
```

## Security Considerations

- All endpoints use idempotent session handling
- Sessions are named `PHANTOM_SESSION` for consistency
- CORS headers are configured for cross-origin requests
- All responses are JSON-encoded
- Error handling with appropriate HTTP status codes
- User authentication checks where applicable

## Testing

Test the session idempotency:
```bash
php -r "require 'router.php'; phantom_start_session(); echo 'Session: ' . session_id();"
```

Test an endpoint:
```bash
curl http://localhost:8080/api/auth/session
```
