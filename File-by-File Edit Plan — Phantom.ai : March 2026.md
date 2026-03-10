## File-by-File Edit Plan — Phantom.ai / March 2026

### Overview
This implementation plan addresses file renaming, UI alignment, trust integration, routing normalization, and documentation improvements for Phantom.ai. The goal is cross-repo compatibility with TruAi governance and strict ROMA trust signaling.

---

### 1. Canonicalize Filenames and Routes

**Files/Paths to Update:**
- `Dashboard.html` → **rename to:** `dashboard.html` (lowercase)
- `phantom.ai-login-portal.html` → **rename to:** `login-portal.html` (lowercase)
- Update all UI and code references to use lowercase filenames and `/Phantom.ai/` path base:
  - Any links, redirects, or file items referencing `Dashboard.html`, `phantom.ai-login-portal.html`, `/TruAi/login-portal.html`, or mixed-case variants
  - Sidebar file explorer in dashboard
  - JavaScript redirect logic in `assets/js/api.js`

**Actions:**
- Search for all usages of `Dashboard.html`, `phantom.ai-login-portal.html`, and `/TruAi/login-portal.html` in both HTML and JS; replace with `/Phantom.ai/dashboard.html` and `/Phantom.ai/login-portal.html`.
- Remove/retire uppercase and prefixed artifacts after migration.

---

### 2. Update Configuration and Routing Logic

**Files to Edit:**
- `dashboard.html` (after rename)
- `login-portal.html` (after rename)
- `assets/js/api.js`

**Changes:**
- Introduce a config object for Phantom-specific routing:
  ```js
  window.PHANTOM_CONFIG = {
    APP_BASE: (window.location.origin + '/Phantom.ai').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
    AUTH_API_BASE: (window.location.origin + '/Phantom.ai/api/v1').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
    ROMA_API_BASE: (window.location.origin + '/ROMA/api/v1').replace(/\/ROMA\/ROMA/, '/ROMA')
  };
  ```
- Remove any hardcoded `/TruAi/` path assumptions from UI and JS.
- Update file explorer and internal routing logic.

---

### 3. Dashboard Execution Header & Trust Integration

**Files to Edit:**
- `dashboard.html`
- `assets/js/roma-trust.js` (new or expanded)

**Changes:**
- Add the Execution Header (above dashboard’s main layout) as specified in ROMA’s dashboard spec:
  - Trust State (live from ROMA)
  - Encryption badge
  - Escalation state
  - ROI indicator
  - System trust matrix (on hover/expand)
- Connect to ROMA’s API base (`ROMA_API_BASE`) for trust polling.
- Block protected UI actions when trust is anything less than VERIFIED.
- Add explicit copy for blocked state.

---

### 4. Login Portal Trust Integration

**Files to Edit:**
- `login-portal.html`
- Any referenced JS (e.g., `assets/js/roma-trust.js`)

**Changes:**
- Add dynamic ROMA trust badge (fetch live from ROMA API base).
- Remove static/pre-rendered “protected” claims. Only display trust badge if runtime-true.
- Ensure session/cookie behavior aligns with Authentication Security doc.
- Validate that all redirects, routing, or authentication endpoints are anchored in `/Phantom.ai/` paths.

---

### 5. Docs Consolidation

**Files to Edit/Add:**
- Add: `docs/phantom-update-plan-2026-03-10.md`
- Update: `docs/deployment.md` (reference new routes, canonical filenames, protected ops, trust gating)
- Document outcome reporting contract (add section or new file as needed):
  - Define POST payload for outcomes in `docs/phantom-outcome-contract.md` or in the plan

---

### 6. Protected Operations Gating

**Files to Edit:**
- `dashboard.html`
- `assets/js/roma-trust.js`

**Actions:**
- Identify and gate all protected operations:
  - File apply/write
  - Automation/exec actions
  - Environment changes
  - Command dispatches
- Only allow if ROMA trust state is VERIFIED.
- Add code helpers to centralize gating logic.

---

### 7. Outcome Reporting Contract

**Files to Edit/Add:**
- Update relevant JS / backend handlers
- Add docs section or file for outcome reporting

**Actions:**
- Document the outcome POST payload for subordinate ops:
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

## Priority Checklist for Implementation

1. File/route normalization
2. Routing/config cleanup
3. Dashboard Execution Header integration
4. Protected ops gating
5. Login portal trust badge
6. Documentation consolidation
7. Outcome reporting contract


---

Here is the exact file-by-file edit plan for `DemeWebsolutions/Phantom.ai`, followed by the drafted GitHub issue.

## Exact file-by-file edit plan

### 1) Rename canonical entry files
**Rename:**
- `Dashboard.html` → `dashboard.html`
- `phantom.ai-login-portal.html` → `login-portal.html`

**Reason:**
- You specified lowercase only for a case-sensitive server.
- Canonical production paths are `/Phantom.ai/dashboard.html` and `/Phantom.ai/login-portal.html`.

### 2) Update `dashboard.html`
**Edit goals:**
- Replace all `/TruAi/` path references with `/Phantom.ai/` where they are Phantom UI routes.
- Add ROMA Execution Header.
- Add protected-operation gating hooks.
- Update file explorer labels to lowercase canonical filenames.
- Add or consume `window.PHANTOM_CONFIG`.

Relevant current reference:

```html name=Dashboard.html url=https://github.com/DemeWebsolutions/Phantom.ai/blob/5f7bdd09bdce591442768c68b1f4e9fef9a76d52/Dashboard.html#L766-L833
<nav class="topnav">
  <a class="topnav-brand" href="/TruAi/">
...
  <a href="/TruAi/login-portal.html">Login</a>
...
  <li class="file-item" onclick="selectFile(this, 'dashboard.html')">
...
  <li class="file-item" onclick="selectFile(this, 'login-portal.html')">
```

**Planned edits inside `dashboard.html`:**
- topnav brand URL:
  - from `/TruAi/`
  - to `/Phantom.ai/`
- login URL:
  - from `/TruAi/login-portal.html`
  - to `/Phantom.ai/login-portal.html`
- secure portal or sibling links:
  - review whether they are Phantom-local or external/shared
  - if Phantom-local, move to `/Phantom.ai/...`
- file explorer entries:
  - ensure `dashboard.html` and `login-portal.html` are canonical
- add config bootstrap near top:
  ```html
  <script>
  window.PHANTOM_CONFIG = {
    APP_BASE: (window.location.origin + '/Phantom.ai').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
    AUTH_API_BASE: (window.location.origin + '/Phantom.ai/api/v1').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
    ROMA_API_BASE: (window.location.origin + '/ROMA/api/v1').replace(/\/ROMA\/ROMA/, '/ROMA')
  };
  </script>
  ```
- insert Execution Header above main layout:
  - Trust State
  - Encryption badge
  - ROI
  - Escalation
  - System trust matrix
- add disabled state / block banner for protected ops when trust is not `VERIFIED`

### 3) Update `login-portal.html`
Current reference:

```html name=phantom.ai-login-portal.html url=https://github.com/DemeWebsolutions/Phantom.ai/blob/5f7bdd09bdce591442768c68b1f4e9fef9a76d52/phantom.ai-login-portal.html#L7-L18
window.TRUAI_CONFIG = {
  API_BASE: (window.location.origin + '/TruAi/api/v1')...
};
<script src="/TruAi/assets/js/api.js"></script>
```

**Planned edits inside `login-portal.html`:**
- rename file to lowercase canonical `login-portal.html`
- replace TruAi-specific config bootstrap with Phantom-specific config
- separate:
  - `AUTH_API_BASE`
  - `ROMA_API_BASE`
- remove hardcoded `/TruAi/assets/js/api.js` dependency if Phantom should own its own copy/path
- if shared JS must remain temporarily, document it as transitional
- add live ROMA trust indicator:
  - no static “protected” language before fetch resolves
  - render verified/unverified/unknown from live API result only
- ensure login redirects and success flows stay inside `/Phantom.ai/...`

### 4) Update `assets/js/api.js`
Current reference:

```javascript name=assets/js/api.js url=https://github.com/DemeWebsolutions/Phantom.ai/blob/5f7bdd09bdce591442768c68b1f4e9fef9a76d52/assets/js/api.js#L26-L128
window.location.href = '/TruAi/login-portal.html';
...
window.location.href = '/TruAi/login-portal.html';
```

**Planned edits inside `assets/js/api.js`:**
- replace all redirects to `/TruAi/login-portal.html` with `/Phantom.ai/login-portal.html`
- stop assuming TruAi path root
- read API bases from `window.PHANTOM_CONFIG`
- keep CSRF/session logic intact
- if auth endpoints remain Phantom-hosted:
  - point requests to `AUTH_API_BASE`
- do not use this module for ROMA trust polling unless intentionally merged

### 5) Add new `assets/js/roma-trust.js`
**New file purpose:**
- isolate ROMA runtime trust logic from generic API/auth code
- provide shared trust rendering for dashboard and login portal

**Planned responsibilities:**
- fetch `GET /api/v1/security/roma` from `ROMA_API_BASE`
- optionally fetch `GET /api/v1/trust/snapshot` if present in your environment
- normalize trust states for UI:
  - `VERIFIED`
  - `DEGRADED`
  - `UNVERIFIED`
  - `UNKNOWN`
  - `UNREACHABLE`
- expose helpers like:
  - `refreshTrustState()`
  - `renderExecutionHeader()`
  - `renderRomaBadge()`
  - `isProtectedOperationAllowed()`

Suggested stub:

```javascript name=assets/js/roma-trust.js
window.PHANTOM_TRUST = {
  state: 'UNKNOWN',
  raw: null,
  async refresh() {},
  isProtectedOperationAllowed() {
    return this.state === 'VERIFIED';
  }
};
```

### 6) Update `docs/deployment.md`
You previously noted this file is stale.

**Planned edits:**
- replace old route/file references with:
  - `/Phantom.ai/dashboard.html`
  - `/Phantom.ai/login-portal.html`
- note lowercase-only filename policy
- document ROMA API base requirement
- document that Phantom is subordinate to TruAi
- document trust-gated protected operations

### 7) Add `docs/phantom-update-plan-2026-03-10.md`
**New file purpose:**
- single planning entrypoint for the March 10, 2026 update
- reference the March 9, 2026 generated docs as supporting materials
- define what is normative vs informational

**Should include:**
- canonical routes
- governance statement
- ROMA trust rules
- protected operations list
- implementation phases
- dependency notes on TruAi/ROMA integration

### 8) Add `docs/phantom-outcome-contract.md`
**New file purpose:**
- define subordinate outcome reporting payloads for Phantom.ai
- document blocked vs successful operations
- keep TruAi as source-of-truth governance

**Should include payload examples for:**
- file write blocked
- review completed
- action escalated
- action executed under verified trust

### 9) Search-and-replace audit across Phantom.ai
Run a full repo pass for:
- `Dashboard.html`
- `phantom.ai-login-portal.html`
- `/TruAi/login-portal.html`
- `/TruAi/`
- `dashboard.html`
- `login-portal.html`

**Targets likely to need edits:**
- root HTML pages
- `assets/js/*.js`
- docs
- deployment instructions
- any router or menu definitions

### 10) Protected operation inventory in dashboard
Inside `dashboard.html` and related JS, mark as protected:
- apply/write actions
- execute/automation actions
- environment changes
- cross-system commands
- any operation that mutates workspace or state

**Gate behavior:**
- if trust != `VERIFIED`, block action and show:
  - `Execution blocked: ROMA trust unverified`

---

## Drafted GitHub issue

````yaml type="draft-issue"
type: draft-issue
tag: "phantomai-update-plan-mar2026"
issueNumber: 0
repository: "DemeWebsolutions/Phantom.ai"
state: "draft"
title: "Phantom.ai Update Plan — File-by-File Implementation Checklist (March 2026)"
description: |-
    ## File-by-File Edit Plan — Phantom.ai / March 2026

    ### Overview
    This implementation plan addresses file renaming, UI alignment, trust integration, routing normalization, and documentation improvements for Phantom.ai. The goal is cross-repo compatibility with TruAi governance and strict ROMA trust signaling.

    ---

    ### 1. Canonicalize Filenames and Routes

    **Files/Paths to Update:**
    - `Dashboard.html` → **rename to:** `dashboard.html` (lowercase)
    - `phantom.ai-login-portal.html` → **rename to:** `login-portal.html` (lowercase)
    - Update all UI and code references to use lowercase filenames and `/Phantom.ai/` path base:
      - Any links, redirects, or file items referencing `Dashboard.html`, `phantom.ai-login-portal.html`, `/TruAi/login-portal.html`, or mixed-case variants
      - Sidebar file explorer in dashboard
      - JavaScript redirect logic in `assets/js/api.js`

    **Actions:**
    - Search for all usages of `Dashboard.html`, `phantom.ai-login-portal.html`, and `/TruAi/login-portal.html` in both HTML and JS; replace with `/Phantom.ai/dashboard.html` and `/Phantom.ai/login-portal.html`.
    - Remove/retire uppercase and prefixed artifacts after migration.

    ---

    ### 2. Update Configuration and Routing Logic

    **Files to Edit:**
    - `dashboard.html` (after rename)
    - `login-portal.html` (after rename)
    - `assets/js/api.js`

    **Changes:**
    - Introduce a config object for Phantom-specific routing:
      ```js
      window.PHANTOM_CONFIG = {
        APP_BASE: (window.location.origin + '/Phantom.ai').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
        AUTH_API_BASE: (window.location.origin + '/Phantom.ai/api/v1').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai'),
        ROMA_API_BASE: (window.location.origin + '/ROMA/api/v1').replace(/\/ROMA\/ROMA/, '/ROMA')
      };
      ```
    - Remove any hardcoded `/TruAi/` path assumptions from UI and JS.
    - Update file explorer and internal routing logic.

    ---

    ### 3. Dashboard Execution Header & Trust Integration

    **Files to Edit:**
    - `dashboard.html`
    - `assets/js/roma-trust.js` (new or expanded)

    **Changes:**
    - Add the Execution Header (above dashboard’s main layout) as specified in ROMA’s dashboard spec:
      - Trust State (live from ROMA)
      - Encryption badge
      - Escalation state
      - ROI indicator
      - System trust matrix (on hover/expand)
    - Connect to ROMA’s API base (`ROMA_API_BASE`) for trust polling.
    - Block protected UI actions when trust is anything less than VERIFIED.
    - Add explicit copy for blocked state.

    ---

    ### 4. Login Portal Trust Integration

    **Files to Edit:**
    - `login-portal.html`
    - Any referenced JS (e.g., `assets/js/roma-trust.js`)

    **Changes:**
    - Add dynamic ROMA trust badge (fetch live from ROMA API base).
    - Remove static/pre-rendered “protected” claims. Only display trust badge if runtime-true.
    - Ensure session/cookie behavior aligns with Authentication Security doc.
    - Validate that all redirects, routing, or authentication endpoints are anchored in `/Phantom.ai/` paths.

    ---

    ### 5. Docs Consolidation

    **Files to Edit/Add:**
    - Add: `docs/phantom-update-plan-2026-03-10.md`
    - Update: `docs/deployment.md` (reference new routes, canonical filenames, protected ops, trust gating)
    - Document outcome reporting contract (add section or new file as needed):
      - Define POST payload for outcomes in `docs/phantom-outcome-contract.md` or in the plan

    ---

    ### 6. Protected Operations Gating

    **Files to Edit:**
    - `dashboard.html`
    - `assets/js/roma-trust.js`

    **Actions:**
    - Identify and gate all protected operations:
      - File apply/write
      - Automation/exec actions
      - Environment changes
      - Command dispatches
    - Only allow if ROMA trust state is VERIFIED.
    - Add code helpers to centralize gating logic.

    ---

    ### 7. Outcome Reporting Contract

    **Files to Edit/Add:**
    - Update relevant JS / backend handlers
    - Add docs section or file for outcome reporting

    **Actions:**
    - Document the outcome POST payload for subordinate ops:
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

    ## Priority Checklist for Implementation

    1. File/route normalization
    2. Routing/config cleanup
    3. Dashboard Execution Header integration
    4. Protected ops gating
    5. Login portal trust badge
    6. Documentation consolidation
    7. Outcome reporting contract
assignees: ["DemeWebsolutions"]
labels: ["update", "dashboard", "integration", "security", "login"]
````
