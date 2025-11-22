/**
 * ===================================================================
 * TREVIO - App.js
 * Main application utilities and global functions
 * ===================================================================
 * Fitur:
 * - DOM utilities dan helpers
 * - Event handling management
 * - Form validation
 * - HTTP requests wrapper
 * - Notification system
 * - Session dan storage management
 * ===================================================================
 */

'use strict';

/**
 * App Global Object
 * Menyediakan utility functions untuk seluruh aplikasi
 */
const App = (function () {
  // ================================================================
  // PRIVATE CONSTANTS
  // ================================================================
  const API_BASE_URL = window.location.origin;
  const STORAGE_PREFIX = 'trevio_';
  const HTTP_TIMEOUT = 30000; // 30 seconds

  // HTTP Status Codes
  const HTTP_STATUS = {
    OK: 200,
    CREATED: 201,
    BAD_REQUEST: 400,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    NOT_FOUND: 404,
    SERVER_ERROR: 500,
  };

  // ================================================================
  // PRIVATE VARIABLES
  // ================================================================
  let eventListeners = new Map();
  let httpRequests = new Map();
  let formValidators = new Map();

  // ================================================================
  // UTILITY FUNCTIONS
  // ================================================================

  /**
   * Safe console.log untuk debugging
   * @param {string} message - Pesan yang ingin ditampilkan
   * @param {*} data - Data tambahan (optional)
   */
  const log = (message, data = null) => {
    if (window.DEBUG === true) {
      console.log(`[TREVIO] ${message}`, data || '');
    }
  };

  /**
   * Safe console.error untuk error reporting
   * @param {string} message - Pesan error
   * @param {Error} error - Error object (optional)
   */
  const logError = (message, error = null) => {
    console.error(`[TREVIO ERROR] ${message}`, error || '');
  };

  // ================================================================
  // DOM UTILITIES
  // ================================================================

  /**
   * Dapatkan element dengan selector
   * @param {string} selector - CSS selector
   * @returns {Element|null} Element atau null jika tidak ditemukan
   */
  const querySelector = (selector) => {
    try {
      return document.querySelector(selector);
    } catch (e) {
      logError(`Invalid selector: ${selector}`, e);
      return null;
    }
  };

  /**
   * Dapatkan multiple elements dengan selector
   * @param {string} selector - CSS selector
   * @returns {NodeList} NodeList dari elements
   */
  const querySelectorAll = (selector) => {
    try {
      return document.querySelectorAll(selector);
    } catch (e) {
      logError(`Invalid selector: ${selector}`, e);
      return [];
    }
  };

  /**
   * Dapatkan element by ID
   * @param {string} id - Element ID
   * @returns {Element|null} Element atau null
   */
  const getElementById = (id) => {
    return document.getElementById(id);
  };

 /**
   * Set text content (LEBIH AMAN)
   * @param {Element|string} element - Element atau selector
   * @param {string} text - Text content
   */
  const setHTML = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      // GANTI DARI innerHTML KE textContent
      el.textContent = text; 
    }
  };

  /**
   * Get inner text dari element
   * @param {Element|string} element - Element atau selector
   * @returns {string} Text content
   */
  const getText = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.textContent : '';
  };

  /**
   * Set text content ke element
   * @param {Element|string} element - Element atau selector
   * @param {string} text - Text content
   */
  const setText = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.textContent = text;
    }
  };

  /**
   * Add CSS class ke element
   * @param {Element|string} element - Element atau selector
   * @param {string|Array} classes - Class name atau array of classes
   */
  const addClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.add(...classList);
  };

  /**
   * Remove CSS class dari element
   * @param {Element|string} element - Element atau selector
   * @param {string|Array} classes - Class name atau array of classes
   */
  const removeClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.remove(...classList);
  };

  /**
   * Toggle CSS class pada element
   * @param {Element|string} element - Element atau selector
   * @param {string} className - Class name
   */
  const toggleClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.classList.toggle(className);
    }
  };

  /**
   * Check apakah element memiliki class
   * @param {Element|string} element - Element atau selector
   * @param {string} className - Class name
   * @returns {boolean}
   */
  const hasClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.classList.contains(className) : false;
  };

  /**
   * Set attribute pada element
   * @param {Element|string} element - Element atau selector
   * @param {string|Object} attr - Attribute name atau object of attributes
   * @param {string} value - Attribute value (jika attr adalah string)
   */
  const setAttribute = (element, attr, value = '') => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    if (typeof attr === 'object') {
      Object.entries(attr).forEach(([key, val]) => {
        el.setAttribute(key, val);
      });
    } else {
      el.setAttribute(attr, value);
    }
  };

  /**
   * Get attribute dari element
   * @param {Element|string} element - Element atau selector
   * @param {string} attr - Attribute name
   * @returns {string|null}
   */
  const getAttribute = (element, attr) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.getAttribute(attr) : null;
  };

  /**
   * Set style pada element
   * @param {Element|string} element - Element atau selector
   * @param {string|Object} prop - CSS property atau object of styles
   * @param {string} value - CSS value (jika prop adalah string)
   */
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

  /**
   * Get computed style dari element
   * @param {Element|string} element - Element atau selector
   * @param {string} prop - CSS property
   * @returns {string}
   */
  const getStyle = (element, prop) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? window.getComputedStyle(el).getPropertyValue(prop) : '';
  };

  /**
   * Show element
   * @param {Element|string} element - Element atau selector
   */
  const show = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = '';
      el.setAttribute('aria-hidden', 'false');
    }
  };

  /**
   * Hide element
   * @param {Element|string} element - Element atau selector
   */
  const hide = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    }
  };

  /**
   * Toggle visibility element
   * @param {Element|string} element - Element atau selector
   */
  const toggleVisibility = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      if (el.style.display === 'none') {
        show(el);
      } else {
        hide(el);
      }
    }
  };

  // ================================================================
  // EVENT MANAGEMENT
  // ================================================================

  /**
   * Add event listener ke element dengan cleanup support
   * @param {Element|string} element - Element atau selector
   * @param {string} event - Event type
   * @param {Function} handler - Event handler
   * @param {Object} options - Event options
   */
  const on = (element, event, handler, options = {}) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    el.addEventListener(event, handler, options);

    // Store untuk cleanup - gunakan element reference, bukan string selector
    const key = el.id || Math.random().toString(36).substr(2, 9);
    if (!el.id && !key.startsWith('0')) {
      el._listenerKey = key;
    }
    
    const fullKey = `${key}:${event}`;
    if (!eventListeners.has(fullKey)) {
      eventListeners.set(fullKey, []);
    }
    eventListeners.get(fullKey).push({ element: el, handler, options });

    log(`Event listener added: ${event}`);
  };

  /**
   * Remove event listener dari element
   * @param {Element|string} element - Element atau selector
   * @param {string} event - Event type
   * @param {Function} handler - Event handler
   */
  const off = (element, event, handler) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    el.removeEventListener(event, handler);
    
    // Cleanup dari tracking map
    const key = el.id || el._listenerKey;
    if (key) {
      const fullKey = `${key}:${event}`;
      eventListeners.delete(fullKey);
    }
    
    log(`Event listener removed: ${event}`);
  };

  /**
   * Add one-time event listener
   * @param {Element|string} element - Element atau selector
   * @param {string} event - Event type
   * @param {Function} handler - Event handler
   */
  const once = (element, event, handler) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    const wrappedHandler = (e) => {
      handler(e);
      off(el, event, wrappedHandler);
    };

    on(el, event, wrappedHandler);
  };

  /**
   * Trigger custom event
   * @param {Element|string} element - Element atau selector
   * @param {string} eventName - Event name
   * @param {Object} detail - Event detail data
   */
  const trigger = (element, eventName, detail = {}) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    const event = new CustomEvent(eventName, { detail, bubbles: true });
    el.dispatchEvent(event);
  };

  // ================================================================
  // FORM UTILITIES
  // ================================================================

  /**
   * Get form data sebagai object
   * @param {Element|string} form - Form element atau selector
   * @returns {Object} Form data object
   */
  const getFormData = (form) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl) return {};

    const formData = new FormData(formEl);
    const data = {};

    for (const [key, value] of formData) {
      if (data[key]) {
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

  /**
   * Set form data dari object
   * @param {Element|string} form - Form element atau selector
   * @param {Object} data - Data object
   */
  const setFormData = (form, data) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl) return;

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

  /**
   * Reset form ke kondisi awal
   * @param {Element|string} form - Form element atau selector
   */
  const resetForm = (form) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (formEl) {
      formEl.reset();
    }
  };

  /**
   * Disable form elements
   * @param {Element|string} form - Form element atau selector
   * @param {boolean} disabled - Disable status
   */
  const disableForm = (form, disabled = true) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl) return;

    const inputs = formEl.querySelectorAll('input, textarea, select, button');
    inputs.forEach((input) => {
      input.disabled = disabled;
    });
  };

  // ================================================================
  // HTTP REQUESTS
  // ================================================================

  /**
   * Make HTTP request
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @returns {Promise} Response promise
   */
  const request = async (url, options = {}) => {
    const {
      method = 'GET',
      headers = {},
      body = null,
      timeout = HTTP_TIMEOUT,
      cache = 'default',
    } = options;

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);

    try {
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          ...headers,
        },
        body: body ? JSON.stringify(body) : null,
        signal: controller.signal,
        cache,
      });

      clearTimeout(timeoutId);

      // Handle response
      let data;
      const contentType = response.headers.get('content-type');

      try {
        if (contentType && contentType.includes('application/json')) {
          data = await response.json();
        } else {
          data = await response.text();
        }
      } catch (parseError) {
        logError('Failed to parse response', parseError);
        data = response.statusText || '';
      }

      if (!response.ok) {
        throw new Error(
          (typeof data === 'object' && data.message) || `HTTP Error: ${response.status}`
        );
      }

      log(`Request successful: ${method} ${url}`);
      return { success: true, status: response.status, data };
    } catch (error) {
      clearTimeout(timeoutId);

      if (error.name === 'AbortError') {
        logError(`Request timeout: ${url}`);
        return {
          success: false,
          status: 408,
          error: 'Request timeout',
        };
      }

      logError(`Request failed: ${method} ${url}`, error);
      return { success: false, status: 0, error: error.message };
    }
  };

  /**
   * GET request
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @returns {Promise}
   */
  const get = (url, options = {}) => {
    return request(url, { ...options, method: 'GET' });
  };

  /**
   * POST request
   * @param {string} url - Request URL
   * @param {Object} body - Request body
   * @param {Object} options - Request options
   * @returns {Promise}
   */
  const post = (url, body = {}, options = {}) => {
    return request(url, { ...options, method: 'POST', body });
  };

  /**
   * PUT request
   * @param {string} url - Request URL
   * @param {Object} body - Request body
   * @param {Object} options - Request options
   * @returns {Promise}
   */
  const put = (url, body = {}, options = {}) => {
    return request(url, { ...options, method: 'PUT', body });
  };

  /**
   * DELETE request
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @returns {Promise}
   */
  const deleteRequest = (url, options = {}) => {
    return request(url, { ...options, method: 'DELETE' });
  };

  // ================================================================
  // STORAGE UTILITIES
  // ================================================================

  /**
   * Set value di localStorage
   * @param {string} key - Storage key
   * @param {*} value - Value (akan di-stringify jika object/array)
   */
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

  /**
   * Get value dari localStorage
   * @param {string} key - Storage key
   * @param {*} defaultValue - Default value jika key tidak ada
   * @returns {*} Value dari storage
   */
  const getStorage = (key, defaultValue = null) => {
    try {
      const storageKey = `${STORAGE_PREFIX}${key}`;
      const value = localStorage.getItem(storageKey);

      if (value === null) {
        return defaultValue;
      }

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

  /**
   * Remove value dari localStorage
   * @param {string} key - Storage key
   */
  const removeStorage = (key) => {
    try {
      const storageKey = `${STORAGE_PREFIX}${key}`;
      localStorage.removeItem(storageKey);
      log(`Storage removed: ${key}`);
    } catch (e) {
      logError(`Storage remove failed for key: ${key}`, e);
    }
  };

  /**
   * Clear semua storage dengan prefix TREVIO
   */
  const clearStorage = () => {
    try {
      const keys = Object.keys(localStorage);
      keys.forEach((key) => {
        if (key.startsWith(STORAGE_PREFIX)) {
          localStorage.removeItem(key);
        }
      });
      log('Storage cleared');
    } catch (e) {
      logError('Storage clear failed', e);
    }
  };

  // ================================================================
  // SESSION UTILITIES
  // ================================================================

  /**
   * Get current session user
   * @returns {Object|null}
   */
  const getUser = () => {
    return getStorage('user', null);
  };

  /**
   * Set current session user
   * @param {Object} user - User object
   */
  const setUser = (user) => {
    setStorage('user', user);
  };

  /**
   * Clear current session
   */
  const clearSession = () => {
    clearStorage();
  };

  /**
   * Check apakah user authenticated
   * @returns {boolean}
   */
  const isAuthenticated = () => {
    return getUser() !== null;
  };

  // ================================================================
  // UTILITY HELPERS
  // ================================================================

  /**
   * Format currency dengan IDR
   * @param {number} amount - Amount
   * @returns {string}
   */
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount);
  };

  /**
   * Format date
   * @param {Date|string} date - Date object atau string
   * @param {string} format - Format pattern (optional)
   * @returns {string}
   */
  const formatDate = (date, format = 'DD/MM/YYYY') => {
    const d = new Date(date);
    if (isNaN(d.getTime())) return '';

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');

    return format
      .replace('DD', day)
      .replace('MM', month)
      .replace('YYYY', year)
      .replace('HH', hours)
      .replace('mm', minutes)
      .replace('ss', seconds);
  };

  /**
   * Debounce function
   * @param {Function} func - Function to debounce
   * @param {number} delay - Delay in milliseconds
   * @returns {Function}
   */
  const debounce = (func, delay = 300) => {
    let timeoutId;
    return function (...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
  };

  /**
   * Throttle function
   * @param {Function} func - Function to throttle
   * @param {number} limit - Limit in milliseconds
   * @returns {Function}
   */
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

  /**
   * Deep copy object
   * @param {Object} obj - Object to copy
   * @returns {Object}
   */
  const deepCopy = (obj) => {
    return JSON.parse(JSON.stringify(obj));
  };

  /**
   * Wait/delay execution
   * @param {number} ms - Milliseconds
   * @returns {Promise}
   */
  const wait = (ms) => {
    return new Promise((resolve) => setTimeout(resolve, ms));
  };

  // ================================================================
  // INITIALIZATION
  // ================================================================

  /**
   * Initialize application
   */
  const init = () => {
    log('App initialized');

    // Setup global error handler
    window.addEventListener('error', (event) => {
      logError('Global error', event.error);
    });

    // Setup unhandled promise rejection
    window.addEventListener('unhandledrejection', (event) => {
      logError('Unhandled promise rejection', event.reason);
    });

    // Setup page visibility
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        log('Page hidden');
      } else {
        log('Page visible');
      }
    });
  };

  // ================================================================
  // CLEANUP
  // ================================================================

  /**
   * Cleanup semua event listeners
   */
  const cleanup = () => {
    eventListeners.forEach((listeners) => {
      listeners.forEach(({ element, handler, options }) => {
        if (element && element.removeEventListener) {
          Object.keys(options || {}).forEach((eventType) => {
            try {
              element.removeEventListener(eventType, handler, options[eventType]);
            } catch (e) {
              // Ignore errors during cleanup
            }
          });
          // Remove event listener dari map
          element.removeEventListener('*', handler);
        }
      });
    });
    eventListeners.clear();
    log('App cleaned up');
  };

  // ================================================================
  // PUBLIC API
  // ================================================================

  return {
    // Constants
    API_BASE_URL,
    STORAGE_PREFIX,
    HTTP_STATUS,

    // DOM utilities
    querySelector,
    querySelectorAll,
    getElementById,
    setHTML,
    getText,
    setText,
    addClass,
    removeClass,
    toggleClass,
    hasClass,
    setAttribute,
    getAttribute,
    setStyle,
    getStyle,
    show,
    hide,
    toggleVisibility,

    // Event management
    on,
    off,
    once,
    trigger,

    // Form utilities
    getFormData,
    setFormData,
    resetForm,
    disableForm,

    // HTTP requests
    request,
    get,
    post,
    put,
    deleteRequest,

    // Storage utilities
    setStorage,
    getStorage,
    removeStorage,
    clearStorage,

    // Session utilities
    getUser,
    setUser,
    clearSession,
    isAuthenticated,

    // Utility helpers
    formatCurrency,
    formatDate,
    debounce,
    throttle,
    deepCopy,
    wait,

    // Lifecycle
    init,
    cleanup,

    // Debugging
    log,
    logError,
  };
})();

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => App.init());
} else {
  App.init();
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => App.cleanup());
