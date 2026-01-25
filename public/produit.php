<?php
require_once "../config/config.php";
// Récupérer l'ID du produit depuis l'URL
$produit_id = intval($_GET['id'] ?? 0);
// Si l'ID n'est pas valide, rediriger vers la page d'accueil
if($produit_id === 0){
    header("Location: index.php");
    exit();
}

// Récupérer les détails du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $produit_id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le produit n'existe pas, rediriger vers la page d'accueil
if(!$produit){
    header("Location: index.php");
    exit();
}

?>

<?php include "../includes/header.php"; ?>
<h1>Fiche produit: <?= htmlspecialchars($produit['nom']); ?></h1>

<div class="product-detail">
    <img src="../assets/images/<?= htmlspecialchars($produit['image']) ?>" alt="Image du produit.">
    <h2><?= htmlspecialchars($produit['nom']) ?></h2>
    <p>Prix: <?= htmlspecialchars($produit['prix']) ?> €</p>
    <p>Description: <?= htmlspecialchars($produit['description']) ?></p>
    <form method="POST" action="ajouter_panier.php">
        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">
        <label for="quantite">Quantité:</label>
        <input type="number" id="quantite" name="quantite" value="1" min="1" required>
        <button type="submit">Ajouter au panier</button>
    </form>
</div>