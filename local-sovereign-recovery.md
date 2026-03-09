# Local Sovereign Recovery Protocol (LSRP)

Generated: 2026-03-09T06:14:49.979190 UTC

## Purpose

LSRP replaces traditional password reset systems with a **local
multi-factor recovery mechanism**.

No email or SMS recovery is used.

## Recovery Factors

  Factor                    Requirement
  ------------------------- -------------
  Local server access       Required
  OS admin verification     Required
  ROMA trust verification   Required
  Device fingerprint        Required

If any factor fails recovery is blocked.

## Temporary Password Generation

Temporary passwords must:

-   Be 24+ characters
-   Be cryptographically random
-   Be single-use
-   Require immediate password rotation

## Master Recovery Key

A 256-bit master key is generated during setup.

Properties:

-   Displayed once
-   Stored only as a hash
-   Used for catastrophic recovery

Use requires ROMA verification and local access.
