<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__.'/../');
}

if (! defined('AUTH_KEY')) {
    define('AUTH_KEY', 'test-auth-key');
    define('SECURE_AUTH_KEY', 'test-secure-auth-key');
    define('LOGGED_IN_KEY', 'test-logged-in-key');
    define('NONCE_KEY', 'test-nonce-key');
}

require_once __DIR__.'/../wp-content/plugins/koro-payments/includes/class-koro-payments-crypto.php';
