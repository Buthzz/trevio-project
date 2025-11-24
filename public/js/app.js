/**
 * ===================================================================
 * TREVIO - App.js (IMPROVED)
 * Main application utilities and global functions
 * ===================================================================
 * Improvements:
 * - Fixed FormData iteration (use .entries())
 * - Enhanced error handling in HTTP requests
 * - Improved attribute handling (null/undefined removal)
 * - Better date formatting with regex global replace
 * - Safe deep copy with error handling
 * - Proper cleanup of all resources
 * - Input validation for all critical functions
 * ===================================================================
 */

'use strict';

const App = (function () {
  const API_BASE_URL = window.location.origin;
  const STORAGE_PREFIX = 'trevio_';
  const HTTP_TIMEOUT = 30000;

  const HTTP_STATUS = {
    OK: 200,
    CREATED: 201,
    BAD_REQUEST: 400,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    NOT_FOUND: 404,
    SERVER_ERROR: 500,
  };

  let eventListeners = new Map();
  let httpRequests = new Map();
  let formValidators = new Map();

  const log = (message, data = null) => {
    if (window.DEBUG === true) {
      console.log(`[TREVIO] ${message}`, data || '');
    }
  };

  const logError = (message, error = null) => {
    console.error(`[TREVIO ERROR] ${message}`, error || '');
  };

  const querySelector = (selector) => {
    try {
      return document.querySelector(selector);
    } catch (e) {
      logError(`Invalid selector: ${selector}`, e);
      return null;
    }
  };

  const querySelectorAll = (selector) => {
    try {
      return document.querySelectorAll(selector);
    } catch (e) {
      logError(`Invalid selector: ${selector}`, e);
      return [];
    }
  };

  const getElementById = (id) => {
    return document.getElementById(id);
  };

  const setHTML = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.textContent = text;
    }
  };

  const getText = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.textContent : '';
  };

  const setText = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.textContent = text;
    }
  };

  const addClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.add(...classList);
  };

  const removeClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.remove(...classList);
  };

  const toggleClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) el.classList.toggle(className);
  };

  const hasClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.classList.contains(className) : false;
  };

  const setAttribute = (element, attr, value = '') => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    if (typeof attr === 'object') {
      Object.entries(attr).forEach(([key, val]) => {
        if (val === null || val === undefined) {
          el.removeAttribute(key);
        } else {
          el.setAttribute(key, String(val));
        }
      });
    } else {
      if (value === null || value === undefined) {
        el.removeAttribute(attr);
      } else {
        el.setAttribute(attr, String(value));
      }
    }
  };

  const getAttribute = (element, attr) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.getAttribute(attr) : null;
  };

  const setStyle = (element, prop, value = '') => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    if (typeof prop === 'object') {
      Object.entries(prop).forEach(([key, val]) => {
        el.style[key] = val;
      });
    } else {
      el.style[prop] = value;
    }
  };

  const getStyle = (element, prop) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? window.getComputedStyle(el).getPropertyValue(prop) : '';
  };

  const show = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = '';
      el.setAttribute('aria-hidden', 'false');
    }
  };

  const hide = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    }
  };

  const toggleVisibility = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display === 'none' ? show(el) : hide(el);
    }
  };

  const on = (element, event, handler, options = {}) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el || typeof handler !== 'function') return;

    el.addEventListener(event, handler, options);
    const key = el.id || Math.random().toString(36).substr(2, 9);
    if (!el.id) el._listenerKey = key;
    
    const fullKey = `${key}:${event}`;
    if (!eventListeners.has(fullKey)) eventListeners.set(fullKey, []);
    eventListeners.get(fullKey).push({ element: el, handler, options });

    log(`Event listener added: ${event}`);
  };

  const off = (element, event, handler) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    el.removeEventListener(event, handler);
    const key = el.id || el._listenerKey;
    if (key) eventListeners.delete(`${key}:${event}`);
    
    log(`Event listener removed: ${event}`);
  };

  const once = (element, event, handler) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    const wrappedHandler = (e) => {
      handler(e);
      off(el, event, wrappedHandler);
    };
    on(el, event, wrappedHandler);
  };

  const trigger = (element, eventName, detail = {}) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    el.dispatchEvent(new CustomEvent(eventName, { detail, bubbles: true }));
  };

  const getFormData = (form) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl || !(formEl instanceof HTMLFormElement)) return {};

    const formData = new FormData(formEl);
    const data = {};

    for (const [key, value] of formData.entries()) {
      if (data.hasOwnProperty(key)) {
        if (Array.isArray(data[key])) {
          data[key].push(value);
        } else {
          data[key] = [data[key], value];
        }
      } else {
        data[key] = value;
      }
    }
    return data;
  };

  const setFormData = (form, data) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl || typeof data !== 'object') return;

    Object.entries(data).forEach(([key, value]) => {
      const field = formEl.elements[key];
      if (field) {
        if (field.type === 'checkbox' || field.type === 'radio') {
          field.checked = value;
        } else {
          field.value = value;
        }
      }
    });
  };

  const resetForm = (form) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (formEl && typeof formEl.reset === 'function') formEl.reset();
  };

  const disableForm = (form, disabled = true) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl) return;
    const inputs = formEl.querySelectorAll('input, textarea, select, button');
    inputs.forEach((input) => { input.disabled = disabled; });
  };

  const request = async (url, options = {}) => {
    if (!url || typeof url !== 'string') {
      logError('Invalid URL provided to request');
      return { success: false, status: 0, error: 'Invalid URL' };
    }

    const { method = 'GET', headers = {}, body = null, timeout = HTTP_TIMEOUT, cache = 'default' } = options;
    const controller = new AbortController();
    let timeoutId = null;

    try {
      timeoutId = setTimeout(() => controller.abort(), timeout);

      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', ...headers },
        body: body ? JSON.stringify(body) : null,
        signal: controller.signal,
        cache,
      });

      clearTimeout(timeoutId);
      timeoutId = null;

      let data;
      const contentType = response.headers.get('content-type') || '';

      try {
        if (contentType.includes('application/json')) {
          data = await response.json();
        } else if (contentType.includes('text')) {
          data = await response.text();
        } else {
          data = await response.blob();
        }
      } catch (parseError) {
        logError('Failed to parse response', parseError);
        data = response.statusText || '';
      }

      if (!response.ok) {
        const errorMessage = (typeof data === 'object' && data?.message) || `HTTP Error: ${response.status}`;
        throw new Error(errorMessage);
      }

      log(`Request successful: ${method} ${url}`);
      return { success: true, status: response.status, data };
    } catch (error) {
      if (timeoutId) clearTimeout(timeoutId);
      if (error.name === 'AbortError') {
        logError(`Request timeout: ${url}`);
        return { success: false, status: 408, error: 'Request timeout' };
      }
      logError(`Request failed: ${method} ${url}`, error);
      return { success: false, status: 0, error: error.message };
    }
  };

  const get = (url, options = {}) => request(url, { ...options, method: 'GET' });
  const post = (url, body = {}, options = {}) => request(url, { ...options, method: 'POST', body });
  const put = (url, body = {}, options = {}) => request(url, { ...options, method: 'PUT', body });
  const deleteRequest = (url, options = {}) => request(url, { ...options, method: 'DELETE' });

  const setStorage = (key, value) => {
    try {
      const storageKey = `${STORAGE_PREFIX}${key}`;
      const stringValue = typeof value === 'string' ? value : JSON.stringify(value);
      localStorage.setItem(storageKey, stringValue);
      log(`Storage set: ${key}`);
    } catch (e) {
      logError(`Storage set failed for key: ${key}`, e);
    }
  };

  const getStorage = (key, defaultValue = null) => {
    try {
      const storageKey = `${STORAGE_PREFIX}${key}`;
      const value = localStorage.getItem(storageKey);
      if (value === null) return defaultValue;
      try {
        return JSON.parse(value);
      } catch {
        return value;
      }
    } catch (e) {
      logError(`Storage get failed for key: ${key}`, e);
      return defaultValue;
    }
  };

  const removeStorage = (key) => {
    try {
      localStorage.removeItem(`${STORAGE_PREFIX}${key}`);
      log(`Storage removed: ${key}`);
    } catch (e) {
      logError(`Storage remove failed for key: ${key}`, e);
    }
  };

  const clearStorage = () => {
    try {
      Object.keys(localStorage).forEach((key) => {
        if (key.startsWith(STORAGE_PREFIX)) localStorage.removeItem(key);
      });
      log('Storage cleared');
    } catch (e) {
      logError('Storage clear failed', e);
    }
  };

  const getUser = () => getStorage('user', null);
  const setUser = (user) => setStorage('user', user);
  const clearSession = () => clearStorage();
  const isAuthenticated = () => getUser() !== null;

  const formatCurrency = (amount) => {
    if (typeof amount !== 'number') return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
  };

  const formatDate = (date, format = 'DD/MM/YYYY') => {
    if (!date) return '';
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');

    return format.replace(/DD/g, day).replace(/MM/g, month).replace(/YYYY/g, year).replace(/HH/g, hours).replace(/mm/g, minutes).replace(/ss/g, seconds);
  };

  const debounce = (func, delay = 300) => {
    let timeoutId;
    return function (...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
  };

  const throttle = (func, limit = 300) => {
    let inThrottle;
    return function (...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => (inThrottle = false), limit);
      }
    };
  };

  const deepCopy = (obj) => {
    try {
      return JSON.parse(JSON.stringify(obj));
    } catch (e) {
      logError('Failed to deep copy object', e);
      return obj;
    }
  };

  const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

  const init = () => {
    log('App initialized');
    window.addEventListener('error', (event) => { logError('Global error', event.error); });
    window.addEventListener('unhandledrejection', (event) => { logError('Unhandled promise rejection', event.reason); });
    document.addEventListener('visibilitychange', () => { log(document.hidden ? 'Page hidden' : 'Page visible'); });
  };

  const cleanup = () => {
    eventListeners.forEach((listeners) => {
      listeners.forEach(({ element, handler }) => {
        if (element && element.removeEventListener) {
          try {
            ['click', 'change', 'submit', 'input', 'focus', 'blur'].forEach(evt => element.removeEventListener(evt, handler));
          } catch (e) {}
        }
      });
    });
    eventListeners.clear();
    httpRequests.clear();
    formValidators.clear();
    log('App cleaned up');
  };

  return {
    API_BASE_URL, STORAGE_PREFIX, HTTP_STATUS,
    querySelector, querySelectorAll, getElementById, setHTML, getText, setText,
    addClass, removeClass, toggleClass, hasClass, setAttribute, getAttribute,
    setStyle, getStyle, show, hide, toggleVisibility,
    on, off, once, trigger,
    getFormData, setFormData, resetForm, disableForm,
    request, get, post, put, deleteRequest,
    setStorage, getStorage, removeStorage, clearStorage,
    getUser, setUser, clearSession, isAuthenticated,
    formatCurrency, formatDate, debounce, throttle, deepCopy, wait,
    init, cleanup, log, logError,
  };
})();

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => App.init());
} else {
  App.init();
}

window.addEventListener('beforeunload', () => App.cleanup());
