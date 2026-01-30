<?php
// Chargement config locale (NON versionnée)
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

if (!isset($_SESSION['invite_id']) && !isset($_SESSION['user'])) {
    $_SESSION['invite_id'] = bin2hex(random_bytes(16));
}