<?php
declare(strict_types=1);

/**
 * Bazaar Email Configuration
 */

// Set this to true to allow email sending.
const MAIL_ENABLED = true;

// Gmail SMTP settings
const MAIL_SMTP_HOST = 'smtp.gmail.com';
const MAIL_SMTP_PORT = 587;

// Your Gmail login details.
// Use your Gmail address and the App Password, NOT your normal Gmail password.
const MAIL_SMTP_USERNAME = 'jpalahang11@gmail.com';
const MAIL_SMTP_PASSWORD = 'nvlazetwmyiovvow';

// The email the user sees as the sender.
const MAIL_FROM_EMAIL = 'jpalahang11@gmail.com';
const MAIL_FROM_NAME = 'Bazaar Support';

// If the user presses reply, it will go here.
const MAIL_REPLY_TO_EMAIL = 'jpalahang11@gmail.com';
const MAIL_REPLY_TO_NAME = 'Bazaar Support';

// Only set this to true when testing with a test file.
// Do not use debug while QueryAdmin.php is redirecting.
const MAIL_DEBUG = false;
