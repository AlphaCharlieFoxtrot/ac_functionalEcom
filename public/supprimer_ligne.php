<?php
require_once "../config/config.php";

// Vérifier sélection produit
if (!isset($_GET['id'])) {
    header("Location: panier.php");
    exit;
}

// Récupérer l'ID de la ligne à supprimer
$ligne_id = (int) $_GET['id'];

// Préparer et exécuter la requête de suppression
$stmt = $pdo->prepare("DELETE FROM panier WHERE id = :id");
$stmt->execute(['id' => $ligne_id]);

// Rediriger vers la page du panier après suppression
header("Location: panier.php");
exit;