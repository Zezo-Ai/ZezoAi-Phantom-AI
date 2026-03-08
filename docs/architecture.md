# Phantom.ai Backend Architecture
**Home Office Private Network Implementation**

## Overview

This document provides a comprehensive architecture and implementation plan for deploying the Phantom.ai backend on a private home office network using **your current Mac** with corporate-grade security.

**Key Advantage:** By using your existing Mac as the server, you avoid the ~$800 hardware cost while maintaining enterprise-level security and functionality.

---

## Architecture Summary

**Deployment Model:** Private home network using current Mac as dedicated server  
**Security Level:** Corporate-grade with multi-layer protection  
**Network Type:** Isolated/segmented private network  
**Access Model:** LAN-only (no external internet exposure)  
**Hardware Cost:** ~$250-400 (external backup SSD + UPS only)

---

## 1. Infrastructure Requirements

### Hardware (Using Current Mac)

**Your Current Mac Setup:**
This deployment uses your **current Mac computer** as the server. The system will check your existing hardware specifications and ensure they meet minimum requirements.

**Minimum Requirements:**
- **Mac Model:** Mac Mini (2018+), iMac, Mac Studio, or MacBook Pro
- **CPU:** Apple M1/M2 or Intel i5+ (4+ cores)
- **RAM:** 16GB minimum, 32GB recommended for optimal performance
- **Storage:** 512GB SSD minimum (1TB recommended)
  - 100GB for OS and applications
  - 200GB for database and logs
  - 200GB for backups
  - Remaining for system growth

**Note:** If your current Mac doesn't meet minimum specs, the installation script will provide recommendations for optimization (e.g., external storage for database/backups).

**Network Infrastructure:**
- Gigabit Ethernet connection (wired preferred over WiFi for security and stability)
- Dedicated VLAN for Phantom.ai services (optional but recommended)
- Static IP address on local network
- Router with firewall capabilities

**Required Additional Hardware:**
- **External SSD** (1-2TB) for automated encrypted backups (~$100-200)
- **UPS** (Uninterruptible Power Supply) for power protection (~$150)

**Backup Storage:**
- External SSD or NAS for automated backups (required)
- Time Machine backup drive (recommended)
- Offsite backup solution (encrypted cloud or physical)

---

## 2. Technology Stack

### Core Components

**Operating System:**
- macOS Monterey 12.0+ or Ventura 13.0+
- Full disk encryption (FileVault) enabled
- Firewall configured and active

**Runtime Environment:**
- **Node.js 20 LTS** (JavaScript/TypeScript backend)
  - Fast, efficient, excellent for real-time features
  - Native macOS support
  - Large ecosystem for authentication and security

**Database:**
- **PostgreSQL 15+** (Primary relational database)
  - Enterprise-grade security features
  - ACID compliance for critical data
  - Excellent audit logging capabilities
  - Native encryption support
- **Redis 7+** (Session store and cache)
  - In-memory session management
  - Fast authentication token validation
  - Rate limiting storage

**Web Server:**
- **Nginx** (Reverse proxy and TLS termination)
  - SSL/TLS certificate management
  - Load balancing (future scaling)
  - DDoS protection
  - Request rate limiting

**Process Management:**
- **PM2** (Node.js process manager)
  - Auto-restart on crashes
  - Zero-downtime deployments
  - Log management
  - Resource monitoring

---

## 3. Security Architecture

### Network Security

**Firewall Configuration:**
```bash
# macOS firewall (pfctl) rules
# Block all incoming by default, allow specific ports

# Allow only from local network (192.168.1.0/24)
pass in on en0 from 192.168.1.0/24 to any port 443 # HTTPS
pass in on en0 from 192.168.1.0/24 to any port 5432 # PostgreSQL (if needed)

# Block all other incoming
block in all
```

**Network Segmentation:**
- VLAN 10: Phantom.ai server and database
- VLAN 20: Client workstations
- VLAN 30: Admin/management access only

**Access Control:**
- IP whitelist for admin access
- MAC address filtering on router
- VPN requirement for remote admin (if needed)

### Application Security

**Authentication System:**
- **Argon2id** password hashing (more secure than bcrypt)
- **JWT tokens** for session management
  - Short-lived access tokens (15 min)
  - Long-lived refresh tokens (7 days, httpOnly)
  - Token rotation on refresh
- **2FA/MFA** with TOTP (Time-based One-Time Password)
  - Google Authenticator compatible
  - Backup codes for account recovery
  - SMS fallback (optional)

**Session Management:**
- HttpOnly cookies (prevents XSS attacks)
- Secure flag (HTTPS only)
- SameSite=Strict (prevents CSRF)
- Session timeout: 30 minutes inactivity
- Maximum session lifetime: 12 hours
- Redis-backed sessions with encryption

**Encryption:**
- **TLS 1.3** for all communications
- Self-signed certificate for LAN (or Let's Encrypt if domain available)
- Database encryption at rest (PostgreSQL native encryption)
- Field-level encryption for sensitive data (API keys, credentials)
  - AES-256-GCM for data encryption
  - Separate encryption keys per data type
  - Key rotation every 90 days

**Rate Limiting:**
- Login attempts: 5 per 15 minutes per IP
- API calls: 100 per minute per user
- Password reset: 3 per hour per account
- Automatic IP blocking after threshold

**Security Headers:**
```javascript
// Express.js middleware
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      imgSrc: ["'self'", "data:"],
    },
  },
  hsts: {
    maxAge: 31536000,
    includeSubDomains: true,
    preload: true
  },
  frameguard: { action: 'deny' },
  xssFilter: true,
  noSniff: true
}));
```

---

## 4. Database Design

### Schema Overview

**Users Table:**
```sql
CREATE TABLE users (
    user_id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- Argon2id hash
    totp_secret VARCHAR(32), -- Encrypted 2FA secret
    is_2fa_enabled BOOLEAN DEFAULT FALSE,
    failed_login_attempts INTEGER DEFAULT 0,
    locked_until TIMESTAMP,
    last_login TIMESTAMP,
    last_password_change TIMESTAMP DEFAULT NOW(),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
```

**Sessions Table:**
```sql
CREATE TABLE sessions (
    session_id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    refresh_token_hash VARCHAR(255) NOT NULL,
    ip_address INET NOT NULL,
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT NOW(),
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_expires_at ON sessions(expires_at);
```

**Audit Log Table:**
```sql
CREATE TABLE audit_log (
    log_id BIGSERIAL PRIMARY KEY,
    user_id UUID REFERENCES users(user_id),
    event_type VARCHAR(50) NOT NULL, -- LOGIN_ATTEMPT, LOGIN_SUCCESS, LOGOUT, etc.
    event_data JSONB, -- Additional context
    ip_address INET NOT NULL,
    user_agent TEXT,
    success BOOLEAN DEFAULT TRUE,
    timestamp TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_audit_log_user_id ON audit_log(user_id);
CREATE INDEX idx_audit_log_timestamp ON audit_log(timestamp DESC);
CREATE INDEX idx_audit_log_event_type ON audit_log(event_type);
```

**Legal Acknowledgments Table:**
```sql
CREATE TABLE legal_acknowledgments (
    ack_id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    terms_version VARCHAR(20) NOT NULL, -- e.g., "2025-01-01"
    ip_address INET NOT NULL,
    user_agent TEXT,
    acknowledged_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_legal_ack_user_id ON legal_acknowledgments(user_id);
```

**AI Credentials Table (Encrypted):**
```sql
CREATE TABLE ai_credentials (
    cred_id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    provider VARCHAR(50) NOT NULL, -- 'claude', 'openai', 'gemini'
    encrypted_api_key TEXT NOT NULL, -- AES-256-GCM encrypted
    model_preference VARCHAR(50),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, provider)
);

CREATE INDEX idx_ai_creds_user_id ON ai_credentials(user_id);
```

---

## 5. API Endpoints

### Authentication Endpoints

**POST /api/auth/register** (Initial setup only - disable after first user)
```json
Request:
{
  "username": "admin",
  "email": "admin@local.phantom.ai",
  "password": "SecureP@ssw0rd123!"
}

Response:
{
  "success": true,
  "message": "Account created. Please set up 2FA."
}
```

**POST /api/auth/login**
```json
Request:
{
  "username": "admin",
  "password": "SecureP@ssw0rd123!"
}

Response:
{
  "success": true,
  "requires2FA": true,
  "tempToken": "eyJhbGc..." // Short-lived token for 2FA step
}
```

**POST /api/auth/verify-2fa**
```json
Request:
{
  "tempToken": "eyJhbGc...",
  "totpCode": "123456"
}

Response:
{
  "success": true,
  "accessToken": "eyJhbGc...", // 15-min JWT
  "user": {
    "userId": "uuid",
    "username": "admin",
    "email": "admin@local.phantom.ai"
  }
}

Set-Cookie: refreshToken=...; HttpOnly; Secure; SameSite=Strict
```

**POST /api/auth/acknowledge-terms**
```json
Request:
{
  "termsVersion": "2025-01-01",
  "acknowledged": true
}

Response:
{
  "success": true,
  "message": "Terms acknowledged"
}
```

**POST /api/auth/refresh**
```json
Request:
// No body - uses refreshToken from httpOnly cookie

Response:
{
  "success": true,
  "accessToken": "eyJhbGc..." // New 15-min token
}
```

**POST /api/auth/logout**
```json
Request:
// Authenticated request with accessToken

Response:
{
  "success": true,
  "message": "Logged out successfully"
}

Set-Cookie: refreshToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT
```

### Session Management

**GET /api/auth/session**
```json
Response:
{
  "success": true,
  "session": {
    "userId": "uuid",
    "username": "admin",
    "lastActivity": "2026-01-06T18:00:00Z",
    "expiresAt": "2026-01-06T18:30:00Z"
  }
}
```

**GET /api/auth/sessions** (All active sessions)
```json
Response:
{
  "success": true,
  "sessions": [
    {
      "sessionId": "uuid",
      "ipAddress": "192.168.1.100",
      "userAgent": "Mozilla/5.0...",
      "lastActivity": "2026-01-06T18:00:00Z",
      "isCurrent": true
    }
  ]
}
```

**DELETE /api/auth/sessions/:sessionId** (Revoke specific session)

### Audit Logging

**GET /api/audit/logs**
```json
Query params: ?page=1&limit=50&eventType=LOGIN_ATTEMPT

Response:
{
  "success": true,
  "logs": [
    {
      "logId": "12345",
      "userId": "uuid",
      "eventType": "LOGIN_SUCCESS",
      "ipAddress": "192.168.1.100",
      "timestamp": "2026-01-06T18:00:00Z",
      "success": true
    }
  ],
  "pagination": {
    "page": 1,
    "totalPages": 10,
    "totalRecords": 500
  }
}
```

**POST /api/audit/log** (Internal - called by frontend)
```json
Request:
{
  "eventType": "PAGE_LOAD",
  "eventData": {
    "page": "dashboard"
  }
}

Response:
{
  "success": true,
  "logId": "12345"
}
```

### AI Credentials Management

**POST /api/credentials/ai**
```json
Request:
{
  "provider": "claude",
  "apiKey": "sk-ant-...",
  "modelPreference": "claude-sonnet-4.5",
  "isDefault": true
}

Response:
{
  "success": true,
  "message": "AI credentials saved securely"
}
```

**GET /api/credentials/ai**
```json
Response:
{
  "success": true,
  "credentials": [
    {
      "provider": "claude",
      "modelPreference": "claude-sonnet-4.5",
      "isDefault": true,
      "maskedApiKey": "sk-ant-***...***xyz"
    }
  ]
}
```

---

## 6. Installation & Setup Guide

### Step 0: Check Your Mac Specifications

Before proceeding, verify your current Mac meets minimum requirements:

```bash
# Check macOS version
sw_vers

# Check RAM
sysctl hw.memsize | awk '{print $2/1024/1024/1024 " GB"}'

# Check available disk space
df -h / | awk 'NR==2 {print $4 " available"}'

# Check CPU info
sysctl -n machdep.cpu.brand_string
sysctl -n hw.physicalcpu  # Physical cores
sysctl -n hw.logicalcpu   # Logical cores (with hyperthreading)

# Check network interface
ifconfig | grep "inet " | grep -v 127.0.0.1
```

**Minimum Requirements Checklist:**
- [ ] macOS 12.0+ (Monterey or newer)
- [ ] 16GB+ RAM
- [ ] 200GB+ free disk space
- [ ] 4+ CPU cores
- [ ] Wired Ethernet connection available

**If requirements not met:** Consider using external SSD for database storage or upgrading RAM if possible.

---

### Step 1: Prepare Mac Server

**Install Homebrew:**
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

**Install Required Software:**
```bash
# Install Node.js LTS
brew install node@20

# Install PostgreSQL
brew install postgresql@15
brew services start postgresql@15

# Install Redis
brew install redis
brew services start redis

# Install Nginx
brew install nginx

# Install PM2 globally
npm install -g pm2
```

**Enable Firewall:**
```bash
# Enable macOS firewall
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate on

# Block all incoming except specific
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setblockall on
```

### Step 2: Configure Database

**Create Database:**
```bash
# Create PostgreSQL user and database
psql postgres

CREATE USER phantom_admin WITH PASSWORD 'SecureDBP@ssw0rd123!';
CREATE DATABASE phantom_ai OWNER phantom_admin;
GRANT ALL PRIVILEGES ON DATABASE phantom_ai TO phantom_admin;
\q
```

**Enable Encryption:**
```bash
# Edit postgresql.conf
# Add: ssl = on
# Add: ssl_cert_file = '/path/to/server.crt'
# Add: ssl_key_file = '/path/to/server.key'

# Restart PostgreSQL
brew services restart postgresql@15
```

**Run Migrations:**
```bash
cd /path/to/phantom-backend
npm run migrate
```

### Step 3: Configure Backend Application

**Clone or Create Backend:**
```bash
mkdir -p ~/phantom-backend
cd ~/phantom-backend
npm init -y
```

**Install Dependencies:**
```bash
npm install express
npm install pg redis
npm install jsonwebtoken bcrypt argon2
npm install helmet cors express-rate-limit
npm install dotenv
npm install speakeasy qrcode # For 2FA
npm install winston # For logging
```

**Create .env File:**
```bash
cat > .env << EOF
# Server Configuration
NODE_ENV=production
PORT=3000
HOST=0.0.0.0

# Database
DB_HOST=localhost
DB_PORT=5432
DB_NAME=phantom_ai
DB_USER=phantom_admin
DB_PASSWORD=SecureDBP@ssw0rd123!
DB_SSL=true

# Redis
REDIS_HOST=localhost
REDIS_PORT=6379

# Security
JWT_SECRET=$(openssl rand -base64 64)
JWT_REFRESH_SECRET=$(openssl rand -base64 64)
ENCRYPTION_KEY=$(openssl rand -base64 32)

# Session
SESSION_TIMEOUT_MINUTES=30
SESSION_MAX_HOURS=12

# Rate Limiting
LOGIN_ATTEMPTS_MAX=5
LOGIN_ATTEMPTS_WINDOW_MINUTES=15

# HTTPS
SSL_CERT_PATH=/path/to/cert.pem
SSL_KEY_PATH=/path/to/key.pem
EOF
```

**Set Permissions:**
```bash
chmod 600 .env
```

### Step 4: Generate SSL Certificate

**Self-Signed Certificate (LAN only):**
```bash
# Generate private key
openssl genrsa -out server.key 4096

# Generate certificate
openssl req -new -x509 -key server.key -out server.crt -days 3650 \
  -subj "/C=US/ST=Florida/O=MyDeme/CN=phantom.local"

# Set permissions
chmod 600 server.key
chmod 644 server.crt

# Move to secure location
sudo mkdir -p /etc/phantom-ai/certs
sudo mv server.* /etc/phantom-ai/certs/
```

### Step 5: Configure Nginx

**Create Nginx Configuration:**
```bash
cat > /usr/local/etc/nginx/servers/phantom.conf << 'EOF'
upstream phantom_backend {
    server 127.0.0.1:3000;
}

server {
    listen 443 ssl http2;
    server_name phantom.local;

    ssl_certificate /etc/phantom-ai/certs/server.crt;
    ssl_certificate_key /etc/phantom-ai/certs/server.key;
    
    ssl_protocols TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Rate limiting
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=100r/m;

    location /api/auth/login {
        limit_req zone=login burst=3 nodelay;
        proxy_pass http://phantom_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    location /api {
        limit_req zone=api burst=50 nodelay;
        proxy_pass http://phantom_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    location / {
        root /path/to/phantom-frontend;
        index Phantom.ai.portal.html;
        try_files $uri $uri/ /Phantom.ai.portal.html;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name phantom.local;
    return 301 https://$server_name$request_uri;
}
EOF
```

**Test and Reload Nginx:**
```bash
sudo nginx -t
sudo brew services restart nginx
```

### Step 6: Start Backend with PM2

**Create PM2 Ecosystem File:**
```bash
cat > ecosystem.config.js << 'EOF'
module.exports = {
  apps: [{
    name: 'phantom-backend',
    script: './server.js',
    instances: 2,
    exec_mode: 'cluster',
    env: {
      NODE_ENV: 'production'
    },
    error_file: './logs/error.log',
    out_file: './logs/out.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    max_memory_restart: '1G',
    watch: false
  }]
};
EOF
```

**Start Application:**
```bash
pm2 start ecosystem.config.js
pm2 save
pm2 startup
```

### Step 7: Configure Auto-Start on Boot

**Create LaunchDaemon:**
```bash
sudo pm2 startup launchd -u $(whoami) --hp $(eval echo ~$(whoami))
```

---

## 7. Backup Strategy

### Automated Backups

**Database Backup Script:**
```bash
#!/bin/bash
# /usr/local/bin/phantom-backup.sh

BACKUP_DIR="/Volumes/Backup/phantom-ai"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="phantom_ai"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup PostgreSQL
pg_dump -U phantom_admin -Fc $DB_NAME > "$BACKUP_DIR/db_$DATE.dump"

# Backup Redis
redis-cli --rdb "$BACKUP_DIR/redis_$DATE.rdb"

# Backup .env and configs
cp ~/phantom-backend/.env "$BACKUP_DIR/env_$DATE.bak"

# Encrypt backups
openssl enc -aes-256-cbc -salt -in "$BACKUP_DIR/db_$DATE.dump" \
  -out "$BACKUP_DIR/db_$DATE.dump.enc" -k "BackupEncryptionKey123!"
rm "$BACKUP_DIR/db_$DATE.dump"

# Delete backups older than 30 days
find "$BACKUP_DIR" -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

**Schedule with Cron:**
```bash
# Edit crontab
crontab -e

# Add daily backup at 2 AM
0 2 * * * /usr/local/bin/phantom-backup.sh >> /var/log/phantom-backup.log 2>&1
```

---

## 8. Monitoring & Maintenance

### Logging

**Centralized Logging:**
```javascript
// winston logger configuration
const winston = require('winston');

const logger = winston.createLogger({
  level: 'info',
  format: winston.format.combine(
    winston.format.timestamp(),
    winston.format.json()
  ),
  transports: [
    new winston.transports.File({ filename: 'error.log', level: 'error' }),
    new winston.transports.File({ filename: 'combined.log' }),
    new winston.transports.Console()
  ]
});
```

**Log Rotation:**
```bash
# Install logrotate
brew install logrotate

# Create logrotate config
cat > /usr/local/etc/logrotate.d/phantom << 'EOF'
/path/to/phantom-backend/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 phantom phantom
    sharedscripts
}
EOF
```

### Health Monitoring

**Create Health Check Endpoint:**
```javascript
app.get('/api/health', async (req, res) => {
  try {
    // Check database
    await pool.query('SELECT 1');
    
    // Check Redis
    await redisClient.ping();
    
    res.json({
      status: 'healthy',
      timestamp: new Date().toISOString(),
      uptime: process.uptime(),
      database: 'connected',
      redis: 'connected'
    });
  } catch (error) {
    res.status(500).json({
      status: 'unhealthy',
      error: error.message
    });
  }
});
```

**Monitor with PM2:**
```bash
# View status
pm2 status

# View logs
pm2 logs

# Monitor resources
pm2 monit

# View detailed info
pm2 info phantom-backend
```

---

## 9. Security Checklist

### Pre-Deployment

- [ ] FileVault enabled on Mac
- [ ] Firewall configured and active
- [ ] Strong database passwords set
- [ ] SSL certificates generated
- [ ] .env file permissions set (600)
- [ ] Rate limiting configured
- [ ] Security headers enabled
- [ ] Session timeout configured
- [ ] Backup strategy implemented

### Post-Deployment

- [ ] Default admin account created
- [ ] 2FA enabled for admin
- [ ] Registration endpoint disabled
- [ ] Database encrypted at rest
- [ ] Audit logging active
- [ ] PM2 auto-start configured
- [ ] Backup script scheduled
- [ ] Health monitoring active

### Ongoing Maintenance

- [ ] Weekly security update checks
- [ ] Monthly password rotations
- [ ] Quarterly encryption key rotation
- [ ] Review audit logs weekly
- [ ] Test backups monthly
- [ ] Update SSL certificates before expiry

---

## 10. Network Configuration

### Local DNS Setup

**Add to /etc/hosts:**
```bash
sudo nano /etc/hosts

# Add:
192.168.1.10    phantom.local
```

**Configure Router:**
- Reserve static IP (192.168.1.10) for Mac server
- Block external access to ports 443, 3000, 5432, 6379
- Enable MAC address filtering
- Configure firewall rules

### Client Configuration

**Trust Self-Signed Certificate:**
```bash
# On each client Mac
sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /path/to/server.crt
```

**Access Application:**
```
https://phantom.local
```

---

## 11. Cost & Resource Estimates

### One-Time Costs (Using Current Mac)

Since you're using your **current Mac** as the server, hardware costs are minimal:

- **Current Mac:** $0 (already owned)
- **External SSD (1-2TB backup):** ~$100-200
- **Uninterruptible Power Supply (UPS):** ~$150 (highly recommended)
- **Network cables/accessories:** ~$20-50 (if needed)
- **Total:** ~$250-400 (instead of $1,150)

**Note:** This deployment leverages your existing Mac hardware, significantly reducing upfront costs while maintaining corporate-grade security.

### Monthly Costs

- **Electricity:** ~$2-5/month (depending on Mac model and usage)
  - Apple Silicon (M1/M2): ~5-10W idle, 15-25W peak → ~$2/month
  - Intel Macs: ~10-20W idle, 40-65W peak → ~$3-5/month
- **Internet bandwidth:** $0 (LAN-only deployment)
- **Total:** ~$2-5/month

### Time Investment

- **Initial setup:** 8-12 hours (one-time)
- **Monthly maintenance:** 2-4 hours
- **Annual time:** ~50 hours

### Resource Usage (Your Mac)

The Phantom.ai backend will use approximately:
- **Disk Space:** 100-300GB (database, logs, backups)
- **RAM:** 4-8GB (PostgreSQL, Node.js, Redis, Nginx)
- **CPU:** 5-15% average (spikes during AI task processing)

**Your Mac remains usable for other tasks** - the server processes run in the background with minimal performance impact.

---

## 12. Future Enhancements

### Phase 3 Roadmap

- **Horizontal Scaling:** Add second Mac for high availability
- **Containerization:** Docker deployment for easier management
- **Advanced Monitoring:** Grafana + Prometheus dashboards
- **Automated Testing:** Integration and security tests
- **API Documentation:** OpenAPI/Swagger specs
- **Mobile App:** iOS/Android clients
- **Advanced Analytics:** Usage patterns and cost tracking
- **AI Model Management:** Automatic model selection based on task complexity

---

## 13. Troubleshooting Guide

### Common Issues

**Issue: Cannot connect to https://phantom.local**
```bash
# Check Nginx status
sudo brew services list | grep nginx

# Check certificate
openssl s_client -connect phantom.local:443

# Verify DNS
ping phantom.local
```

**Issue: Database connection errors**
```bash
# Check PostgreSQL
brew services list | grep postgresql

# Test connection
psql -U phantom_admin -d phantom_ai -h localhost

# Check logs
tail -f /usr/local/var/log/postgres.log
```

**Issue: Sessions not persisting**
```bash
# Check Redis
redis-cli ping

# View sessions
redis-cli KEYS "sess:*"

# Check Redis logs
tail -f /usr/local/var/log/redis.log
```

---

## 14. Support & Documentation

### Additional Resources

- Node.js Security Best Practices: https://nodejs.org/en/docs/guides/security/
- PostgreSQL Security: https://www.postgresql.org/docs/current/security.html
- OWASP Top 10: https://owasp.org/www-project-top-ten/

### Internal Documentation

- API Specification: `/docs/API.md` (to be created)
- Database Schema: `/docs/DATABASE.md` (to be created)
- Deployment Guide: `/docs/DEPLOYMENT.md` (to be created)

---

## Conclusion

This architecture provides a robust, secure, and scalable foundation for the Phantom.ai backend in a private home office environment. The implementation uses corporate-grade security practices while remaining cost-effective and manageable with Mac resources.

**Key Strengths:**
- ✅ Multi-layered security (network, application, data)
- ✅ Comprehensive audit logging
- ✅ Automated backups with encryption
- ✅ Session management with timeout
- ✅ 2FA/MFA support
- ✅ Rate limiting and DDoS protection
- ✅ Scalable architecture for future growth

**Next Steps:**
1. Review and approve architecture
2. Procure hardware (Mac Mini + backup storage)
3. Begin Phase 2 implementation
4. Test thoroughly before production use
5. Document any customizations or deviations

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-06  
**Author:** GitHub Copilot  
**Classification:** Internal Use Only - Proprietary
