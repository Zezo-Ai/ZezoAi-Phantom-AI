# Phantom.ai - Update & History of Developmental Structure and Guidelines

Phantom.ai update (standalone ai interface for electron app Mac + Local Server)
Task    Model
Planning    Cheap / fast model 
Review    Mid-tier
Code generation    High-tier
Tests    Mid-tier

Faithful over little - Ruler over much model:

Prompt -> (cheap / fast model) [adjust to minimal request to minimize token usage] + [send / verify comprehension] (yes / no) -> 

Elevation
(Yes) -> (cheap / fast model) {delegation} -> *Mid-tier / High-tire 

The Grind
(No) *more clarification needed -> (cheap / fast model) {improve comprehension and repeat cycle until (yes)} 

* {delegation}
Basic Response (previous knowledge)    Cheap / fast model 
Review    Mid-tier
Code generation    High-tier

-> (cheap / fast model) Verify response for High-Tier (yes / no)
(yes) task completion -> (Mid Tier) Test -> Pass / Fail (implementing / recycle - improve).
 
* Learn from success and failures - apply to future prompts, limit token usage, improve low tier ( cheap / fast model ) responses - improve resourcefulness and adaptability. This contributes to Basic Response {delegation} ROI based response - No or limited,  High or Mid-tier responses needed based on history/past response. 

Familiarize yourself with Phantom.Ai - Confirm this summary of the software and capabilities. Assist in planning update. Capabilities will broaden yet, it will remain with the core purpose of Software Development  and Web Development Dominance. Using TruAi Core it will (continue to) be compatible with and operate with the governance of TruAi (Main Source of Truth) Software / Applications. The New Update will feature a proprietary “Cursor Parity” styled dashboard with functionality to in exact aliment with TruAi. Consider the fallowing in plan execution Phantom.ai will be subordinate to TruAi with sole purpose of expedited and cost efficient software / app / web development - with ROMA Security Protection (encryption and protocol alignment). To Achieve all of this carefully review all Phantom.ai notes / documents, review existing files that may need to up be updated or eliminated all-together. Study TruAi and to align Phantom.ai with TruAi via it’s TruAi Core. 

The new update will feature a new login portal .html (provided in the attachment) and dashboard .html (see attachments). visual will be updated - but wiring won’t insure all wiring has corresponding functionality with cursor parity capabilities. 

This software / app will keep the cost efficient model in tier governance - delegations of high-cost commands as hand-off to copilot for maximum ROI efficiency and Mid to Low - within network (AI API - Claude and ChatGPT) cost-effective and efficient usage. In this respect additional documentation will be available for review. 

Short summary of what Phantom.ai is and where things live:

- What it is: a proprietary, self-managing workflow and compliance toolchain for WordPress development that combines automated compliance checks, multi-tier AI routing, Copilot-driven code generation, audit logging, and a frontend portal.
- Legal/security: Strict proprietary license, trade-secret protected; report vulnerabilities to security@demewebsolutions.com.
- Main components (location: `phantom-ai/`):
  - TierManager (three AI tiers: cheap/fast, mid, high) — task classification and cost tracking.
  - TaskRouter — workflow routing, prompt compression, clarification loops.
  - LearningEngine / MetadataTracker — store execution metadata, learn and optimize routing.
  - TruAi Core (`phantom-ai/Core/TruAiCore.php`) — AI-source arbitration, maintenance authorization, policy enforcement.
  - AuditLogger & MaintenanceController — comprehensive audit trails and self-maintenance flow.
  - CLI: `phantom-cli` for classify/process/copilot/stats/report.
- APIs (under `api/`):
  - Session/auth endpoints (GET /api/auth/session, POST /api/auth/login, 2FA/refresh/logout).
  - Audit endpoints (GET /api/audit/list, POST /api/audit/log).
  - TruAi endpoints (POST /api/truai/arbitrate, POST /api/truai/maintenance).
- Frontend vs Backend status:
  - Frontend: complete and production-ready (multiple HTML pages, session UX, legal modal, AI credentials UI).
  - Backend: architecture and deployment thoroughly documented (Node.js + PostgreSQL + Redis + Nginx recommended), but many backend pieces are documented/pending implementation (auth, AI integrations, policy enforcement).
- Deployment & ops:
  - Docs recommend running on a Mac LAN server (Node 20, Postgres 15, Redis 7, Nginx, PM2). Full deployment, backups, monitoring, and security hardening guidance in `docs/`.
- Where to find docs and entry points:
  - Root README: `./README.md`
  - Core component overview: `./phantom-ai/README.md`
  - API docs: `./api/README.md`
  - Operational docs: `./docs/architecture.md`, `./docs/deployment.md`, `./docs/quickstart.md`, `./docs/truai-core.md`, `./docs/security-implementation-notes.md`.

