<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/includes/functions.php';

// 1. Vérifier admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../public/connexion.php");
    exit();
}

// ---------------------------
// VARIABLES À REMPLIR EN SQL
// ---------------------------
$commande_total = sqlValue($pdo, "SELECT COUNT(*) FROM commandes");
$commandes_7j = sqlValue($pdo, "SELECT COUNT(*) FROM commandes WHERE date_commande >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$chiffre_30j = sqlValue($pdo, "SELECT SUM(total) FROM commandes WHERE statut = 'valide' AND date_commande >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$commandes_en_attente = sqlValue($pdo, "SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente'");

$users_total = sqlValue($pdo, "SELECT COUNT(*) FROM users");
$users_30j = sqlValue($pdo, "SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$users_actifs = sqlValue($pdo, "SELECT COUNT(DISTINCT utilisateur_id) FROM commandes WHERE date_commande >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$produits_rupture = sqlValue($pdo, "SELECT COUNT(*) FROM produits WHERE stock <= 5");
$produits_top = sqlFetchAll($pdo, "SELECT p.nom, SUM(cp.quantite) AS total_vendu FROM commande_produits cp JOIN produits p ON cp.produit_id = p.id GROUP BY cp.produit_id ORDER BY total_vendu DESC LIMIT 3");


?>

<?php 

include "../includes/header.php"; ?>

<h1>Dashboard Admin</h1>

<section>
    <h2>Commandes</h2>
    <p>Total commandes : <?= $commande_total ?? 0 ?></p>
    <p>Commandes 7 derniers jours : <?= $commandes_7j ?? 0 ?></p>
    <p>Chiffre d'affaires 30 jours : <?= $chiffre_30j != null ? $chiffre_30j : 0 ?> €</p>
    <p>Commandes en attente : <?= $commandes_en_attente ?></p>
</section>

<section>
    <h2>Utilisateurs</h2>
    <p>Total utilisateurs : <?= $users_total ?></p>
    <p>Nouveaux utilisateurs (30j) : <?= $users_30j ?></p>
    <p>Utilisateurs actifs : <?= $users_actifs ?></p>
</section>

<section>
    <h2>Produits</h2>
    <p>Produits en rupture / faible stock : <?= $produits_rupture ?></p>

    <h3>Top ventes</h3>
    <ul>
        <?php foreach($produits_top as $p): ?>
            <li><?= htmlspecialchars($p['nom']) ?> — <?= htmlspecialchars($p['total_vendu']) ?> vendus</li>
        <?php endforeach; ?>
    </ul>
</section>

<?php include "../includes/footer.php"; ?>
