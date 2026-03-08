# Phantom.ai

Phantom is a compliance and review toolchain that unifies WordPress coding standards, PHP compatibility checks, security heuristics, accessibility patterns, and AI-assisted triage into one CI-ready command.

## Portal Pages

| Page | File |
|------|------|
| Login Portal | `Phantom.ai.portal.html` |
| Dashboard | `phantom-defined.html` |
| Workspace | `Phantom.ai.workspace.html` |
| Review | `Phantom.ai.review.html` |
| Files | `Phantom.ai.files.html` |
| Audit Log | `Phantom.ai.auditlog.html` |
| Settings | `Phantom.ai.settings.html` |
| AI Screen | `Phantom.ai-screen.html` |

## Quick Start

Open `Phantom.ai.portal.html` in a browser to launch the portal.
For full deployment, see `DEPLOYMENT_GUIDE.md`.
For backend implementation, see `BACKEND_ARCHITECTURE.md`.

## Core Engine (phantom-ai/)

The `phantom-ai/` directory contains the PHP core:
- `Core/TierManager.php` — Three-tier AI execution system
- `Workflow/TaskRouter.php` — Task routing and escalation
- `Learning/MetadataTracker.php` — Task metadata storage
- `Learning/LearningEngine.php` — Learning and optimization
- `Templates/` — Copilot checklist, execution plan, WordPress templates
- `Assets/` — SVG design assets (`phantom-ai-01.svg`, `phantom-ai-02.svg`)
- `phantom-cli` — CLI interface

## Scripts (scripts/)

| Script | Purpose |
|--------|---------|
| `run_phantom_against.sh` | Run Phantom against a target |
| `wp-plugin-ai-review.sh` | WordPress plugin AI review |
| `check_accessibility_static.py` | Static accessibility checks |
| `check_readme_i18n.py` | i18n checks for readme |
| `to_sarif.py` | Convert output to SARIF format |
| `semgrep/` | Semgrep rules |

## Assets

- `assets/phantom-ai-01.svg` — Primary Phantom.ai logo
- `assets/phantom-ai-02.svg` — Neural network variant logo

## Security

See `SECURITY.md` and `SECURITY_IMPLEMENTATION_NOTES.md`.

## Deployment

See `DEPLOYMENT_GUIDE.md` for complete deployment instructions.
See `BACKEND_ARCHITECTURE.md` for backend (Node.js/PostgreSQL) setup.

## License

See `LICENSE` and `NOTICE`.

© 2013–2026 My Deme, LLC. All Rights Reserved.
