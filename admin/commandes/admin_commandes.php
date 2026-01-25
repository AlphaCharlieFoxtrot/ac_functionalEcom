<?php 
    require_once '../../config/config.php';

    //controle admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header("Location: ../../public/connexion.php");
        exit();
    }

    //fetch toutes les commandes
    $query = "SELECT c.id AS commande_id, c.total, c.statut, c.date_commande, u.username, u.email FROM commandes c LEFT JOIN users u ON c.utilisateur_id = u.id ORDER BY c.id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $error_message = '';

    //controle commandes
    if(!$commandes){
        $error_message = "Aucune commande.";
    }

?>

<?php include '../../includes/header.php' ?>

    <h1>Gestion des commandes.</h1> 
    <?php if ($error_message !== ''): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p> 
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Utilisateur</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?= htmlspecialchars($commande['username'] ?? 'Compte supprimé'); ?></td>
                            <td><?= htmlspecialchars($commande['total']); ?> €</td>
                            <td><?= htmlspecialchars($commande['statut']); ?></td>
                            <td><?= htmlspecialchars($commande['date_commande']); ?></td>
                            <td>
                                <a href="admin_commande_details.php?id=<?= $commande['commande_id']; ?>">Voir</a>
                                <a href="admin_supprimer_commande.php?id=<?= $commande['commande_id']; ?>" onclick="return confirm('Supprimer cet commande ?')>Supprimer</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    <?php endif; ?>

<?php include '../../includes/footer.php' ?>