# Phantom AI: WordPress Compliance Software

Phantom is a compliance and review toolchain that unifies WordPress coding standards, PHP compatibility checks, security heuristics, accessibility patterns, and AI-assisted triage into one CI-ready command.

- Deep static checks: PHPCS + WPCS, PHPCompatibilityWP, Semgrep (WordPress-focused rules)
- Readme/i18n and basic accessibility checks (static)
- Unified JSON + SARIF output for CI and GitHub code scanning
- Optional AI post-processing to correlate findings and propose patches

## Quick start

1) Requirements
- PHP 8.2+, Composer
- Python 3.10+ (for report conversion and custom checks)
- jq, bash
- Semgrep (installed in CI step below)
- GitHub Actions (optional but recommended)

2) Install dev dependencies
```bash
composer install
```

3) Run locally
```bash
bash ./scripts/wp-plugin-ai-review.sh /path/to/your-wordpress-plugin
```

Artifacts will be written to `./artifacts`:
- `phantom-report.json`: unified findings
- `phantom-report.sarif`: uploadable to GitHub code scanning
- `phpcs.json`, `phpcompat.json`, `semgrep.json`: raw tool outputs

4) CI
A GitHub Actions workflow is included at `.github/workflows/phantom-audit.yml`. On each push/PR, it:
- Sets up PHP
- Installs PHPCS standards
- Installs jq and Semgrep
- Runs the Phantom review script
- Uploads SARIF to the Security tab (Code scanning alerts)

## Configuration

Phantom reads optional settings from `.phantom.yml`:
- PHP versions for compatibility checks
- Include/exclude paths
- Toggle checks (i18n, readme, a11y)
- AI review mode and provider (optional)

## Exit codes
- Exit non-zero if any WARNING or ERROR is present in the aggregated report (configurable in future).

## Credits and Ownership

- Credits: demewebsolutions.com / Kenneth "Demetrius" Weaver / My Deme, Llc.
- Proprietary software — all rights reserved. No open-source license is granted.

## Legal

This repository is proprietary. No permission is granted to use, copy, modify, merge, publish, distribute, sublicense, or sell copies of the software unless you obtain a commercial or written license from the copyright holder.

For licensing inquiries, contact: https://demewebsolutions.com/phantom-ai