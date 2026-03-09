# TruAi / ROMA / Gemini / Phantom.ai Development Knowledge Base

Generated: 2026-03-09T06:04:29.768264 UTC

------------------------------------------------------------------------

# 1. System Ecosystem Overview

The My Deme AI ecosystem consists of four primary systems:

## TruAi Core

Source of truth and governance engine.

Responsibilities: - Decision authority - Risk evaluation - ROI
evaluation - Escalation control - Subordinate AI governance

## ROMA Security Layer

Security and encryption substrate responsible for:

-   Local‑only enforcement
-   Encryption (RSA‑2048 / AES‑256‑GCM)
-   Session and credential protection
-   Monitoring and security events
-   Internal trust validation

ROMA must never falsely report "active" unless encryption and monitoring
are verified.

## Gemini.ai

Cloud server management executor.

Characteristics:

-   Subordinate to TruAi Core
-   Executes server actions only after approval
-   Plesk compatible
-   Contabo infrastructure aware
-   Cost‑aware ROI threshold evaluation

Execution flow:

User → TruAi Core → Approved Intent → Gemini.ai → Server → Audit → TruAi
Core

## Phantom.ai

Local development and web project orchestration system.

Primary responsibilities:

-   Web application development
-   Project orchestration
-   Task management
-   Multi‑source resource integration
-   Execution planning

Phantom operates locally alongside TruAi.

------------------------------------------------------------------------

# 2. Gemini.ai ROI Threshold Algorithm

Purpose: Determine whether Gemini executes locally or escalates.

Formula:

ROI = (V × 0.35) + (K × 0.10) − (C × 0.35) − (R × 0.20)

Variables:

V = Operational Value\
K = Knowledge Confidence\
C = Execution Cost\
R = Risk Exposure

Decision thresholds:

  ROI Score   Action
  ----------- ----------------------
  ≥ 55        Execute locally
  40--54      Execute with warning
  25--39      Forward to TruAi
  \< 25       Forward to Copilot

Mandatory escalation rule:

If Risk ≥ 70 → escalate regardless of ROI.

------------------------------------------------------------------------

# 3. ROMA Security Architecture

ROMA responsibilities:

-   Encryption enforcement
-   Portal protection
-   Session security
-   Credential encryption
-   System monitoring

ROMA components:

  Component            Role
  -------------------- -------------------------------
  Encryption Service   RSA/AES key management
  Security Monitor     anomaly detection
  Portal Protection    authentication hardening
  Trust Channel        internal system communication

ROMA must validate:

-   Encryption keys present
-   Session integrity
-   Workspace access
-   System trust state

If verification fails → Trust state becomes SUSPENDED.

------------------------------------------------------------------------

# 4. Internal Trust Channel (ITC)

ITC connects internal systems securely.

Example communication path:

TruAi Core → Gemini.ai → Server infrastructure

Handshake procedure:

1.  TruAi sends identity request
2.  Gemini responds with public key
3.  ROMA verifies key signature
4.  Session key negotiated
5.  Encrypted communication begins

Failure result:

Trust state downgraded and execution halted.

------------------------------------------------------------------------

# 5. Local Sovereign Recovery Protocol (LSRP)

A proprietary password recovery mechanism designed to eliminate remote
hijacking risks.

Principles:

-   No email recovery
-   No SMS authentication
-   Local system verification required
-   ROMA encryption enforced

Recovery factors:

1.  Local server access
2.  OS administrator password verification
3.  ROMA trust validation
4.  Device fingerprint match

If all checks succeed:

Temporary password is generated and encrypted using ROMA.

Temporary credential requirements:

-   24+ characters
-   One‑time use
-   Short expiration
-   Mandatory rotation after login

------------------------------------------------------------------------

# 6. Master Recovery Key

During initial setup:

Generate 256‑bit recovery key.

Properties:

-   Displayed once
-   Stored hashed only
-   Used only for catastrophic recovery

Use conditions:

-   Local system access
-   ROMA verified
-   Rate limited

------------------------------------------------------------------------

# 7. Password Security Improvements

Recommended upgrades:

Algorithm migration:

bcrypt → Argon2id

Benefits:

-   GPU resistant
-   Memory‑hard hashing
-   Stronger modern defense

Password requirements:

-   Minimum 12 characters
-   Uppercase
-   Lowercase
-   Number
-   Symbol

Password history tracking recommended.

------------------------------------------------------------------------

# 8. Gemini.ai Deployment Architecture

Recommended hosting model:

Subdomain:

gemini-ai.demewebsolutions.com

Directory layout:

/var/www/vhosts/demewebsolutions.com/ httpdocs/ gemini-ai/ public/
backend/ config/ logs/

ROMA key storage:

/opt/roma/keys/

Keys must never exist inside web root.

Recommended secure connection:

WireGuard VPN between HQ and Contabo VPS.

------------------------------------------------------------------------

# 9. Phantom.ai Development Notes

Phantom.ai functions as:

-   Web project orchestration environment
-   Multi‑source resource engine
-   Task planning system

Future integration goals:

-   TruAi governance oversight
-   Copilot execution delegation
-   Code context awareness
-   Resource aggregation from:

Local files\
Server assets\
URLs\
Repositories

------------------------------------------------------------------------

# 10. Security Principles Across the Ecosystem

Primary rules:

1.  TruAi Core is the only authority
2.  Subordinate AIs cannot self‑approve
3.  Encryption must always be verified
4.  Cost efficiency must be evaluated before execution
5.  Escalation occurs when risk exceeds allowed threshold

------------------------------------------------------------------------

# 11. Condensed Transcript (Phantom.ai Development Discussion)

User requested:

-   Dashboard redesign
-   Multi‑source task capability
-   Integration of existing panel icons
-   Source‑of‑truth architecture philosophy

Discussion outcomes:

Execution Header concept\
Task Stack queue system\
Inline diff preview capability\
Auto‑ROI transparency display

Goal:

Create an IDE‑style AI environment capable of replacing Cursor while
preserving TruAi governance controls.

Key principle:

Efficiency and practical workflow take priority over experimental
features.

------------------------------------------------------------------------

# End of Export
