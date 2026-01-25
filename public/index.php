<?php
require_once __DIR__ . '/../config/config.php';


// Récupération des produits (limités pour l'accueil)
$stmt = $pdo->query("SELECT * FROM produits ORDER BY created_at DESC LIMIT 6");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php  include "../includes/header.php"; ?>
<h1>
    Derniers Produits
</h1>
<div class="products">
    <?php foreach ($produits as $p): ?>
        <div class="product-card">
            <img src="/asset/images/<?= htmlspecialchars($p['image']) ?>" alt="">
            <h3><?=htmlspecialchars($p['nom']) ?></h3>
            <p><?=htmlspecialchars($p['prix']) ?> €</p>
            <a href="produit.php?id=<?= $p['id'] ?>">Voir</a>
        </div>
    <?php endforeach; ?>
</div>

<?php include "../includes/footer.php"; ?>