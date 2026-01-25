<?php
require_once '../config/config.php';

// vérifier presence id commande
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

// Affecter id commande
$commande_id = intval($_GET['id'] ?? 0);

if($commande_id === 0){
    header('Location: panier.php');
    exit();
}

// Charger détails commandes en sécurité
$query = "SELECT * FROM commandes WHERE id = :cid LIMIT 1";
$stmt=$pdo->prepare($query);
$stmt->execute([
    'cid' => $commande_id
]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

// Si pas de commande trouvée -> index
if(!$commande){
    header("Location: index.php");
    exit;
}else{
    // Récupérer les produits de la commande
    $query = "SELECT cp.quantite, cp.prix_unitaire, p.nom FROM commande_produits cp JOIN produits p ON cp.produit_id = p.id WHERE cp.commande_id = :commande_id";
    $stmt= $pdo->prepare($query);
    $stmt->execute([
        'commande_id' => $commande_id,
    ]);
    $cprd = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php if(!empty($cprd)):?>
    <?php include "../includes/header.php"; ?>
    <h1>Commande Validée</h1>
    <h2>Votre confirmation d'achat à été pris en compte.</h2>
    <h3>Récapitulatif de votre commande:</h3>
    <ul>
        <li>
            Date de la commande: <?=htmlspecialchars($commande['date_commande'])?>
        </li>
        <?php $total = 0; ?>
        <?php foreach($cprd as $prd): ?>
            <li>
                <p>
                    Produit: <?=htmlspecialchars($prd['nom'])?><br>
                    Quantité: <?=htmlspecialchars($prd['quantite'])?><br>
                    Prix unitaire: <?=htmlspecialchars($prd['prix_unitaire'])?> €<br>
                    prix total: <?=htmlspecialchars($lignetotal = $prd['prix_unitaire']*$prd['quantite'])?>
                </p>
                <?php $total += $lignetotal; ?>
            </li>
        <?php endforeach; ?>
        <li><strong>Total de la commande: <?=htmlspecialchars($total)?> €</strong></li>
    </ul>
    <button onclick="window.location.href='boutique.php'">
     Retour à la boutique
    </button>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="commandes.php" class="btn">Voir mes commandes</a>
        <?php endif; ?>
<?php else: ?>
    <h2>Aucun détail de commande disponible.</h2>
<?php endif; ?>
<?php include "../includes/footer.php"; ?>