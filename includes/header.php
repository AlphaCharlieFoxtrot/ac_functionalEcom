<?php 
$logged = isset($_SESSION['user']);
$isAdmin = $logged && $_SESSION['user']['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<header>
<nav>
    <a href="<?= BASE_URL ?>/public/index.php">Accueil</a>
    <!-- <a href="<?= BASE_URL ?>/public/boutique.php">Boutique</a> -->
    <a href="<?= BASE_URL ?>/public/panier.php">Panier</a>

    <?php if(!$logged): ?>
        <!-- Invité -->
        <a href="<?= BASE_URL ?>/public/connexion.php">Connexion</a>
        <a href="<?= BASE_URL ?>/public/inscription.php">Inscription</a>

    <?php elseif($isAdmin): ?>
        <!-- Admin -->
        <a href="<?= BASE_URL ?>/admin/categories/admin_categories.php">Catégories</a>
        <a href="<?= BASE_URL ?>/admin/commandes/admin_commandes.php">Commandes</a>
        <a href="<?= BASE_URL ?>/admin/produits/admin_produits.php">Produits</a>
        <a href="<?= BASE_URL ?>/admin/users/admin_utilisateurs.php">Utilisateurs</a>
        <a href="<?= BASE_URL ?>/admin/admin_dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>/public/deconnexion.php">Déconnexion</a>

    <?php else: ?>
        <!-- User simple -->
        <a href="<?= BASE_URL ?>/public/commandes.php">Mes commandes</a>
        <a href="<?= BASE_URL ?>/public/deconnexion.php">Déconnexion</a>
    <?php endif; ?>

</nav>
</header>
