/**
 * ===================================================================
 * TREVIO - Google Auth.js (IMPROVED)
 * Google OAuth 2.0 authentication management
 * ===================================================================
 * Improvements:
 * - Fixed JWT token parsing with proper validation
 * - Added script loading safety checks to prevent duplicates
 * - Enhanced error handling with timeout protection
 * - Improved token refresh with error recovery
 * - Better type checking and null safety
 * - Secure token and user data management
 * - Standardized error responses
 * - JSDoc documentation for public APIs
 * ===================================================================
 */

'use strict';

const GoogleAuth = (function () {
  const GOOGLE_CLIENT_ID = window.GOOGLE_CLIENT_ID || '';
  const STORAGE_KEYS = { token: 'google_token', user: 'google_user', session: 'google_session' };
  const TOKEN_REFRESH_BUFFER = 5 * 60 * 1000; // Refresh 5 minutes before expiry
  
  let listeners = new Map();
  let gisScriptLoaded = false;
  let gapiScriptLoaded = false;
  let tokenRefreshInterval = null;
  let gisInitialized = false;

  /**
   * @typedef {Object} AuthResponse
   * @property {boolean} success - Operation success status
   * @property {*} data - Response data or null
   * @property {string} error - Error message if failed
   */

  /**
   * Creates standardized success response
   * @param {*} data - Response data
   * @returns {AuthResponse} Success response
   * @private
   */
  const createSuccessResponse = (data) => ({
    success: true,
    data,
  });

  /**
   * Creates standardized error response
   * @param {string} error - Error message
   * @returns {AuthResponse} Error response
   * @private
   */
  const createErrorResponse = (error) => ({
    success: false,
    data: null,
    error,
  });

  /**
   * Logs message to console when DEBUG enabled
   * @param {string} message - Message to log
   * @returns {void}
   */
  const log = (message) => {
    if (window.DEBUG) console.log(`[GOOGLE-AUTH] ${message}`);
  };

  /**
   * Logs error to console
   * @param {string} message - Error message
   * @param {Error} error - Optional error object
   * @returns {void}
   */
  const logError = (message, error = null) => {
    if (window.DEBUG) console.error(`[GOOGLE-AUTH ERROR] ${message}`, error || '');
  };

  /**
   * Emits event to all registered listeners
   * @param {string} event - Event name
   * @param {*} data - Event data
   * @returns {void}
   * @private
   */
  const emit = (event, data) => {
    if (listeners.has(event)) {
      const eventListeners = listeners.get(event);
      eventListeners.forEach((callback) => {
        try { callback(data); } catch (e) { logError(`Error in listener for ${event}`, e); }
      });
    }
  };

  /**
   * Parses JWT token to extract payload
   * @param {string} token - JWT token
   * @returns {Object|null} Parsed JWT payload or null
   * @private
   */
  const parseJwt = (token) => {
    if (!token || typeof token !== 'string') return null;
    try {
      const parts = token.split('.');
      if (parts.length !== 3) return null;
      const base64Url = parts[1];
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map((c) => `%${('00' + c.charCodeAt(0).toString(16)).slice(-2)}`)
          .join('')
      );
      const payload = JSON.parse(jsonPayload);
      if (!payload || typeof payload !== 'object') return null;
      return payload;
    } catch (e) {
      logError('Failed to parse JWT', e);
      return null;
    }
  };

  /**
   * Stores token in localStorage
   * @param {string} token - JWT token
   * @returns {AuthResponse} Storage response
   * @private
   */
  const storeToken = (token) => {
    if (!token || typeof token !== 'string') {
      return createErrorResponse('Invalid token provided');
    }
    try {
      localStorage.setItem(STORAGE_KEYS.token, token);
      return createSuccessResponse(true);
    } catch (e) {
      logError('Failed to store token', e);
      return createErrorResponse('Failed to store token');
    }
  };

  /**
   * Gets token from localStorage
   * @returns {string|null} Stored token or null
   * @private
   */
  const getToken = () => {
    try { return localStorage.getItem(STORAGE_KEYS.token); } catch (e) { logError('Failed to get token', e); return null; }
  };

  /**
   * Stores user object in localStorage
   * @param {Object} user - User object
   * @returns {AuthResponse} Storage response
   * @private
   */
  const storeUser = (user) => {
    if (!user || typeof user !== 'object') {
      return createErrorResponse('Invalid user object');
    }
    try {
      localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));
      return createSuccessResponse(true);
    } catch (e) {
      logError('Failed to store user', e);
      return createErrorResponse('Failed to store user');
    }
  };

  /**
   * Gets user object from localStorage
   * @returns {Object|null} User object or null
   * @private
   */
  const getUser = () => {
    try {
      const user = localStorage.getItem(STORAGE_KEYS.user);
      return user ? JSON.parse(user) : null;
    } catch (e) {
      logError('Failed to get user', e);
      return null;
    }
  };

  /**
   * Checks if JWT token is valid
   * @param {string} token - JWT token
   * @returns {boolean} Whether token is valid
   * @private
   */
  const isTokenValid = (token) => {
    if (!token) return false;
    try {
      const payload = parseJwt(token);
      if (!payload || !payload.exp) return false;
      const expirationTime = parseInt(payload.exp, 10) * 1000;
      return Date.now() < expirationTime - TOKEN_REFRESH_BUFFER;
    } catch (e) {
      logError('Failed to validate token', e);
      return false;
    }
  };

  /**
   * Clears all session data
   * @returns {void}
   * @private
   */
  const clearSession = () => {
    try {
      localStorage.removeItem(STORAGE_KEYS.token);
      localStorage.removeItem(STORAGE_KEYS.user);
      localStorage.removeItem(STORAGE_KEYS.session);
    } catch (e) {
      logError('Failed to clear session', e);
    }
  };

  /**
   * Loads Google Sign-In library
   * @returns {Promise<void>} Promise that resolves when library is loaded
   * @private
   */
  const loadGoogleSignInLibrary = () => {
    return new Promise((resolve) => {
      if (gisScriptLoaded) { log('GIS script already loaded'); resolve(); return; }
      if (document.getElementById('google-signin-script')) { gisScriptLoaded = true; resolve(); return; }
      
      try {
        const script = document.createElement('script');
        script.id = 'google-signin-script';
        script.src = 'https://accounts.google.com/gsi/client';
        script.async = true;
        script.defer = true;
        script.onload = () => { gisScriptLoaded = true; log('GIS library loaded'); resolve(); };
        script.onerror = () => { logError('Failed to load GIS library'); resolve(); };
        document.head.appendChild(script);
      } catch (e) {
        logError('Error loading GIS library', e);
        resolve();
      }
    });
  };

  /**
   * Loads Google API library
   * @returns {Promise<void>} Promise that resolves when library is loaded
   * @private
   */
  const loadGapiLibrary = () => {
    return new Promise((resolve) => {
      if (gapiScriptLoaded) { log('GAPI script already loaded'); resolve(); return; }
      if (document.getElementById('gapi-script')) { gapiScriptLoaded = true; resolve(); return; }
      
      try {
        const script = document.createElement('script');
        script.id = 'gapi-script';
        script.src = 'https://apis.google.com/js/platform.js';
        script.async = true;
        script.defer = true;
        script.onload = () => { gapiScriptLoaded = true; log('GAPI library loaded'); resolve(); };
        script.onerror = () => { logError('Failed to load GAPI library'); resolve(); };
        
        // Use Promise.race for timeout protection
        Promise.race([
          new Promise((r) => { script.onload = () => { r(); }; }),
          new Promise((r) => setTimeout(r, 10000))
        ]).then(() => { gapiScriptLoaded = true; resolve(); });
        
        document.head.appendChild(script);
      } catch (e) {
        logError('Error loading GAPI library', e);
        resolve();
      }
    });
  };

  /**
   * Handles sign-in response from Google
   * @param {Object} response - Google sign-in response
   * @returns {AuthResponse} Sign-in response
   * @private
   */
  const handleSignInResponse = (response) => {
    if (!response || typeof response !== 'object') {
      return createErrorResponse('Invalid response object');
    }
    if (!response.credential) {
      return createErrorResponse('No credential in response');
    }
    
    try {
      const token = response.credential;
      const user = parseJwt(token);
      
      if (!user) {
        return createErrorResponse('Failed to parse user from token');
      }
      
      storeToken(token);
      storeUser(user);
      log('User signed in successfully');
      emit('signin', user);
      return createSuccessResponse(user);
    } catch (e) {
      logError('Error handling sign-in response', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Refreshes authentication token
   * @returns {Promise<AuthResponse>} Token refresh response
   */
  const refreshToken = () => {
    const token = getToken();
    if (!token) {
      logError('No token available to refresh');
      return Promise.resolve(createErrorResponse('No token available'));
    }
    
    if (isTokenValid(token)) {
      log('Token still valid, skipping refresh');
      return Promise.resolve(createSuccessResponse(token));
    }
    
    try {
      return gapi.auth2.getAuthInstance().signIn().then(
        (googleUser) => {
          const authResponse = googleUser.getAuthResponse();
          if (!authResponse) {
            return createErrorResponse('No auth response from refresh');
          }
          
          const newToken = authResponse.id_token;
          if (!newToken) {
            return createErrorResponse('No new token in auth response');
          }
          
          storeToken(newToken);
          log('Token refreshed successfully');
          return createSuccessResponse(newToken);
        },
        (error) => {
          logError('Token refresh failed', error);
          return createErrorResponse(error.message || 'Token refresh failed');
        }
      );
    } catch (e) {
      logError('Error refreshing token', e);
      return Promise.resolve(createErrorResponse(e.message));
    }
  };

  /**
   * Initializes Google Authentication
   * @returns {Promise<AuthResponse>} Initialization response
   */
  const init = () => {
    return Promise.race([
      new Promise((resolve) => {
        (async () => {
          try {
            await loadGoogleSignInLibrary();
            await loadGapiLibrary();
            
            // Initialize Google Sign-In
            if (window.google && window.google.accounts && window.google.accounts.id) {
              window.google.accounts.id.initialize({ client_id: GOOGLE_CLIENT_ID });
              gisInitialized = true;
              log('Google Auth initialized');
              emit('ready', { authenticated: !!getToken() });
              resolve(createSuccessResponse({ initialized: true }));
            } else {
              logError('Google API not available');
              resolve(createErrorResponse('Google API not available'));
            }
          } catch (e) {
            logError('Error initializing Google Auth', e);
            resolve(createErrorResponse(e.message));
          }
        })();
      }),
      new Promise((resolve) => setTimeout(() => { logError('Google Auth initialization timeout'); resolve(createErrorResponse('Initialization timeout')); }, 5000))
    ]);
  };

  /**
   * Renders Google Sign-In button
   * @param {HTMLElement|string} container - Container element or selector
   * @param {Object} options - Button options
   * @returns {AuthResponse} Render response
   */
  const renderSignInButton = (container, options = {}) => {
    if (!gisInitialized) {
      return createErrorResponse('Google Auth not initialized');
    }
    
    try {
      const containerEl = typeof container === 'string' ? document.querySelector(container) : container;
      if (!containerEl) {
        return createErrorResponse('Container not found');
      }
      
      window.google.accounts.id.renderButton(containerEl, {
        type: options.type || 'standard',
        theme: options.theme || 'outline',
        size: options.size || 'large',
        text: options.text || 'signin_with',
        ...options,
      });
      log('Sign-in button rendered');
      return createSuccessResponse(true);
    } catch (e) {
      logError('Error rendering sign-in button', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Signs out current user
   * @returns {AuthResponse} Sign-out response
   */
  const signOut = () => {
    try {
      clearSession();
      if (window.google && window.google.accounts && window.google.accounts.id) {
        window.google.accounts.id.disableAutoSelect();
      }
      if (typeof gapi !== 'undefined' && gapi.auth2) {
        gapi.auth2.getAuthInstance().signOut();
      }
      clearInterval(tokenRefreshInterval);
      log('User signed out');
      emit('signout', null);
      return createSuccessResponse(null);
    } catch (e) {
      logError('Error signing out', e);
      return createErrorResponse(e.message);
    }
  };

  /**
   * Registers event listener
   * @param {string} event - Event name
   * @param {Function} callback - Callback function
   * @returns {AuthResponse} Registration response
   */
  const on = (event, callback) => {
    if (!event || typeof event !== 'string' || typeof callback !== 'function') {
      return createErrorResponse('Invalid event or callback');
    }
    if (!listeners.has(event)) listeners.set(event, []);
    listeners.get(event).push(callback);
    log(`Listener added for event: ${event}`);
    return createSuccessResponse(true);
  };

  /**
   * Removes event listener
   * @param {string} event - Event name
   * @param {Function} callback - Callback function
   * @returns {AuthResponse} Removal response
   */
  const off = (event, callback) => {
    if (!event || typeof event !== 'string') {
      return createErrorResponse('Invalid event');
    }
    if (!listeners.has(event)) return createSuccessResponse(true);
    const callbacks = listeners.get(event);
    const index = callbacks.indexOf(callback);
    if (index > -1) callbacks.splice(index, 1);
    log(`Listener removed for event: ${event}`);
    return createSuccessResponse(true);
  };

  /**
   * Registers one-time event listener
   * @param {string} event - Event name
   * @param {Function} callback - Callback function
   * @returns {AuthResponse} Registration response
   */
  const once = (event, callback) => {
    if (!event || typeof event !== 'string' || typeof callback !== 'function') {
      return createErrorResponse('Invalid event or callback');
    }
    const wrapper = (data) => {
      callback(data);
      off(event, wrapper);
    };
    on(event, wrapper);
    log(`One-time listener added for event: ${event}`);
    return createSuccessResponse(true);
  };

  /**
   * Gets current authentication token
   * @returns {string|null} JWT token or null
   */
  const getTokenPublic = () => getToken();

  /**
   * Gets current user object
   * @returns {Object|null} User object or null
   */
  const getUserPublic = () => getUser();

  /**
   * Checks if current token is valid
   * @returns {boolean} Whether token is valid
   */
  const isTokenValidPublic = () => isTokenValid(getToken());

  /**
   * Handles Google sign-in response (public wrapper)
   * @param {Object} response - Google sign-in response
   * @returns {void}
   */
  const handleSignInResponsePublic = (response) => {
    handleSignInResponse(response);
  };

  return {
    // Initialization
    init,
    
    // Authentication
    getToken: getTokenPublic,
    getUser: getUserPublic,
    isTokenValid: isTokenValidPublic,
    refreshToken,
    handleSignInResponse: handleSignInResponsePublic,
    
    // UI
    renderSignInButton,
    signOut,
    
    // Events
    on, off, once,
  };
})();

// Auto-load on DOM ready
if (typeof window !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      if (window.GOOGLE_CLIENT_ID) {
        GoogleAuth.init().catch((e) => console.error('[GOOGLE-AUTH] Initialization error', e));
      }
    });
  } else {
    if (window.GOOGLE_CLIENT_ID) {
      GoogleAuth.init().catch((e) => console.error('[GOOGLE-AUTH] Initialization error', e));
    }
  }
}
