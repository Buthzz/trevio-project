<?php

/**
 * WhatsApp API Configuration
 * Settings for WhatsApp notification integration
 */

return [
    // Enable/Disable WhatsApp notifications
    'enabled' => filter_var(getenv('WHATSAPP_ENABLED'), FILTER_VALIDATE_BOOLEAN, ['options' => ['default' => false]]),
    
    // WhatsApp Business API provider
    'provider' => getenv('WHATSAPP_PROVIDER') ?: 'fonnte', // fonnte, twillio, wati, etc
    
    // API Credentials
    'api_key' => getenv('WHATSAPP_API_KEY') ?: '',
    'api_url' => getenv('WHATSAPP_API_URL') ?: 'https://api.fonnte.com/send',
    
    // Sender phone number (with country code)
    'sender_number' => getenv('WHATSAPP_SENDER_NUMBER') ?: '',
    
    // Message templates
    'templates' => [
        'booking_confirmed' => "🎉 *Booking Confirmed!*\n\nHi {customer_name},\n\nBooking kamu telah dikonfirmasi!\n\n📋 Booking Code: *{booking_code}*\n🏨 Hotel: {hotel_name}\n📅 Check-in: {check_in_date}\n📅 Check-out: {check_out_date}\n💰 Total: {total_price}\n\nDetail: {detail_url}\n\n_Terima kasih telah memilih Trevio!_",
        
        'payment_verified' => "✅ *Payment Verified*\n\nHi {customer_name},\n\nPembayaran kamu telah diverifikasi!\n\n📋 Booking: *{booking_code}*\n🏨 Hotel: {hotel_name}\n💰 Amount: {amount}\n\nBooking kamu sekarang *confirmed*.\n\nDetail: {detail_url}",
        
        'payment_rejected' => "❌ *Payment Rejected*\n\nHi {customer_name},\n\nMaaf, bukti pembayaran kamu ditolak.\n\n📋 Booking: *{booking_code}*\n❓ Alasan: {reason}\n\nSilakan upload ulang bukti pembayaran yang valid.\n\nDetail: {detail_url}",
        
        'refund_approved' => "💰 *Refund Approved*\n\nHi {customer_name},\n\nRefund kamu telah disetujui!\n\n📋 Booking: *{booking_code}*\n💰 Amount: {refund_amount}\n\nDana akan ditransfer ke rekening kamu dalam 1-3 hari kerja.\n\n_Terima kasih atas pengertiannya._",
        
        'refund_completed' => "✅ *Refund Completed*\n\nHi {customer_name},\n\nRefund telah ditransfer!\n\n📋 Booking: *{booking_code}*\n💰 Amount: {refund_amount}\n🏦 Bank: {bank_name}\n\nSilakan cek rekening kamu.\n\n_Terima kasih telah menggunakan Trevio!_",
        
        'checkin_reminder' => "📅 *Check-in Reminder*\n\nHi {customer_name},\n\nCheck-in besok!\n\n🏨 Hotel: {hotel_name}\n📍 Address: {hotel_address}\n📞 Phone: {hotel_phone}\n📅 Check-in: {check_in_date}\n\n_Have a great stay!_ 🌟",
        
        'checkout_reminder' => "👋 *Check-out Reminder*\n\nHi {customer_name},\n\nCheck-out hari ini!\n\n📅 Check-out time: 12:00 PM\n\nJangan lupa review pengalaman kamu! ⭐\n\n{review_url}\n\n_Sampai jumpa lagi!_",
        
        'review_request' => "⭐ *Review Your Stay*\n\nHi {customer_name},\n\nBagaimana pengalaman menginap kamu di {hotel_name}?\n\nBerikan review kamu:\n{review_url}\n\n_Your feedback matters!_ 🙏"
    ],
    
    // Retry settings
    'retry_attempts' => 3,
    'retry_delay' => 5, // seconds
    
    // Timeout (seconds)
    'timeout' => 30,
    
    // Logging
    'log_messages' => filter_var(getenv('WHATSAPP_LOG_MESSAGES'), FILTER_VALIDATE_BOOLEAN, ['options' => ['default' => true]]),
    
    // Rate limiting (messages per minute)
    'rate_limit' => 30,
];
