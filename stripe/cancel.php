<?php
$commande_id = (int) ($_GET['id'] ?? 0);
if ($commande_id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE commandes
     SET statut = 'annulee'
     WHERE id = :id"
);
$stmt->execute(['id' => $commande_id]);
header("Location: " . BASE_URL . "/public/panier.php");
exit;
