<?php
// trevio-project/config/whatsapp.php

/**
 * Konfigurasi API WhatsApp (Fonnte/Wablas)
 * Digunakan oleh libraries/WhatsApp.php untuk mengirim notifikasi.
 */

define('WHATSAPP_API_KEY', 'PSb4ar7j6d482Bvphgc1'); 

// Status aktifasi fitur WhatsApp (true jika 'WHATSAPP_ENABLED' di .env adalah true)
define('WHATSAPP_ENABLED', (bool) (getenv('WHATSAPP_ENABLED') === 'true'));

define('WHATSAPP_API_URL', getenv('WHATSAPP_API_URL') ?: 'https://api.fonnte.com/send');

define('WHATSAPP_PROVIDER', getenv('WHATSAPP_PROVIDER') ?: 'fonnte');