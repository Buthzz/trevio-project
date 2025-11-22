/**
 * ===================================================================
 * TREVIO - Google-Auth.js
 * Google OAuth 2.0 Authentication integration
 * ===================================================================
 * Fitur:
 * - Google Sign-In initialization
 * - Token management
 * - User profile handling
 * - Logout functionality
 * - Token refresh
 * - Session management
 * ===================================================================
 */

'use strict';

/**
 * GoogleAuth Global Object
 * Menyediakan Google OAuth 2.0 authentication utilities
 */
const GoogleAuth = (function () {
  // ================================================================
  // PRIVATE CONSTANTS
  // ================================================================

  const GOOGLE_OAUTH_CONFIG = {
    // Ganti dengan Google OAuth Client ID Anda
    CLIENT_ID:
      window.GOOGLE_CLIENT_ID ||
      process.env.GOOGLE_CLIENT_ID ||
      '',
    SCOPE: ['profile', 'email'],
    DISCOVERY_DOCS: [
      'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest',
      'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest',
    ],
  };

  const STORAGE_KEYS = {
    USER: 'google_user',
    TOKEN: 'google_token',
    REFRESH_TOKEN: 'google_refresh_token',
    EXPIRES_AT: 'google_token_expires_at',
  };

  const TOKEN_REFRESH_BUFFER = 5 * 60 * 1000; // 5 minutes before expiry

  // ================================================================
  // PRIVATE VARIABLES
  // ================================================================

  let gapi = window.gapi;
  let gisLoaded = false;
  let googleAuth = null;
  let currentUser = null;
  let tokenRefreshTimer = null;
  let eventListeners = new Map();

  // ================================================================
  // UTILITY FUNCTIONS
  // ================================================================

  /**
   * Safe console log
   */
  const log = (message, data = null) => {
    if (window.DEBUG) {
      console.log(`[GOOGLE-AUTH] ${message}`, data || '');
    }
  };

  /**
   * Log error
   */
  const logError = (message, error = null) => {
    console.error(`[GOOGLE-AUTH ERROR] ${message}`, error || '');
  };

  /**
   * Store token di storage
   */
  const storeToken = (token, expiresIn = 3600) => {
    if (!token) return;

    const expiresAt = new Date().getTime() + expiresIn * 1000;

    try {
      localStorage.setItem(
        `trevio_${STORAGE_KEYS.TOKEN}`,
        token
      );
      localStorage.setItem(
        `trevio_${STORAGE_KEYS.EXPIRES_AT}`,
        expiresAt.toString()
      );
      log('Token stored');
    } catch (e) {
      logError('Failed to store token', e);
    }
  };

  /**
   * Get stored token
   */
  const getStoredToken = () => {
    try {
      return localStorage.getItem(`trevio_${STORAGE_KEYS.TOKEN}`);
    } catch (e) {
      return null;
    }
  };

  /**
   * Check apakah token masih valid
   */
  const isTokenValid = () => {
    try {
      const expiresAt = localStorage.getItem(
        `trevio_${STORAGE_KEYS.EXPIRES_AT}`
      );
      if (!expiresAt) return false;

      const now = new Date().getTime();
      return now < parseInt(expiresAt);
    } catch (e) {
      return false;
    }
  };

  /**
   * Store user data
   */
  const storeUser = (user) => {
    if (!user) return;

    try {
      localStorage.setItem(
        `trevio_${STORAGE_KEYS.USER}`,
        JSON.stringify(user)
      );
      log('User stored', user);
    } catch (e) {
      logError('Failed to store user', e);
    }
  };

  /**
   * Get stored user
   */
  const getStoredUser = () => {
    try {
      const user = localStorage.getItem(`trevio_${STORAGE_KEYS.USER}`);
      return user ? JSON.parse(user) : null;
    } catch (e) {
      return null;
    }
  };

  /**
   * Remove stored token and user
   */
  const clearStoredAuth = () => {
    try {
      localStorage.removeItem(`trevio_${STORAGE_KEYS.USER}`);
      localStorage.removeItem(`trevio_${STORAGE_KEYS.TOKEN}`);
      localStorage.removeItem(`trevio_${STORAGE_KEYS.REFRESH_TOKEN}`);
      localStorage.removeItem(`trevio_${STORAGE_KEYS.EXPIRES_AT}`);
      log('Auth data cleared');
    } catch (e) {
      logError('Failed to clear auth data', e);
    }
  };

  /**
   * Parse JWT token
   */
  const parseJwt = (token) => {
    try {
      const base64Url = token.split('.')[1];
      const base64 = base64Url
        .replace(/-/g, '+')
        .replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
          .join('')
      );
      return JSON.parse(jsonPayload);
    } catch (e) {
      logError('Failed to parse JWT', e);
      return null;
    }
  };

  /**
   * Setup auto token refresh
   */
  const setupTokenRefresh = (expiresIn) => {
    if (tokenRefreshTimer) {
      clearTimeout(tokenRefreshTimer);
    }

    const refreshTime = expiresIn * 1000 - TOKEN_REFRESH_BUFFER;

    tokenRefreshTimer = setTimeout(() => {
      log('Auto-refreshing token');
      refreshToken();
    }, refreshTime);

    log(`Token refresh scheduled in ${refreshTime / 1000} seconds`);
  };

  /**
   * Trigger custom event
   */
  const triggerEvent = (eventName, detail = {}) => {
    if (!eventListeners.has(eventName)) return;

    eventListeners.get(eventName).forEach((callback) => {
      try {
        callback(detail);
      } catch (e) {
        logError(`Event callback error for ${eventName}`, e);
      }
    });
  };

  // ================================================================
  // GOOGLE API LOADING
  // ================================================================

  /**
   * Load Google Sign-In library
   * @returns {Promise}
   */
  const loadGoogleSignInLibrary = () => {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src =
        'https://accounts.google.com/gsi/client';
      script.async = true;
      script.defer = true;

      script.onload = () => {
        gisLoaded = true;
        log('Google Sign-In library loaded');
        resolve();
      };

      script.onerror = () => {
        logError('Failed to load Google Sign-In library');
        reject(new Error('Failed to load Google Sign-In'));
      };

      document.head.appendChild(script);
    });
  };

  /**
   * Load Google API Gapi library
   * @returns {Promise}
   */
  const loadGapiLibrary = () => {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = 'https://apis.google.com/js/api.js';
      script.async = true;
      script.defer = true;

      script.onload = () => {
        gapi = window.gapi;
        
        if (!gapi) {
          logError('Google API Gapi not found in window');
          reject(new Error('Google API Gapi not available'));
          return;
        }

        try {
          gapi.load('client:auth2', async () => {
            try {
              await gapi.client.init({
                clientId: GOOGLE_OAUTH_CONFIG.CLIENT_ID,
                scope: GOOGLE_OAUTH_CONFIG.SCOPE.join(' '),
                discoveryDocs: GOOGLE_OAUTH_CONFIG.DISCOVERY_DOCS,
              });

              googleAuth = gapi.auth2.getAuthInstance();
              log('Google API Gapi library loaded');
              resolve();
            } catch (e) {
              logError('Failed to initialize Gapi', e);
              reject(e);
            }
          });
        } catch (e) {
          logError('Failed to load Gapi module', e);
          reject(e);
        }
      };

      script.onerror = () => {
        logError('Failed to load Google API Gapi library');
        reject(new Error('Failed to load Gapi'));
      };

      document.head.appendChild(script);
    });
  };

  // ================================================================
  // AUTHENTICATION
  // ================================================================

  /**
   * Initialize Google Authentication
   * @param {Object} options - Initialization options
   * @returns {Promise}
   */
  const init = async (options = {}) => {
    try {
      log('Initializing Google Auth');

      // Update config dengan options
      if (options.clientId) {
        GOOGLE_OAUTH_CONFIG.CLIENT_ID = options.clientId;
      }

      // Validasi client ID
      if (!GOOGLE_OAUTH_CONFIG.CLIENT_ID) {
        throw new Error('Google Client ID not configured');
      }

      // Load libraries
      await loadGoogleSignInLibrary();

      // Try to restore session
      const storedUser = getStoredUser();
      if (storedUser && isTokenValid()) {
        currentUser = storedUser;
        log('Session restored from storage');
        triggerEvent('onSessionRestored', { user: currentUser });
      }

      log('Google Auth initialized');
      triggerEvent('onInitialized');

      return true;
    } catch (e) {
      logError('Initialization failed', e);
      triggerEvent('onInitError', { error: e.message });
      throw e;
    }
  };

  /**
   * Render Google Sign-In button
   * @param {string} containerId - Container element ID
   * @param {Object} options - Button options
   */
  const renderSignInButton = (containerId, options = {}) => {
    if (!gisLoaded) {
      logError('Google Sign-In library not loaded');
      return;
    }

    if (!window.google || !window.google.accounts) {
      logError('Google accounts not available');
      return;
    }

    const container = document.getElementById(containerId);
    if (!container) {
      logError(`Container element not found: ${containerId}`);
      return;
    }

    try {
      window.google.accounts.id.initialize({
        client_id: GOOGLE_OAUTH_CONFIG.CLIENT_ID,
        callback: handleSignInResponse,
        ...options,
      });

      window.google.accounts.id.renderButton(container, {
        type: options.type || 'standard',
        theme: options.theme || 'outline',
        size: options.size || 'large',
        text: options.text || 'signin_with',
        shape: options.shape || 'rectangular',
        logo_alignment: options.logoAlignment || 'left',
        width: options.width || '100%',
      });

      log('Sign-In button rendered');
    } catch (e) {
      logError('Failed to render sign-in button', e);
    }
  };

  /**
   * Handle Google Sign-In response
   */
  const handleSignInResponse = async (response) => {
    try {
      if (!response.credential) {
        throw new Error('No credential received');
      }

      log('Sign-In response received');

      // Parse JWT token
      const payload = parseJwt(response.credential);
      if (!payload) {
        throw new Error('Failed to parse token');
      }

      // Create user object
      const user = {
        id: payload.sub,
        email: payload.email,
        name: payload.name,
        picture: payload.picture,
        aud: payload.aud,
        iss: payload.iss,
        emailVerified: payload.email_verified,
      };

      // Store user and token
      currentUser = user;
      storeUser(user);
      storeToken(response.credential, 3600);

      log('User authenticated', user);
      triggerEvent('onSignIn', { user });

      return user;
    } catch (e) {
      logError('Sign-in response handling failed', e);
      triggerEvent('onSignInError', { error: e.message });
      throw e;
    }
  };

  /**
   * Sign in dengan Google
   * @returns {Promise}
   */
  const signIn = async () => {
    try {
      log('Initiating sign-in');

      if (!window.google || !window.google.accounts) {
        throw new Error('Google accounts not available');
      }

      return new Promise((resolve, reject) => {
        window.google.accounts.id.renderButton(
          document.createElement('div'),
          {
            click_listener: async (response) => {
              try {
                const user = await handleSignInResponse(response);
                resolve(user);
              } catch (e) {
                reject(e);
              }
            },
          }
        );
      });
    } catch (e) {
      logError('Sign-in failed', e);
      throw e;
    }
  };

  /**
   * Sign out
   * @returns {Promise}
   */
  const signOut = async () => {
    try {
      log('Signing out');

      if (
        window.google &&
        window.google.accounts
      ) {
        window.google.accounts.id.disableAutoSelect();
      }

      // Clear local data
      clearStoredAuth();
      currentUser = null;

      // Clear token refresh
      if (tokenRefreshTimer) {
        clearTimeout(tokenRefreshTimer);
      }

      log('User signed out');
      triggerEvent('onSignOut');

      return true;
    } catch (e) {
      logError('Sign-out failed', e);
      throw e;
    }
  };

  /**
   * Refresh access token
   * @returns {Promise}
   */
  const refreshToken = async () => {
    try {
      log('Refreshing token');

      if (!googleAuth) {
        await loadGapiLibrary();
      }

      if (!googleAuth) {
        throw new Error('Google Auth not initialized');
      }

      const userAuth = googleAuth.currentUser.get();
      if (!userAuth.isSignedIn()) {
        throw new Error('User not signed in');
      }

      const authResponse = userAuth.getAuthResponse(true);
      storeToken(
        authResponse.id_token,
        authResponse.expires_in
      );
      setupTokenRefresh(authResponse.expires_in);

      log('Token refreshed');
      triggerEvent('onTokenRefreshed');

      return authResponse.id_token;
    } catch (e) {
      logError('Token refresh failed', e);
      triggerEvent('onTokenRefreshError', { error: e.message });
      throw e;
    }
  };

  // ================================================================
  // USER INFORMATION
  // ================================================================

  /**
   * Get current user
   * @returns {Object|null}
   */
  const getCurrentUser = () => {
    return currentUser || getStoredUser();
  };

  /**
   * Get current token
   * @returns {string|null}
   */
  const getToken = () => {
    return getStoredToken();
  };

  /**
   * Check apakah user signed in
   * @returns {boolean}
   */
  const isSignedIn = () => {
    return currentUser !== null || getStoredUser() !== null;
  };

  /**
   * Get user profile photo URL
   * @returns {string|null}
   */
  const getUserPhotoUrl = () => {
    const user = getCurrentUser();
    return user ? user.picture : null;
  };

  /**
   * Get user email
   * @returns {string|null}
   */
  const getUserEmail = () => {
    const user = getCurrentUser();
    return user ? user.email : null;
  };

  /**
   * Get user name
   * @returns {string|null}
   */
  const getUserName = () => {
    const user = getCurrentUser();
    return user ? user.name : null;
  };

  // ================================================================
  // EVENT MANAGEMENT
  // ================================================================

  /**
   * Register event listener
   * @param {string} eventName - Event name
   * @param {Function} callback - Callback function
   */
  const on = (eventName, callback) => {
    if (!eventListeners.has(eventName)) {
      eventListeners.set(eventName, []);
    }

    eventListeners.get(eventName).push(callback);
    log(`Event listener registered: ${eventName}`);
  };

  /**
   * Unregister event listener
   * @param {string} eventName - Event name
   * @param {Function} callback - Callback function
   */
  const off = (eventName, callback) => {
    if (!eventListeners.has(eventName)) return;

    const listeners = eventListeners.get(eventName);
    const index = listeners.indexOf(callback);

    if (index > -1) {
      listeners.splice(index, 1);
      log(`Event listener removed: ${eventName}`);
    }
  };

  /**
   * Register one-time event listener
   * @param {string} eventName - Event name
   * @param {Function} callback - Callback function
   */
  const once = (eventName, callback) => {
    const wrappedCallback = (detail) => {
      callback(detail);
      off(eventName, wrappedCallback);
    };

    on(eventName, wrappedCallback);
  };

  // ================================================================
  // UTILITY
  // ================================================================

  /**
   * Check configuration
   * @returns {Object}
   */
  const getConfig = () => {
    return {
      clientId: GOOGLE_OAUTH_CONFIG.CLIENT_ID,
      gisLoaded: gisLoaded,
      currentUser: currentUser,
      isSignedIn: isSignedIn(),
    };
  };

  /**
   * Cleanup resources
   */
  const cleanup = () => {
    if (tokenRefreshTimer) {
      clearTimeout(tokenRefreshTimer);
    }
    eventListeners.clear();
    log('Google Auth cleaned up');
  };

  // ================================================================
  // PUBLIC API
  // ================================================================

  return {
    // Initialization
    init,
    renderSignInButton,

    // Authentication
    signIn,
    signOut,
    refreshToken,

    // User info
    getCurrentUser,
    getToken,
    isSignedIn,
    getUserPhotoUrl,
    getUserEmail,
    getUserName,

    // Events
    on,
    off,
    once,

    // Utility
    getConfig,
    cleanup,
  };
})();

// Initialize on document ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    GoogleAuth.init();
  });
} else {
  GoogleAuth.init().catch((e) => {
    console.error('Failed to initialize Google Auth:', e);
  });
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => GoogleAuth.cleanup());

// Log when module is loaded
console.log('[GOOGLE-AUTH] Module loaded successfully');
