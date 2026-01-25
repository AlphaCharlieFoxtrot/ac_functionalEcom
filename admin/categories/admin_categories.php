<?php 
    require_once __DIR__ . '/../../config/config.php';

//1. vérifier que user est admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header('Location: ../../public/connexion.php');
    exit();
}

//2. récupérer toutes les catégories depuis la bdd
$query = "SELECT * FROM categories ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error_message = '';

if(!$categories){
    $error_message = "Aucune catégorie trouvée.";
}
?>

<?php include '../../includes/header.php'; ?>

<h1>Gestion des Catégories</h1>

<div>
    <a href="admin_ajouter_categorie.php">Ajouter une nouvelle catégorie</a>
</div>

<?php if(isset($_SESSION['success_message'])): ?>
    <p style="color:green"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if($error_message): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<?php if($categories): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($categories as $ctg): ?>
            <tr>
                <td><?= htmlspecialchars($ctg['id']); ?></td>
                <td><?= htmlspecialchars($ctg['nom']); ?></td>
                <td>
                    <a href="admin_modifier_categorie.php?id=<?= $ctg['id']; ?>">Modifier</a>
                    |
                    <a href="admin_supprimer_categorie.php?id=<?= $ctg['id']; ?>" onclick="return confirm('Supprimer cette catégorie ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
