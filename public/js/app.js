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
 * - Configurable timeouts and options
 * - Request cancellation API
 * - Standardized error responses
 * - JSDoc documentation for public APIs
 * ===================================================================
 */

'use strict';

const App = (function () {
  const API_BASE_URL = window.location.origin;
  const STORAGE_PREFIX = 'trevio_';

  /**
   * Configuration object - can be customized at runtime
   * @type {Object}
   */
  const CONFIG = {
    HTTP_TIMEOUT: 30000,
    DEBUG: false,
  };

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

  /**
   * @typedef {Object} ApiResponse
   * @property {boolean} success - Whether request succeeded
   * @property {number} status - HTTP status code
   * @property {*} data - Response data (optional)
   * @property {string} error - Error message (optional)
   */

  /**
   * Creates standardized error response
   * @param {number} status - HTTP status code
   * @param {string} message - Error message
   * @returns {ApiResponse} Standardized error response
   * @private
   */
  const createErrorResponse = (status, message) => ({
    success: false,
    status,
    error: message,
  });

  /**
   * Creates standardized success response
   * @param {number} status - HTTP status code
   * @param {*} data - Response data
   * @returns {ApiResponse} Standardized success response
   * @private
   */
  const createSuccessResponse = (status, data) => ({
    success: true,
    status,
    data,
  });

  /**
   * Logs message to console when DEBUG is enabled
   * @param {string} message - Message to log
   * @param {*} data - Optional data to log
   * @returns {void}
   */
  const log = (message, data = null) => {
    if (CONFIG.DEBUG === true) {
      console.log(`[TREVIO] ${message}`, data || '');
    }
  };

  /**
   * Logs error to console
   * @param {string} message - Error message
   * @param {Error} error - Optional error object
   * @returns {void}
   */
  const logError = (message, error = null) => {
    console.error(`[TREVIO ERROR] ${message}`, error || '');
  };

  /**
   * Queries single DOM element
   * @param {string} selector - CSS selector
   * @returns {Element|null} DOM element or null
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
   * Queries multiple DOM elements
   * @param {string} selector - CSS selector
   * @returns {NodeList} DOM elements collection
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
   * Gets element by ID
   * @param {string} id - Element ID
   * @returns {Element|null} DOM element or null
   */
  const getElementById = (id) => {
    return document.getElementById(id);
  };

  /**
   * Sets element's text content
   * @param {Element|string} element - DOM element or selector
   * @param {string} text - Text content to set
   * @returns {void}
   */
  const setHTML = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.textContent = text;
    }
  };

  /**
   * Gets element's text content
   * @param {Element|string} element - DOM element or selector
   * @returns {string} Element's text content
   */
  const getText = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.textContent : '';
  };

  /**
   * Sets element's text content
   * @param {Element|string} element - DOM element or selector
   * @param {string} text - Text content to set
   * @returns {void}
   */
  const setText = (element, text) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.textContent = text;
    }
  };

  /**
   * Adds CSS classes to element
   * @param {Element|string} element - DOM element or selector
   * @param {string|Array<string>} classes - Class name(s) to add
   * @returns {void}
   */
  const addClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.add(...classList);
  };

  /**
   * Removes CSS classes from element
   * @param {Element|string} element - DOM element or selector
   * @param {string|Array<string>} classes - Class name(s) to remove
   * @returns {void}
   */
  const removeClass = (element, classes) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    const classList = Array.isArray(classes) ? classes : [classes];
    el.classList.remove(...classList);
  };

  /**
   * Toggles CSS class on element
   * @param {Element|string} element - DOM element or selector
   * @param {string} className - Class name to toggle
   * @returns {void}
   */
  const toggleClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) el.classList.toggle(className);
  };

  /**
   * Checks if element has CSS class
   * @param {Element|string} element - DOM element or selector
   * @param {string} className - Class name to check
   * @returns {boolean} Whether element has class
   */
  const hasClass = (element, className) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.classList.contains(className) : false;
  };

  /**
   * Sets attributes on element
   * @param {Element|string} element - DOM element or selector
   * @param {string|Object} attr - Attribute name or object with attributes
   * @param {string} value - Attribute value (when attr is string)
   * @returns {void}
   */
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

  /**
   * Gets attribute value from element
   * @param {Element|string} element - DOM element or selector
   * @param {string} attr - Attribute name
   * @returns {string|null} Attribute value or null
   */
  const getAttribute = (element, attr) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? el.getAttribute(attr) : null;
  };

  /**
   * Sets inline styles on element
   * @param {Element|string} element - DOM element or selector
   * @param {string|Object} prop - Style property name or object with styles
   * @param {string} value - Style value (when prop is string)
   * @returns {void}
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
   * Gets computed style of element
   * @param {Element|string} element - DOM element or selector
   * @param {string} prop - CSS property name
   * @returns {string} Computed style value
   */
  const getStyle = (element, prop) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    return el ? window.getComputedStyle(el).getPropertyValue(prop) : '';
  };

  /**
   * Shows element by removing display:none
   * @param {Element|string} element - DOM element or selector
   * @returns {void}
   */
  const show = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = '';
      el.setAttribute('aria-hidden', 'false');
    }
  };

  /**
   * Hides element by setting display:none
   * @param {Element|string} element - DOM element or selector
   * @returns {void}
   */
  const hide = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    }
  };

  /**
   * Toggles element visibility
   * @param {Element|string} element - DOM element or selector
   * @returns {void}
   */
  const toggleVisibility = (element) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (el) {
      el.style.display === 'none' ? show(el) : hide(el);
    }
  };

  /**
   * Adds event listener to element
   * @param {Element|string} element - DOM element or selector
   * @param {string} event - Event name
   * @param {Function} handler - Event handler function
   * @param {Object} options - Event listener options
   * @returns {void}
   */
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

  /**
   * Removes event listener from element
   * @param {Element|string} element - DOM element or selector
   * @param {string} event - Event name
   * @param {Function} handler - Event handler function
   * @returns {void}
   */
  const off = (element, event, handler) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;

    el.removeEventListener(event, handler);
    const key = el.id || el._listenerKey;
    if (key) eventListeners.delete(`${key}:${event}`);
    
    log(`Event listener removed: ${event}`);
  };

  /**
   * Adds one-time event listener to element
   * @param {Element|string} element - DOM element or selector
   * @param {string} event - Event name
   * @param {Function} handler - Event handler function
   * @returns {void}
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
   * Triggers custom event on element
   * @param {Element|string} element - DOM element or selector
   * @param {string} eventName - Custom event name
   * @param {Object} detail - Event detail data
   * @returns {void}
   */
  const trigger = (element, eventName, detail = {}) => {
    const el = typeof element === 'string' ? querySelector(element) : element;
    if (!el) return;
    el.dispatchEvent(new CustomEvent(eventName, { detail, bubbles: true }));
  };

  /**
   * Extracts form data as object
   * @param {HTMLFormElement|string} form - Form element or selector
   * @returns {Object} Form data as key-value object
   */
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

  /**
   * Populates form with data
   * @param {HTMLFormElement|string} form - Form element or selector
   * @param {Object} data - Data to populate
   * @returns {void}
   */
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

  /**
   * Resets form to initial state
   * @param {HTMLFormElement|string} form - Form element or selector
   * @returns {void}
   */
  const resetForm = (form) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (formEl && typeof formEl.reset === 'function') formEl.reset();
  };

  /**
   * Disables or enables all form inputs
   * @param {HTMLFormElement|string} form - Form element or selector
   * @param {boolean} disabled - Whether to disable (default: true)
   * @returns {void}
   */
  const disableForm = (form, disabled = true) => {
    const formEl = typeof form === 'string' ? querySelector(form) : form;
    if (!formEl) return;
    const inputs = formEl.querySelectorAll('input, textarea, select, button');
    inputs.forEach((input) => { input.disabled = disabled; });
  };

  /**
   * Makes HTTP request with standardized response format
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @param {string} options.method - HTTP method (default: GET)
   * @param {Object} options.headers - Request headers
   * @param {*} options.body - Request body
   * @param {number} options.timeout - Request timeout in ms (default: CONFIG.HTTP_TIMEOUT)
   * @param {string} options.cache - Cache strategy
   * @returns {Promise<ApiResponse>} Standardized response object
   */
  const request = async (url, options = {}) => {
    if (!url || typeof url !== 'string') {
      logError('Invalid URL provided to request');
      return createErrorResponse(0, 'Invalid URL');
    }

    const { method = 'GET', headers = {}, body = null, timeout = CONFIG.HTTP_TIMEOUT, cache = 'default' } = options;
    const controller = new AbortController();
    const requestId = Math.random().toString(36).substr(2, 9);
    let timeoutId = null;

    try {
      timeoutId = setTimeout(() => controller.abort(), timeout);
      httpRequests.set(requestId, controller);

      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', ...headers },
        body: body ? JSON.stringify(body) : null,
        signal: controller.signal,
        cache,
      });

      clearTimeout(timeoutId);
      timeoutId = null;
      httpRequests.delete(requestId);

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
      return createSuccessResponse(response.status, data);
    } catch (error) {
      if (timeoutId) clearTimeout(timeoutId);
      httpRequests.delete(requestId);
      
      if (error.name === 'AbortError') {
        logError(`Request timeout: ${url}`);
        return createErrorResponse(408, 'Request timeout');
      }
      logError(`Request failed: ${method} ${url}`, error);
      return createErrorResponse(0, error.message);
    }
  };

  /**
   * Cancels in-flight HTTP request by ID
   * @param {string} requestId - Request ID to cancel
   * @returns {boolean} Whether request was cancelled
   */
  const cancelRequest = (requestId) => {
    const controller = httpRequests.get(requestId);
    if (controller) {
      controller.abort();
      httpRequests.delete(requestId);
      log(`Request cancelled: ${requestId}`);
      return true;
    }
    return false;
  };

  /**
   * Makes GET request
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @returns {Promise<ApiResponse>} Response object
   */
  const get = (url, options = {}) => request(url, { ...options, method: 'GET' });

  /**
   * Makes POST request
   * @param {string} url - Request URL
   * @param {*} body - Request body
   * @param {Object} options - Request options
   * @returns {Promise<ApiResponse>} Response object
   */
  const post = (url, body = {}, options = {}) => request(url, { ...options, method: 'POST', body });

  /**
   * Makes PUT request
   * @param {string} url - Request URL
   * @param {*} body - Request body
   * @param {Object} options - Request options
   * @returns {Promise<ApiResponse>} Response object
   */
  const put = (url, body = {}, options = {}) => request(url, { ...options, method: 'PUT', body });

  /**
   * Makes DELETE request
   * @param {string} url - Request URL
   * @param {Object} options - Request options
   * @returns {Promise<ApiResponse>} Response object
   */
  const deleteRequest = (url, options = {}) => request(url, { ...options, method: 'DELETE' });

  /**
   * Sets value in localStorage
   * @param {string} key - Storage key
   * @param {*} value - Value to store
   * @returns {void}
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
   * Gets value from localStorage
   * @param {string} key - Storage key
   * @param {*} defaultValue - Default value if key not found
   * @returns {*} Stored value or default value
   */
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

  /**
   * Removes value from localStorage
   * @param {string} key - Storage key
   * @returns {void}
   */
  const removeStorage = (key) => {
    try {
      localStorage.removeItem(`${STORAGE_PREFIX}${key}`);
      log(`Storage removed: ${key}`);
    } catch (e) {
      logError(`Storage remove failed for key: ${key}`, e);
    }
  };

  /**
   * Clears all app-prefixed values from localStorage
   * @returns {void}
   */
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

  /**
   * Gets stored user object
   * @returns {Object|null} User object or null
   */
  const getUser = () => getStorage('user', null);

  /**
   * Sets stored user object
   * @param {Object} user - User object to store
   * @returns {void}
   */
  const setUser = (user) => setStorage('user', user);

  /**
   * Clears all session data
   * @returns {void}
   */
  const clearSession = () => clearStorage();

  /**
   * Checks if user is authenticated
   * @returns {boolean} Whether user is authenticated
   */
  const isAuthenticated = () => getUser() !== null;

  /**
   * Formats number as Indonesian Rupiah currency
   * @param {number} amount - Amount to format
   * @returns {string} Formatted currency string
   */
  const formatCurrency = (amount) => {
    if (typeof amount !== 'number') return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
  };

  /**
   * Formats date with specified format
   * @param {string|Date} date - Date to format
   * @param {string} format - Format string (DD, MM, YYYY, HH, mm, ss)
   * @returns {string} Formatted date string
   */
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

  /**
   * Creates debounced function that delays execution
   * @param {Function} func - Function to debounce
   * @param {number} delay - Delay in milliseconds (default: 300)
   * @returns {Function} Debounced function
   */
  const debounce = (func, delay = 300) => {
    let timeoutId;
    return function (...args) {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
  };

  /**
   * Creates throttled function that limits execution frequency
   * @param {Function} func - Function to throttle
   * @param {number} limit - Time limit in milliseconds (default: 300)
   * @returns {Function} Throttled function
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
   * Creates deep copy of object
   * @param {Object} obj - Object to copy
   * @returns {Object} Deep copied object
   */
  const deepCopy = (obj) => {
    try {
      return JSON.parse(JSON.stringify(obj));
    } catch (e) {
      logError('Failed to deep copy object', e);
      return obj;
    }
  };

  /**
   * Creates promise that resolves after specified time
   * @param {number} ms - Milliseconds to wait
   * @returns {Promise<void>} Promise that resolves after delay
   */
  const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

  /**
   * Initializes app and sets up global event handlers
   * @returns {void}
   */
  const init = () => {
    log('App initialized');
    window.addEventListener('error', (event) => { logError('Global error', event.error); });
    window.addEventListener('unhandledrejection', (event) => { logError('Unhandled promise rejection', event.reason); });
    document.addEventListener('visibilitychange', () => { log(document.hidden ? 'Page hidden' : 'Page visible'); });
  };

  /**
   * Cleans up all app resources and event listeners
   * @returns {void}
   */
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

  /**
   * Updates configuration values
   * @param {Object} updates - Configuration updates
   * @returns {Object} Updated configuration
   */
  const updateConfig = (updates) => {
    if (typeof updates !== 'object') {
      logError('Invalid configuration updates');
      return CONFIG;
    }
    Object.assign(CONFIG, updates);
    log('Configuration updated', CONFIG);
    return CONFIG;
  };

  return {
    // Configuration
    CONFIG, updateConfig,
    
    // Constants
    API_BASE_URL, STORAGE_PREFIX, HTTP_STATUS,
    
    // DOM selection
    querySelector, querySelectorAll, getElementById,
    
    // DOM content
    setHTML, getText, setText,
    
    // DOM classes
    addClass, removeClass, toggleClass, hasClass,
    
    // DOM attributes
    setAttribute, getAttribute,
    
    // DOM styling
    setStyle, getStyle, show, hide, toggleVisibility,
    
    // Events
    on, off, once, trigger,
    
    // Forms
    getFormData, setFormData, resetForm, disableForm,
    
    // HTTP requests
    request, get, post, put, deleteRequest, cancelRequest,
    
    // Storage
    setStorage, getStorage, removeStorage, clearStorage,
    
    // User & Auth
    getUser, setUser, clearSession, isAuthenticated,
    
    // Formatting
    formatCurrency, formatDate,
    
    // Utilities
    debounce, throttle, deepCopy, wait,
    
    // Lifecycle
    init, cleanup, log, logError,
  };
})();

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => App.init());
} else {
  App.init();
}

window.addEventListener('beforeunload', () => App.cleanup());
