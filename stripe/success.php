<?php
require_once __DIR__ . '/../config/config.php';

$commande_id = (int) ($_GET['id'] ?? 0);
if ($commande_id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE commandes
     SET statut_payement = 'succces'
     WHERE id = :id"
);
$stmt->execute(['id' => $commande_id]);

header("Location: ../commande_validee.php?id=" . $commande_id);
exit();
