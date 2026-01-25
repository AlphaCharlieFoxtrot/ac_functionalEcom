<?php 
require_once "../config/config.php";

if (!isset($_POST['produit_id'])) {
    header("Location: index.php");
    exit;
}

$produit_id = intval($_POST['produit_id']);
$quantite = intval($_POST['quantite']);

// 1) ID user ou invité
$utilisateur_id = $_SESSION['user']['id'] ?? null;
$invite_id = $_SESSION['invite_id'] ?? null;

// 2) Vérifier si le produit est déjà dans le panier
if (!isset($utilisateur_id)) {
$query = "SELECT id, quantite FROM panier WHERE produit_id = :pid AND utilisateur_id = :uid LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'pid' => $produit_id,
    'uid' => $utilisateur_id,
]);
}else{
$query = "SELECT id, quantite FROM panier WHERE produit_id = :pid AND invite_id = :iid LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'pid' => $produit_id,
    'iid' => $invite_id,
]);
}

$ligne = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ligne) {
    // 3) Si existe on augmente la quantite
    $newQ = $ligne['quantite'] + 1;
    $stmt = $pdo->prepare("UPDATE panier SET quantite = :q WHERE id = :id");
    $stmt->execute([
        'q' => $newQ,
        'id' => $ligne['id']
    ]);
} else {
    // 4) Sinon -> insertion

    if (!isset($utilisateur_id)) {
        $stmt = $pdo->prepare("INSERT INTO panier (utilisateur_id, produit_id, quantite) VALUES (:uid, :pid, :q)");
        $stmt->execute([
            'uid' => $utilisateur_id,
            'pid' => $produit_id,
            'q' => $quantite
        ]);
    }else{
        $stmt = $pdo->prepare("INSERT INTO panier (invite_id, produit_id, quantite) VALUES (:iid, :pid, :q)");
        $stmt->execute([
            'iid' => $invite_id,
            'pid' => $produit_id,
            'q' => $quantite
        ]);
    }
}

header("Location: panier.php");
exit;
?>