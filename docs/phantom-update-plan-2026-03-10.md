# Phantom.ai Update Plan — 2026-03-10

Generated from: "File-by-File Edit Plan — Phantom.ai / March 2026"

---

## Canonical UI Routes (lowercase only — case-sensitive servers)

| Page | Canonical Path | Former / Retired Path |
|------|---------------|-----------------------|
| Dashboard | `/Phantom.ai/dashboard.html` | `Dashboard.html` (uppercase, root) |
| Login Portal | `/Phantom.ai/login-portal.html` | `phantom.ai-login-portal.html` (prefixed, root) |

> All internal links and API redirects use the canonical lowercase paths above.

---

## Governance

Phantom.ai is a **subordinate system** of TruAi Core.

- Must respect TruAi Core's governance layer decisions.
- Must not override TruAi Core's risk level, tier, or approval decisions.
- Outcome reporting is sent upward to TruAi (and ultimately ROMA) — not invented locally.

Reference: `truai-core-architecture.md`, ROMA `docs/TRUAI_SUBORDINATE_SYSTEM_CONTRACTS.md`.

---

## Security Authority — ROMA Trust State

ROMA is the trust substrate. Phantom.ai trust UI must reflect **live ROMA state only**.
Static "protected" copy is forbidden unless runtime-verified (ROMA TRUST_STATE_DASHBOARD_SPEC).

### Trust State API

- Endpoint: `GET /ROMA/api/v1/security/roma`
- Config key: `window.PHANTOM_CONFIG.ROMA_API_BASE`

### Trust States

| State | Meaning | UI Action |
|-------|---------|-----------|
| `VERIFIED` | All checks passed | Execution permitted; Execution Header green |
| `DEGRADED` | Partial failure | Escalation bias ↑; high-risk actions restricted |
| `UNVERIFIED` | Trust broken | Execution blocked; protected controls disabled |
| `UNKNOWN` | Not yet checked | Hold execution; header neutral |

### Protected Operations (blocked when not `VERIFIED`)

Any element with `data-protected-op` attribute is automatically gated by `PHANTOM_TRUST._applyGating()`:

- Send / execute AI task (`#btn-send`)
- Code review trigger (`🔍 Review` toolbar button)
- Apply/write file actions
- Task approval and execution
- Environment-changing operations
- Cross-system command dispatch

---

## Config Object

```javascript
window.PHANTOM_CONFIG = {
  APP_BASE:      '/Phantom.ai',
  AUTH_API_BASE: '/Phantom.ai/api/v1',   // Phantom auth endpoints
  ROMA_API_BASE: '/ROMA/api/v1',         // ROMA trust endpoints (separate)
  CSRF_TOKEN:    '',
  IS_AUTHENTICATED: false,
  USERNAME: ''
};
```

---

## Files Changed (2026-03-10)

| File | Action |
|------|--------|
| `Dashboard.html` | Renamed → `dashboard.html` |
| `phantom.ai-login-portal.html` | Renamed → `login-portal.html` |
| `dashboard.html` | Updated title, config, nav links, ROMA Execution Header, trust gating |
| `login-portal.html` | Updated config, ROMA fetch (ROMA_API_BASE), all `/TruAi/` → `/Phantom.ai/` |
| `assets/js/api.js` | Updated login redirect to `/Phantom.ai/login-portal.html`; fallback base URL |
| `assets/js/roma-trust.js` | **Created** — ROMA trust fetch/render/gating helper |
| `docs/deployment.md` | Updated canonical filenames and route table |
| `docs/phantom-update-plan-2026-03-10.md` | **Created** (this file) |
| `docs/phantom-outcome-payload.example.json` | **Created** — outcome reporting contract |

---

## Normative References

- `truai-core-architecture.md` — TruAi Core source of truth, subordinate list
- `roma-security.md` — ROMA encryption and monitoring layer
- `authentication-security.md` — Argon2id, rate limiting, session security
- `local-sovereign-recovery.md` — Local Sovereign Recovery Protocol
- ROMA `docs/TRUAI_SUBORDINATE_SYSTEM_CONTRACTS.md` — Phantom.ai contract
- ROMA `docs/TRUST_STATE_DASHBOARD_SPEC.md` — Execution Header spec
- TruAi `docs/ROMA_CONTRACT.md` — ROMA API payload shape
- TruAi `docs/SECURITY.md` — Session cookie requirements

---

## Implementation Priorities (completed)

- [x] Route normalization (filename rename + canonical paths)
- [x] Redirect/API path cleanup (`/TruAi/` → `/Phantom.ai/` and `/ROMA/`)
- [x] Dashboard Execution Header (ROMA trust strip)
- [x] Protected-operation gating (`data-protected-op` + `PHANTOM_TRUST`)
- [x] Login portal ROMA trust badge (live fetch from ROMA_API_BASE)
- [x] Docs consolidation (this file + deployment.md update)
- [ ] Outcome reporting contract (payload defined in `phantom-outcome-payload.example.json`; POST wiring is a Phase 2 backend task)
