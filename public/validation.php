<?php
require_once '../config/config.php';
require_once '../includes/panier_context.php';

// Vérification méthode POST sinon -> panier
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: panier.php");
    exit;
}

// Récupérer les lignes du panier
$query = "SELECT pa.*, p.nom, p.prix FROM panier pa JOIN produits p ON pa.produit_id = p.id WHERE (
    pa.utilisateur_id = :uid AND :uid IS NOT NULL
    )
    OR
    (
        pa.invite_id = :iid AND :iid IS NOT NULL
    )";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'uid' => $utilisateur_id,
    'iid' => $invite_id
]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = 0;

if (empty($items)) {
    header("Location: panier.php");
    exit;
}

foreach ($items as $item) {
    $total += $item['prix'] * $item['quantite'];
}

// Insérer la commande
if (isset($_POST['confirme'])) {

    if ($utilisateur_id !== null) {
        $stmt = $pdo->prepare(
            "INSERT INTO commandes (utilisateur_id, total, date_commande)
             VALUES (:uid, :t, NOW())"
        );
        $stmt->execute([
            'uid' => $utilisateur_id,
            't'   => $total
        ]);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO commandes (invite_id, total, date_commande)
             VALUES (:iid, :t, NOW())"
        );
        $stmt->execute([
            'iid' => $invite_id,
            't'   => $total
        ]);
    }

    $commande_id = $pdo->lastInsertId();

    foreach ($items as $item) {
        $stmt = $pdo->prepare(
            "INSERT INTO commande_produits
             (commande_id, produit_id, quantite, prix_unitaire)
             VALUES (:cid, :pid, :q, :pu)"
        );
        $stmt->execute([
            'cid' => $commande_id,
            'pid' => $item['produit_id'],
            'q'   => $item['quantite'],
            'pu'  => $item['prix']
        ]);
    }

    $stmt = $pdo->prepare(
        "DELETE FROM panier
         WHERE (
             utilisateur_id = :uid AND :uid IS NOT NULL
         )
         OR (
             invite_id = :iid AND :iid IS NOT NULL
         )"
    );
    $stmt->execute([
        'uid' => $utilisateur_id,
        'iid' => $invite_id
    ]);

    header("Location: commande_validee.php?id=" . $commande_id);
    exit;
}

?>

<h2>Confirmation de la commande</h2>
<ul class="confirm_list_command">
    <?php foreach ($items as $item): ?>
        <li>
            Produit: <?= htmlspecialchars($item['nom']) ?> -
            Prix unitaire: <?= $item['prix'] ?> € -
            Quantité: <?= $item['quantite'] ?> -
            Total ligne: <?= $item['prix'] * $item['quantite'] ?> €
        </li>
    <?php endforeach; ?>
</ul>
<h3>Prix total de votre panier : <?= $total ?> €</h3>
<form method="post">
    <button type="submit" name="confirme">Confirmer la commande</button>
</form>