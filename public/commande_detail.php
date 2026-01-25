<?php
require_once __DIR__ . '/../config/config.php';

// Vérifer si user connecté
if(!isset($_SESSION['user'])){
    header("Location: connexion.php");
    exit;
};

// Identifier user
$utilisateur_id = $_SESSION['user']['id'];

if(!isset($_GET['id'])){
    header("Location: commandes.php");
    exit;
}else{

    // Affecter id commande
    $commande_id = (int) $_GET['id'];

    // Controler que la commande appartient bien à l'utilisateur
    $query = "SELECT id, date_commande, statut, total FROM commandes WHERE id = :cid AND utilisateur_id = :uid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'cid' => $commande_id,
        'uid' => $utilisateur_id,
    ]);
    $controle = $stmt->fetch(PDO::FETCH_ASSOC);

    // La verification    
    if(empty($controle)){
        header("Location: commandes.php");
        exit;
    }else{
        // Récupérer les produits de la commande
        $query = "SELECT cp.quantite, cp.prix_unitaire, p.nom FROM commande_produits cp JOIN produits p ON cp.produit_id = p.id WHERE cp.commande_id = :cid";
        $stmt= $pdo->prepare($query);
        $stmt->execute([
            'cid' => $commande_id,
        ]);
        $cp = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
};
?>
<?php include "../includes/header.php"; ?>
    <main>
        <h2>Détails de la commande #<?=$commande_id?></h2>
        <p>Date:<?=$controle['date_commande'] ?></p>
        <p>Statut:<?=statutCommande($controle['statut'])?></p>
        <p>Total:<?=$controle['total'] ?> €</p>
        <?php if(isset($cp)):?>
            <h5>Liste des produits:</h5>
            <?php foreach($cp as $produit_list): ?>
                <ul>
                    <li>Produit: <?=$produit_list['nom']?></li>
                    <li>Quantité: <?=$produit_list['quantite']?></li>
                    <li>Prix unitaire: <?=$produit_list['prix_unitaire']?> €</li>
                    <li>Prix Ligne: <?=$lignetotal = $produit_list['prix_unitaire']*$produit_list['quantite']?></li>
                </ul>
            <?php endforeach; ?>
        <?php endif ?>
    </main>
<?php include "../includes/footer.php"; ?>
    

