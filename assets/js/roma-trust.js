/**
 * Phantom.ai ROMA Trust Module
 *
 * Manages ROMA trust state for Phantom.ai dashboard and login portal.
 * Trust signals are runtime-true only — no static "protected" claims.
 *
 * ROMA Trust States:
 *   VERIFIED   — All checks passed; protected operations allowed
 *   DEGRADED   — Partial failure; escalation bias up; high-risk ops restricted
 *   UNVERIFIED — Trust broken; execution blocked
 *   UNKNOWN    — Not yet checked; execution held
 */

'use strict';

window.PHANTOM_TRUST = (function () {

  // ── Internal state ───────────────────────────────────────────────────────
  let _state      = 'UNKNOWN';
  let _raw        = null;
  let _lastFetch  = 0;
  let _listeners  = [];

  const POLL_INTERVAL_MS = 60000; // re-poll every 60 s

  // ── Helpers ──────────────────────────────────────────────────────────────
  function getRomaApiBase() {
    return (window.PHANTOM_CONFIG && window.PHANTOM_CONFIG.ROMA_API_BASE)
      ? window.PHANTOM_CONFIG.ROMA_API_BASE
      : (window.location.origin + '/Phantom.ai/api/v1').replace(/\/Phantom\.ai\/Phantom\.ai/, '/Phantom.ai');
  }

  function _notify() {
    _listeners.forEach(function (fn) { try { fn(_state, _raw); } catch (e) {} });
  }

  // ── Public API ───────────────────────────────────────────────────────────
  return {

    /**
     * Current trust state: VERIFIED | DEGRADED | UNVERIFIED | UNKNOWN
     */
    get state() { return _state; },

    /**
     * Raw API response object (may be null before first fetch).
     */
    get raw() { return _raw; },

    /**
     * Returns true only when trust is VERIFIED and protected ops are allowed.
     */
    isProtectedOpAllowed: function () {
      return _state === 'VERIFIED';
    },

    /**
     * Block a protected operation with explicit UX copy.
     * Returns true if operation is blocked; false if allowed to proceed.
     */
    gateProtectedOp: function (opName) {
      if (this.isProtectedOpAllowed()) return false; // proceed
      var msg;
      if (_state === 'UNVERIFIED') {
        msg = 'Execution blocked: ROMA trust is UNVERIFIED.\n\nThe operation "' + opName + '" cannot proceed until ROMA trust is verified.';
      } else if (_state === 'DEGRADED') {
        msg = 'Warning: ROMA trust is DEGRADED.\n\nHigh-risk operations are restricted until trust is restored.';
      } else {
        msg = 'Execution held: ROMA trust state is ' + _state + '.\n\nWaiting for trust verification before "' + opName + '" can proceed.';
      }
      alert(msg);
      return true; // blocked
    },

    /**
     * Fetch ROMA trust state from the API and update internal state.
     * Calls registered listeners after update.
     */
    fetchTrustState: async function () {
      var base = getRomaApiBase();
      try {
        var r = await fetch(base + '/security/roma', { credentials: 'include' });
        var d = await r.json();
        _raw = d;
        if (d.trust_state === 'VERIFIED')   _state = 'VERIFIED';
        else if (d.trust_state === 'DEGRADED')  _state = 'DEGRADED';
        else if (d.trust_state === 'UNVERIFIED') _state = 'UNVERIFIED';
        else                                     _state = 'UNKNOWN';
      } catch (e) {
        _raw   = null;
        _state = 'UNKNOWN';
      }
      _lastFetch = Date.now();
      _notify();
      return _state;
    },

    /**
     * Subscribe to trust state changes.
     * Callback receives (state, rawData).
     */
    onStateChange: function (fn) {
      if (typeof fn === 'function') _listeners.push(fn);
    },

    /**
     * Render a compact ROMA execution header element.
     * @param {HTMLElement} container - element to render into
     */
    renderExecutionHeader: function (container) {
      if (!container) return;

      var d = _raw || {};
      var checks = d.checks || {};

      var stateColor = {
        VERIFIED:   '#22c55e',
        DEGRADED:   '#f59e0b',
        UNVERIFIED: '#ef4444',
        UNKNOWN:    '#6b7280'
      }[_state] || '#6b7280';

      var stateLabel = {
        VERIFIED:   '✓ VERIFIED',
        DEGRADED:   '⚠ DEGRADED',
        UNVERIFIED: '✗ UNVERIFIED',
        UNKNOWN:    '◌ UNKNOWN'
      }[_state] || '◌ UNKNOWN';

      var executionStatus = _state === 'VERIFIED'
        ? 'Execution: Allowed'
        : _state === 'DEGRADED'
          ? 'Execution: Restricted'
          : _state === 'UNVERIFIED'
            ? 'Execution: BLOCKED'
            : 'Execution: Held';

      var encAlgo   = d.encryption_algorithm || (checks.encryption_keys ? 'AES-256-GCM' : '—');
      var roi       = (d.roi !== undefined) ? String(d.roi) : '—';
      var escalated = d.escalation_active ? '⬆ Escalation Active' : 'Normal';
      var sysCount  = d.systems_online !== undefined ? d.systems_online : '—';

      container.innerHTML =
        '<div style="display:flex;align-items:center;gap:16px;padding:6px 18px;' +
        'background:rgba(0,0,0,0.55);border-bottom:1px solid rgba(255,255,255,0.07);' +
        'font-size:11px;color:#a0aec0;letter-spacing:0.04em;flex-wrap:wrap;">' +

          '<span style="font-weight:700;color:' + stateColor + ';white-space:nowrap;">' +
            'ROMA ' + stateLabel +
          '</span>' +

          '<span style="color:#4b5563;">|</span>' +

          '<span style="white-space:nowrap;color:' +
            (_state === 'UNVERIFIED' ? '#ef4444' : (_state === 'DEGRADED' ? '#f59e0b' : '#a0aec0')) + ';">' +
            executionStatus +
          '</span>' +

          '<span style="color:#4b5563;">|</span>' +
          '<span style="white-space:nowrap;">Encrypt: ' + encAlgo + '</span>' +

          '<span style="color:#4b5563;">|</span>' +
          '<span style="white-space:nowrap;">Escalation: ' + escalated + '</span>' +

          '<span style="color:#4b5563;">|</span>' +
          '<span style="white-space:nowrap;">ROI: ' + roi + '</span>' +

          '<span style="color:#4b5563;">|</span>' +
          '<span style="white-space:nowrap;">Systems: ' + sysCount + '</span>' +

          '<span style="flex:1;"></span>' +
          '<span style="color:#4b5563;font-size:10px;white-space:nowrap;">' +
            (d.checked_at ? 'Checked: ' + new Date(d.checked_at).toLocaleTimeString() : 'Awaiting check') +
          '</span>' +

        '</div>';
    },

    /**
     * Start automatic polling.
     */
    startPolling: function () {
      var self = this;
      this.fetchTrustState();
      setInterval(function () { self.fetchTrustState(); }, POLL_INTERVAL_MS);
    }
  };

}());
