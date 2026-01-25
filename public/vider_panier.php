<?php
require_once '../config/config.php';

// Vérification identité utilisateur
$utilisateur_id = $_SESSION['user']['id'] ?? null;
$invite_id = $_SESSION['invite_id'] ?? null;

//Préparer la requête de suppression
if(!isset($utilisateur_id)){
    $query = "DELETE FROM panier WHERE utilisateur_id = :uid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['uid' => $utilisaeteur_id]);
}else{
    $query = "DELETE FROM panier WHERE invite_id = :iid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['iid' => $invite_id]);
}
// Rediriger vers la page du panier après vidage
header("Location: panier.php");
exit;
