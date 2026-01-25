<?php
require_once __DIR__ . '/../config/config.php';

// Vérification méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL);
    exit;
}

$produit_id = (int) $_POST['produit_id'];
$quantite   = max(1, (int) $_POST['quantite']);

// utilisateur ou invité
$utilisateur_id = $_SESSION['user']['id'] ?? null;
$invite_id      = $_SESSION['invite_id'] ?? null;

// chercher ligne existante
if ($utilisateur_id !== null) {
    $stmt = $pdo->prepare(
        "SELECT id, quantite FROM panier
         WHERE produit_id = :pid AND utilisateur_id = :uid
         LIMIT 1"
    );
    $stmt->execute([
        'pid' => $produit_id,
        'uid' => $utilisateur_id
    ]);
} else {
    $stmt = $pdo->prepare(
        "SELECT id, quantite FROM panier
         WHERE produit_id = :pid AND invite_id = :iid
         LIMIT 1"
    );
    $stmt->execute([
        'pid' => $produit_id,
        'iid' => $invite_id
    ]);
}

$ligne = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ligne) {
    // mise à jour quantité
    $newQ = $ligne['quantite'] + $quantite;
    $stmt = $pdo->prepare("UPDATE panier SET quantite = :q WHERE id = :id");
    $stmt->execute([
        'q'  => $newQ,
        'id' => $ligne['id']
    ]);
} else {
    // insertion
    if ($utilisateur_id !== null) {
        $stmt = $pdo->prepare(
            "INSERT INTO panier (utilisateur_id, produit_id, quantite)
             VALUES (:uid, :pid, :q)"
        );
        $stmt->execute([
            'uid' => $utilisateur_id,
            'pid' => $produit_id,
            'q'   => $quantite
        ]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO panier (invite_id, produit_id, quantite)
             VALUES (:iid, :pid, :q)"
        );
        $stmt->execute([
            'iid' => $invite_id,
            'pid' => $produit_id,
            'q'   => $quantite
        ]);
    }
}

header("Location: " . BASE_URL . "/public/panier.php");
exit;
