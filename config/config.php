<?php
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

define('BASE_URL', '/ecommerce');

define('STRIPE_SECRET_KEY', 'sk_test_xxx');

if (!isset($_SESSION['invite_id']) && !isset($_SESSION['user'])) {
    $_SESSION['invite_id'] = bin2hex(random_bytes(16));
}