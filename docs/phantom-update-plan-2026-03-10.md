# Phantom.ai Update Plan — 2026-03-10

Generated: 2026-03-10

## Canonical UI Routes

| Route | File | Status |
|-------|------|--------|
| `/Phantom.ai/dashboard.html` | `dashboard.html` | ✅ Canonical (lowercase) |
| `/Phantom.ai/login-portal.html` | `login-portal.html` | ✅ Canonical (lowercase) |
| `/Phantom.ai/Dashboard.html` | `Dashboard.html` | ↪ Redirect stub → `dashboard.html` |
| `/Phantom.ai/phantom.ai-login-portal.html` | `phantom.ai-login-portal.html` | ↪ Redirect stub → `login-portal.html` |

**Case-sensitive server rule:** Only lowercase filenames are used on the production server.

---

## Governance

Phantom.ai is subordinate to TruAi Core and must not override Core decisions.

- Phantom.ai **must** respect TruAi Core's governance tier, risk level, and approval decisions.
- Phantom.ai **must not** invent new authority or override TruAi governance.
- Phantom.ai **must** report execution outcomes upward via the subordinate contract.

---

## Security Authority

ROMA is the trust substrate. Phantom.ai trust UI must reflect live ROMA state only.

- Trust state is fetched from `PHANTOM_CONFIG.ROMA_API_BASE + '/security/roma'`
- No "protected", "verified", or similar claims are shown unless runtime data supports them
- Fail-closed: if trust state is UNVERIFIED or UNKNOWN, protected operations are blocked

---

## Trust States (ROMA)

| State | Meaning | Execution |
|-------|---------|-----------|
| `VERIFIED` | All checks passed | Auto-execution allowed |
| `DEGRADED` | Partial failure | Escalation bias ↑; high-risk ops restricted |
| `UNVERIFIED` | Trust broken | Execution blocked |
| `UNKNOWN` | Not yet checked | Hold execution |

---

## Phantom-Scoped Config Object

```javascript
window.PHANTOM_CONFIG = {
  APP_BASE:      '/Phantom.ai',
  AUTH_API_BASE: '/Phantom.ai/api/v1',
  ROMA_API_BASE: '/Phantom.ai/api/v1',  // or '/ROMA/api/v1' when ROMA runs separately
  CSRF_TOKEN:    '',
  IS_AUTHENTICATED: false,
  USERNAME: ''
};
```

---

## Normative References

- `truai-core-architecture.md` — TruAi Core architecture
- `roma-security.md` — ROMA security architecture
- `authentication-security.md` — Authentication security
- `local-sovereign-recovery.md` — LSRP documentation
- `docs/deployment.md` — Deployment guide
- `docs/truai-core.md` — TruAi Core documentation

External (cross-repo):
- `DemeWebsolutions/ROMA: docs/TRUAI_SUBORDINATE_SYSTEM_CONTRACTS.md` — Subordinate contracts
- `DemeWebsolutions/ROMA: docs/TRUST_STATE_DASHBOARD_SPEC.md` — Trust state dashboard spec

---

## New Files Added (March 2026)

| File | Purpose |
|------|---------|
| `dashboard.html` | Canonical lowercase dashboard (replaces `Dashboard.html`) |
| `login-portal.html` | Canonical lowercase login portal (replaces `phantom.ai-login-portal.html`) |
| `assets/js/roma-trust.js` | ROMA trust state management, execution header, trust gating |
| `assets/js/phantom-settings.js` | Settings save/load/test with AI API key test functions |
| `api/security/roma.php` | ROMA trust state API endpoint |
| `api/settings/index.php` | Settings save/load API endpoint |
| `docs/phantom-update-plan-2026-03-10.md` | This planning document |

---

## Protected Operations List

The following dashboard operations require ROMA trust state to be `VERIFIED`:

- AI Agent Task execution
- Deploy (push to production)
- Run Tests (executes scripts)
- Any workspace file write
- Task approval/execution
- Cross-system command dispatch

Operations that are **not** trust-gated (always available):
- Code review (read-only analysis)
- Chat/messaging (read-only)
- Settings management (local preferences)
- ROMA trust check (the trust check itself must always work)
- Copy/view operations

---

## Outcome Reporting Contract

Phantom.ai should report execution outcomes via `POST /api/v1/subordinate/outcome`:

```json
{
  "system": "phantom.ai",
  "task_id": "task-1024",
  "action": "workspace_file_write",
  "status": "blocked",
  "reason": "roma_trust_unverified",
  "trust_state": "UNVERIFIED",
  "timestamp": "2026-03-10T00:00:00Z"
}
```

---

## Implementation Priorities

1. ✅ Route/filename normalization
2. ✅ Redirect/API path cleanup  
3. ✅ Dashboard ROMA execution header
4. ✅ Protected-operation trust gating
5. ✅ Login portal ROMA trust badge (runtime-true only)
6. ✅ Phantom-scoped config (`window.PHANTOM_CONFIG`)
7. ✅ AI settings save/load/test (ChatGPT + Claude)
8. ✅ Recommendations panel
9. ✅ Documentation consolidation
10. [ ] Outcome reporting endpoint (backend v2)
11. [ ] Full backend API implementation (requires Node.js/PostgreSQL stack)
