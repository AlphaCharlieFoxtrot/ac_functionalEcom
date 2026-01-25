<?php
require_once '../config/config.php';

$utilisateur_id = $_SESSION['user']['id'] ?? null;
$invite_id = $_SESSION['invite_id'] ?? null;

if(!isset($utilisateur_id) && !isset($invite_id)){
    $invite_id = uniqid('invite_', true);
    $_SESSION['invite_id'] = $invite_id;
}