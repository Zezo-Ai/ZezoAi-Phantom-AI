# TruAi Core Architecture

Generated: 2026-03-09T06:14:49.979190 UTC

## Overview

TruAi Core is the governance and orchestration layer for the My Deme AI
ecosystem. It acts as the **source of truth**, controlling execution
authority, escalation logic, and risk governance across subordinate
systems.

## Responsibilities

-   AI orchestration
-   Risk evaluation
-   ROI calculation
-   Execution approval gating
-   Governance enforcement
-   Audit logging

## System Authority

TruAi Core maintains final authority over:

  Capability                      Authority
  ------------------------------- -----------
  Execution approval              TruAi
  Risk classification             TruAi
  Policy definition               TruAi
  Subordinate system governance   TruAi

Subordinate systems cannot override TruAi decisions.

## Subordinate Systems

-   Gemini.ai --- server executor
-   Phantom.ai --- project orchestration system
-   ROMA --- security substrate

## Execution Flow

User → TruAi Core → Risk Evaluation → Approval → Subordinate Execution →
Audit Return

## Escalation Logic

Tasks escalate when:

-   Risk exceeds allowed threshold
-   ROI efficiency drops below acceptable value
-   Security trust state degrades
