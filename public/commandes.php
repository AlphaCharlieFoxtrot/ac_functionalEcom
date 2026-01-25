<?php
require_once __DIR__ . '/../config/config.php';

// Vérifier si user connecté
if(!isset($_SESSION['user'])){
    header("Location: connexion.php");
    exit;
}

// Identifier utilisateur
$utilisateur_id = $_SESSION['user']['id'];

// Récupérer toutes les commandes de l'utilisateur
$query = "SELECT id, date_commande, statut, total FROM commandes WHERE utilisateur_id = :uid ORDER BY date_commande DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'uid' => $utilisateur_id,
]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include "../includes/header.php"; ?>
<h1>Mes Commandes</h1>
<?php if(!$commandes): ?>
    <p>Aucune commande.</p>
<?php else: ?>
    <?php foreach($commandes as $commande): ?>
        <ul>
            <li>Commande numéro:<?=$commande['id']?></li>
            <li>Le <?=$commande['date_commande']?></li>
            <li>Statut: <?=statutCommande($commande['statut'])?></li>
            <li>Prix total: <?=$commande['total']?> €</li>
            <li><a href="commande_detail.php?id=<?=$commande['id']?>">Voir</a></li>
        </ul>
    <?php endforeach; ?>
<?php endif; ?>
<?php include "../includes/footer.php"; ?>