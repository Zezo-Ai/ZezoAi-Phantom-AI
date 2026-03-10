/**
 * Phantom.ai — ROMA Trust Module
 *
 * Fetches the live ROMA trust state from ROMA's API base and exposes
 * helpers used by dashboard.html and login-portal.html to gate
 * protected operations.
 *
 * Trust states (per ROMA TRUST_STATE_DASHBOARD_SPEC):
 *   VERIFIED   — all checks passed; auto-execution allowed
 *   DEGRADED   — partial failure; escalation bias ↑
 *   UNVERIFIED — trust broken; execution blocked
 *   UNKNOWN    — not yet checked; hold execution
 *
 * @package Phantom.ai
 */
'use strict';

window.PHANTOM_TRUST = {
  /** Current trust state string */
  state: 'UNKNOWN',
  /** Raw API payload (null until first fetch) */
  raw: null,
  /** Timestamp of last successful fetch */
  lastFetched: null,

  /**
   * Returns true only when protected operations are permitted.
   * Protected ops require state === 'VERIFIED'.
   */
  isProtectedOpAllowed() {
    return this.state === 'VERIFIED';
  },

  /**
   * Fetch trust state from ROMA API.
   * @param {string} romaApiBase  e.g. window.PHANTOM_CONFIG.ROMA_API_BASE
   * @returns {Promise<string>}   resolved trust state
   */
  async fetch(romaApiBase) {
    const endpoint = romaApiBase.replace(/\/$/, '') + '/security/roma';
    try {
      const r = await window.fetch(endpoint, { credentials: 'include' });
      if (!r.ok) {
        this.state = 'UNVERIFIED';
        this.raw = null;
        return this.state;
      }
      const d = await r.json();
      this.raw = d;
      this.lastFetched = Date.now();
      // Normalise to known states
      const reported = (d.trust_state || '').toUpperCase();
      if (['VERIFIED', 'DEGRADED', 'UNVERIFIED', 'UNKNOWN'].includes(reported)) {
        this.state = reported;
      } else if (d.trust_state === 'BLOCKED') {
        this.state = 'UNVERIFIED';
      } else {
        this.state = 'UNKNOWN';
      }
    } catch (_) {
      this.state = 'UNKNOWN';
      this.raw = null;
    }
    return this.state;
  },

  /**
   * Apply the trust state to a header element (id="roma-exec-header").
   * Creates or updates the element with badge colour and copy.
   */
  renderHeader() {
    const el = document.getElementById('roma-exec-header');
    if (!el) return;
    const cfg = {
      VERIFIED:   { cls: 'trust-verified',   icon: '✔', label: 'VERIFIED',   note: 'Execution permitted' },
      DEGRADED:   { cls: 'trust-degraded',   icon: '⚠', label: 'DEGRADED',   note: 'Escalation bias ↑ — high-risk actions restricted' },
      UNVERIFIED: { cls: 'trust-unverified', icon: '✖', label: 'UNVERIFIED', note: 'Execution blocked — ROMA trust unverified' },
      UNKNOWN:    { cls: 'trust-unknown',    icon: '…', label: 'UNKNOWN',    note: 'Holding execution — trust state not yet confirmed' },
    };
    const info = cfg[this.state] || cfg['UNKNOWN'];
    const enc = this.raw && this.raw.encryption_algorithm
      ? ' · ' + this.raw.encryption_algorithm
      : '';
    const roi = this.raw && this.raw.roi_indicator != null
      ? ' · ROI: ' + this.raw.roi_indicator
      : '';
    el.className = 'roma-exec-header ' + info.cls;
    el.innerHTML =
      '<span class="trust-icon">' + info.icon + '</span>' +
      '<strong>TRUST STATE: ' + info.label + '</strong>' +
      '<span class="trust-note">' + info.note + enc + roi + '</span>';
    this._applyGating();
  },

  /**
   * Disable / re-enable all protected-operation controls based on state.
   * Elements carrying [data-protected-op] are gated.
   */
  _applyGating() {
    const allowed = this.isProtectedOpAllowed();
    document.querySelectorAll('[data-protected-op]').forEach(el => {
      el.disabled = !allowed;
      el.title = allowed ? '' : 'Execution blocked: ROMA trust unverified';
      if (!allowed) {
        el.setAttribute('aria-disabled', 'true');
      } else {
        el.removeAttribute('aria-disabled');
      }
    });
    const blockMsg = document.getElementById('roma-block-message');
    if (blockMsg) {
      blockMsg.style.display = (this.state === 'UNVERIFIED') ? '' : 'none';
    }
  },

  /**
   * Convenience: fetch then render header.
   * @param {string} romaApiBase
   */
  async fetchAndRender(romaApiBase) {
    await this.fetch(romaApiBase);
    this.renderHeader();
  }
};
