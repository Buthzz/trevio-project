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

  const log = (message) => {
    if (window.DEBUG) console.log(`[GOOGLE-AUTH] ${message}`);
  };

  const logError = (message, error = null) => {
    if (window.DEBUG) console.error(`[GOOGLE-AUTH ERROR] ${message}`, error || '');
  };

  const emit = (event, data) => {
    if (listeners.has(event)) {
      const eventListeners = listeners.get(event);
      eventListeners.forEach((callback) => {
        try { callback(data); } catch (e) { logError(`Error in listener for ${event}`, e); }
      });
    }
  };

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

  const storeToken = (token) => {
    if (!token || typeof token !== 'string') { logError('Invalid token provided'); return false; }
    try {
      localStorage.setItem(STORAGE_KEYS.token, token);
      return true;
    } catch (e) {
      logError('Failed to store token', e);
      return false;
    }
  };

  const getToken = () => {
    try { return localStorage.getItem(STORAGE_KEYS.token); } catch (e) { logError('Failed to get token', e); return null; }
  };

  const storeUser = (user) => {
    if (!user || typeof user !== 'object') { logError('Invalid user object'); return false; }
    try {
      localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(user));
      return true;
    } catch (e) {
      logError('Failed to store user', e);
      return false;
    }
  };

  const getUser = () => {
    try {
      const user = localStorage.getItem(STORAGE_KEYS.user);
      return user ? JSON.parse(user) : null;
    } catch (e) {
      logError('Failed to get user', e);
      return null;
    }
  };

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

  const clearSession = () => {
    try {
      localStorage.removeItem(STORAGE_KEYS.token);
      localStorage.removeItem(STORAGE_KEYS.user);
      localStorage.removeItem(STORAGE_KEYS.session);
    } catch (e) {
      logError('Failed to clear session', e);
    }
  };

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

  const handleSignInResponse = (response) => {
    if (!response || typeof response !== 'object') { logError('Invalid response object'); return; }
    if (!response.credential) { logError('No credential in response'); return; }
    
    try {
      const token = response.credential;
      const user = parseJwt(token);
      
      if (!user) { logError('Failed to parse user from token'); return; }
      
      storeToken(token);
      storeUser(user);
      log('User signed in successfully');
      emit('signin', user);
    } catch (e) {
      logError('Error handling sign-in response', e);
    }
  };

  const refreshToken = () => {
    const token = getToken();
    if (!token) { logError('No token available to refresh'); return Promise.reject('No token'); }
    
    if (isTokenValid(token)) {
      log('Token still valid, skipping refresh');
      return Promise.resolve(token);
    }
    
    try {
      return gapi.auth2.getAuthInstance().signIn().then(
        (googleUser) => {
          const authResponse = googleUser.getAuthResponse();
          if (!authResponse) { logError('No auth response from refresh'); return Promise.reject('No auth response'); }
          
          const newToken = authResponse.id_token;
          if (!newToken) { logError('No new token in auth response'); return Promise.reject('No token'); }
          
          storeToken(newToken);
          log('Token refreshed successfully');
          return newToken;
        },
        (error) => {
          logError('Token refresh failed', error);
          return Promise.reject(error);
        }
      );
    } catch (e) {
      logError('Error refreshing token', e);
      return Promise.reject(e);
    }
  };

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
              resolve();
            } else {
              logError('Google API not available');
              resolve();
            }
          } catch (e) {
            logError('Error initializing Google Auth', e);
            resolve();
          }
        })();
      }),
      new Promise((resolve) => setTimeout(() => { logError('Google Auth initialization timeout'); resolve(); }, 5000))
    ]);
  };

  const renderSignInButton = (container, options = {}) => {
    if (!gisInitialized) { logError('Google Auth not initialized'); return; }
    
    try {
      const containerEl = typeof container === 'string' ? document.querySelector(container) : container;
      if (!containerEl) { logError('Container not found'); return; }
      
      window.google.accounts.id.renderButton(containerEl, {
        type: options.type || 'standard',
        theme: options.theme || 'outline',
        size: options.size || 'large',
        text: options.text || 'signin_with',
        ...options,
      });
      log('Sign-in button rendered');
    } catch (e) {
      logError('Error rendering sign-in button', e);
    }
  };

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
    } catch (e) {
      logError('Error signing out', e);
    }
  };

  const on = (event, callback) => {
    if (!event || typeof event !== 'string' || typeof callback !== 'function') { logError('Invalid event or callback'); return; }
    if (!listeners.has(event)) listeners.set(event, []);
    listeners.get(event).push(callback);
    log(`Listener added for event: ${event}`);
  };

  const off = (event, callback) => {
    if (!event || typeof event !== 'string') { logError('Invalid event'); return; }
    if (!listeners.has(event)) return;
    const callbacks = listeners.get(event);
    const index = callbacks.indexOf(callback);
    if (index > -1) callbacks.splice(index, 1);
    log(`Listener removed for event: ${event}`);
  };

  const once = (event, callback) => {
    if (!event || typeof event !== 'string' || typeof callback !== 'function') { logError('Invalid event or callback'); return; }
    const wrapper = (data) => {
      callback(data);
      off(event, wrapper);
    };
    on(event, wrapper);
    log(`One-time listener added for event: ${event}`);
  };

  return {
    init,
    getToken,
    getUser,
    isTokenValid,
    refreshToken,
    handleSignInResponse,
    renderSignInButton,
    signOut,
    on,
    off,
    once,
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
