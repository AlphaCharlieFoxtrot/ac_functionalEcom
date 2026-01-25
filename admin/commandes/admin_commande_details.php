<?php
require_once '../../config/config.php';

// 1. Vérifier admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/connexion.php");
    exit();
}

// 2. Récupérer ID
$commande_id = intval($_GET['id'] ?? 0);

if ($commande_id === 0) {
    $_SESSION['error_message'] = "ID de commande invalide.";
    header("Location: admin_commandes.php");
    exit();
}

// 3. Fetch commande + user
$query = "
    SELECT 
        c.id AS commande_id,
        c.total,
        c.statut,
        c.date_commande,
        u.username,
        u.email
    FROM commandes c
    LEFT JOIN users u ON c.utilisateur_id = u.id
    WHERE c.id = :id
    LIMIT 1
";

$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $commande_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Contrôle existence
if (!$commande) {
    $_SESSION['error_message'] = "Commande introuvable.";
    header("Location: admin_commandes.php");
    exit();
}

$error_message = '';

// 5. Fetch articles de la commande
$query = "
    SELECT 
        p.nom,
        cp.prix_unitaire,
        cp.quantite
    FROM commande_produits cp
    JOIN produits p ON cp.produit_id = p.id
    WHERE cp.commande_id = :id ORDER BY p.nom ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $commande_id]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Contrôle existence
if (!$articles) {
    $error_message = "Aucun article trouvé pour cette commande.";
}

?>

<?php include '../../includes/header.php'; ?>

<h1>Détails de la commande n°:<?= htmlspecialchars($commande_id) ?> (Admin)</h1>
<article>
    <p><strong>Client:</strong> <?= htmlspecialchars($commande['username'] ?? 'Compte supprimé') ?> (<?= htmlspecialchars($commande['email'] ?? 'N/A') ?>)</p>
    <p><strong>Date de commande:</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
    <p><strong>Montant total:</strong> <?= htmlspecialchars($commande['total']) ?> €</p>
    <p><strong>Statut:</strong> <?= htmlspecialchars($commande['statut']) ?></p>

    <h2>Articles dans la commande:</h2>
    <?php if ($error_message): ?>
    <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <ul>
        <?php foreach ($articles as $article): ?>
            <li>
                <?= htmlspecialchars($article['nom']) ?> - 
                Prix: <?= htmlspecialchars($article['prix_unitaire']) ?> € - 
                Quantité: <?= htmlspecialchars($article['quantite']) ?> -
                Sous-total: <?= htmlspecialchars($article['prix_unitaire'] * $article['quantite']) ?> €
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="admin_modifier_commande.php?id=<?= htmlspecialchars($commande_id) ?>">Modifier le statut de la commande</a>
</article>

<?php include '../../includes/footer.php'; ?>