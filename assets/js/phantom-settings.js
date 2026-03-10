/**
 * Phantom.ai Settings Module
 *
 * Handles save, load, and test for Phantom.ai settings including
 * ChatGPT (OpenAI) and Claude (Anthropic) API credentials.
 *
 * Persistence: localStorage (primary) + optional API backend.
 */

'use strict';

window.PHANTOM_SETTINGS = (function () {

  var STORAGE_KEY = 'phantom_settings';

  // ── Default settings schema ──────────────────────────────────────────────
  var _defaults = {
    username:       '',
    theme:          'dark',
    fontSize:       13,
    keybinding:     'vscode',
    defaultModel:   'auto',
    openaiApiKey:   '',
    anthropicApiKey:'',
    streaming:      '1',
    dataSharing:    '0'
  };

  // ── Internal: load from localStorage ────────────────────────────────────
  function _loadFromStorage() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (raw) return Object.assign({}, _defaults, JSON.parse(raw));
    } catch (e) {}
    return Object.assign({}, _defaults);
  }

  // ── Internal: save to localStorage ──────────────────────────────────────
  function _saveToStorage(settings) {
    try {
      // Never persist plaintext keys to server; only localStorage (client-side)
      localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
    } catch (e) {
      console.error('Failed to save Phantom settings to localStorage');
    }
  }

  // ── Internal: try API backend (optional) ─────────────────────────────────
  async function _syncToBackend(settings) {
    var base = (window.PHANTOM_CONFIG && window.PHANTOM_CONFIG.AUTH_API_BASE)
      ? window.PHANTOM_CONFIG.AUTH_API_BASE
      : null;
    if (!base) return false;
    try {
      var resp = await fetch(base + '/settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ settings: settings })
      });
      return resp.ok;
    } catch (e) {
      return false;
    }
  }

  // ── Internal: try loading from API backend (optional) ────────────────────
  async function _loadFromBackend() {
    var base = (window.PHANTOM_CONFIG && window.PHANTOM_CONFIG.AUTH_API_BASE)
      ? window.PHANTOM_CONFIG.AUTH_API_BASE
      : null;
    if (!base) return null;
    try {
      var resp = await fetch(base + '/settings', { credentials: 'include' });
      if (!resp.ok) return null;
      var data = await resp.json();
      return data.settings || null;
    } catch (e) {
      return null;
    }
  }

  // ── Public API ───────────────────────────────────────────────────────────
  return {

    /**
     * Save settings. Persists to localStorage always; attempts API backend.
     * @param {object} settings
     */
    save: async function (settings) {
      var merged = Object.assign({}, _defaults, settings);
      _saveToStorage(merged);
      await _syncToBackend(merged);
      return merged;
    },

    /**
     * Load settings. Tries API backend first; falls back to localStorage.
     * @returns {object} settings
     */
    load: async function () {
      var backendSettings = await _loadFromBackend();
      if (backendSettings) {
        var merged = Object.assign({}, _defaults, backendSettings);
        _saveToStorage(merged); // sync backend → local
        return merged;
      }
      return _loadFromStorage();
    },

    /**
     * Load settings synchronously from localStorage only.
     * Use when async is not available (e.g., during initial page render).
     */
    loadSync: function () {
      return _loadFromStorage();
    },

    /**
     * Populate settings form fields from stored settings.
     * @param {object} settings
     */
    populateForm: function (settings) {
      var s = settings || _loadFromStorage();

      function setVal(id, val) {
        var el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === 'SELECT' || el.type === 'number' || el.type === 'text') {
          el.value = val !== undefined ? val : '';
        } else if (el.type === 'password') {
          el.value = val || '';
        }
      }

      setVal('settings-username',       s.username);
      setVal('settings-theme',          s.theme);
      setVal('settings-fontsize',       s.fontSize);
      setVal('settings-keybinding',     s.keybinding);
      setVal('settings-model',          s.defaultModel);
      setVal('settings-openai-key',     s.openaiApiKey);
      setVal('settings-anthropic-key',  s.anthropicApiKey);
      setVal('settings-streaming',      s.streaming);
      setVal('settings-data-sharing',   s.dataSharing);
    },

    /**
     * Read settings from form fields.
     * @returns {object} settings
     */
    readForm: function () {
      function getVal(id) {
        var el = document.getElementById(id);
        return el ? el.value : '';
      }
      return {
        username:        getVal('settings-username'),
        theme:           getVal('settings-theme'),
        fontSize:        Number(getVal('settings-fontsize')) || 13,
        keybinding:      getVal('settings-keybinding'),
        defaultModel:    getVal('settings-model'),
        openaiApiKey:    getVal('settings-openai-key'),
        anthropicApiKey: getVal('settings-anthropic-key'),
        streaming:       getVal('settings-streaming'),
        dataSharing:     getVal('settings-data-sharing')
      };
    },

    /**
     * Test ChatGPT (OpenAI) API key.
     * Shows result in element with id "openai-test-result" if present.
     */
    testChatGPTKey: async function () {
      var keyEl  = document.getElementById('settings-openai-key');
      var resEl  = document.getElementById('openai-test-result');
      var btnEl  = document.getElementById('btn-test-openai');

      var key = keyEl ? keyEl.value.trim() : '';

      if (!key) {
        if (resEl) { resEl.textContent = '⚠ Enter an OpenAI API key first.'; resEl.style.color = '#f59e0b'; }
        return;
      }

      if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Testing…'; }
      if (resEl) { resEl.textContent = 'Testing…'; resEl.style.color = '#a0aec0'; }

      try {
        var resp = await fetch('https://api.openai.com/v1/models', {
          headers: { 'Authorization': 'Bearer ' + key }
        });

        if (resp.ok) {
          if (resEl) { resEl.textContent = '✓ OpenAI key is valid.'; resEl.style.color = '#22c55e'; }
        } else if (resp.status === 401) {
          if (resEl) { resEl.textContent = '✗ Invalid API key (401 Unauthorized).'; resEl.style.color = '#ef4444'; }
        } else {
          if (resEl) { resEl.textContent = '⚠ OpenAI responded with HTTP ' + resp.status + '.'; resEl.style.color = '#f59e0b'; }
        }
      } catch (e) {
        if (resEl) {
          resEl.textContent = '⚠ Could not reach OpenAI (network error or CSP).';
          resEl.style.color = '#f59e0b';
        }
      } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Test'; }
      }
    },

    /**
     * Test Claude (Anthropic) API key.
     * Shows result in element with id "anthropic-test-result" if present.
     */
    testClaudeKey: async function () {
      var keyEl = document.getElementById('settings-anthropic-key');
      var resEl = document.getElementById('anthropic-test-result');
      var btnEl = document.getElementById('btn-test-anthropic');

      var key = keyEl ? keyEl.value.trim() : '';

      if (!key) {
        if (resEl) { resEl.textContent = '⚠ Enter an Anthropic API key first.'; resEl.style.color = '#f59e0b'; }
        return;
      }

      if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Testing…'; }
      if (resEl) { resEl.textContent = 'Testing…'; resEl.style.color = '#a0aec0'; }

      try {
        // Anthropic models endpoint for key validation
        var resp = await fetch('https://api.anthropic.com/v1/models', {
          headers: {
            'x-api-key': key,
            'anthropic-version': '2023-06-01'
          }
        });

        if (resp.ok) {
          if (resEl) { resEl.textContent = '✓ Anthropic key is valid.'; resEl.style.color = '#22c55e'; }
        } else if (resp.status === 401) {
          if (resEl) { resEl.textContent = '✗ Invalid API key (401 Unauthorized).'; resEl.style.color = '#ef4444'; }
        } else {
          if (resEl) { resEl.textContent = '⚠ Anthropic responded with HTTP ' + resp.status + '.'; resEl.style.color = '#f59e0b'; }
        }
      } catch (e) {
        if (resEl) {
          resEl.textContent = '⚠ Could not reach Anthropic (network error or CSP).';
          resEl.style.color = '#f59e0b';
        }
      } finally {
        if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Test'; }
      }
    }
  };

}());
