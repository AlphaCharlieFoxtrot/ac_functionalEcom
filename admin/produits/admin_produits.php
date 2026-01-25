<?php
    require_once '../../config/config.php';
    //1.verifier que user est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../public/connexion.php');
        exit();
    }

    //2. recuperer tous les produits depuis la bdd dans $produits
    $query = "SELECT p.*, c.nom AS categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.id DESC";
    $stmt= $pdo->prepare($query);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $error_message = '';

    //3. gestion empty($produits)
    if(!$produits){
        $error_message = "Pas de produits pour le moment.";
    }
?>

<?php include '../../includes/header.php'; ?>

    <?php if(isset($_SESSION['error_message'])): ?>
        <p style="color: red;"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['success_message'])): ?>
    <p style="color:green"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <h1>Gestion des Produits</h1>
        <?php if($error_message !== ''): ?>
            <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
            <div>
                <a href="admin_ajouter_produit.php">Ajouter un nouveau produit</a>
            </div>
        <?php else: ?>
            <p>Liste des produits disponibles :</p>
            <div>
                <a href="admin_ajouter_produit.php">Ajouter un nouveau produit</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Categorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produits as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['id']); ?></td>
                            <td><?= htmlspecialchars($produit['nom']); ?></td>
                            <td><?= htmlspecialchars($produit['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                            <td><?= htmlspecialchars($produit['prix']); ?> €</td>
                            <td><?= htmlspecialchars($produit['stock']); ?></td>
                            <td><?= htmlspecialchars($produit['created_at']); ?></td>

                            <td>
                                <a href="admin_modifier_produit.php?id=<?= $produit['id']; ?>">Modifier</a>
                                |
                                <a href="admin_supprimer_produit.php?id=<?= $produit['id']; ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

<?php include '../../includes/footer.php'; ?>