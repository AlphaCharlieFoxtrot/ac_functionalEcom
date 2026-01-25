<?php
    require_once __DIR__ . '/../../config/config.php';

    //verif admin
    if(empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin'){
        header("Location: ../../public/connexion.php");
        exit();
    }

    //lecture id
    $commande_id = intval($_GET['id'] ?? 0);

    //verif id
    if($commande_id === 0){
        header("Location: admin_commandes.php");
        exit();
    }

    //fetch commande + user
    $query = "SELECT c.id AS commande_id, c.total, c.statut, c.date_commande, u.username, u.email FROM commandes c LEFT JOIN users u ON c.utilisateur_id = u.id WHERE c.id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $commande_id
    ]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);

    $error_message = '';

    //verif commande
    if(!$commande){
        $_SESSION['error_message'] = "Commande introuvable ou deja supprimé.";
        header("Location: admin_commandes.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Vérifier statut autorisé
    if($commande['statut'] !== 'en_attente' && $commande['statut'] !== 'annulee'){
        $error_message = "Vous ne pouvez supprimer que les commandes en attente ou annulées.";
    }

    // S'il n'y a pas d'erreur → suppression
    if($error_message === ''){
        $deletequery = "DELETE FROM commandes WHERE id = :cid";
        $stmt = $pdo->prepare($deletequery);
        $stmt->execute(['cid' => $commande_id]);

        $_SESSION['success_message'] = "Commande supprimée avec succès.";
        header("Location: admin_commandes.php");
        exit();
    }
}

?>

<?php include '../../includes/header.php'; ?>

    <h1>
        Voulez-vous vraiment supprimer cette commande ?
    </h1>
    <p>
        Commande de:
        <strong>
            <?= htmlspecialchars($commande['username']); ?>
            <?= htmlspecialchars($commande['email']); ?>
        </strong>
    </p>
    <p>
        Effectuée le:
        <strong>
            <?= htmlspecialchars($commande['date_commande']); ?>
        </strong>
    </p>
    <p>
        Statut:
        <strong>
            <?= htmlspecialchars($commande['statut']); ?>
        </strong>
    </p>
    <p>
        Prix total:
        <strong>
            <?= htmlspecialchars($commande['total']); ?>
        </strong>
    </p>
    
    <?php if($error_message): ?>
    <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="post">
        <button type="submit" style = "background:red;color:white">CONFIRMER</button>
    </form>
    <a style="text-decoration:none;" href="admin_commandes.php">
        ANNULER
    </a>

<?php include '../../includes/footer.php'; ?>