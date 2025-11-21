<?php
// trevio-project/config/google-oauth.php

/**
 * Konfigurasi Google OAuth 2.0
 * Digunakan oleh AuthController untuk fitur "Sign in with Google"
 */

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'your_google_client_id_here');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'your_google_client_secret_here');

define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: BASE_URL . '/auth/google-callback');

define('GOOGLE_OAUTH_SCOPE', 'email profile');

// URL Google API
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v1/userinfo');