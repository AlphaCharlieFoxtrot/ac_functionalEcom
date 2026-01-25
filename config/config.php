<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/ecommerce');

// Chargement config locale (NON versionnée)
if (file_exists(__DIR__ . '/database.local.php')) {
    require __DIR__ . '/database.local.php';
}

if (!isset($_SESSION['invite_id']) && !isset($_SESSION['user'])) {
    $_SESSION['invite_id'] = bin2hex(random_bytes(16));
}