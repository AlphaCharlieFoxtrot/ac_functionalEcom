<?php 
require_once '../../config/config.php';

//1. Vérifier que user est admin 
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../public/connexion.php');
    exit();
}

//2. Fetch tous les utilisateurs (SELECT id, username, email, role, created_at) 

$query = "SELECT id, username, email, role, created_at FROM users ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error_message = '';

//3. Si vide => message "Aucun utilisateur" 
if (!$users) {
    $error_message = "Aucune utilisateur.";
}

/*4. Afficher tableau : - ID - Username - Email - Rôle - Date - Actions (modifier / supprimer)*/
?>

<?php include '../../includes/header.php'; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?= htmlspecialchars($_SESSION['error_message']); ?></p>
    <?php unset($_SESSION['error_message']); ?> 
<?php endif; ?> 

<?php if (isset($_SESSION['success_message'])): ?>
    <p style="color:green"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
    <?php unset($_SESSION['success_message']); ?> 
<?php endif; ?>

<h1>Gestion des utilisateurs</h1> 
    <?php if ($error_message !== ''): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message); ?></p> 
    <?php else: ?>
    <p>Liste des utilisateurs :</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']); ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td><?= htmlspecialchars($user['created_at']); ?></td>
                    <td>
                        <a href="admin_modifier_utilisateur.php?id=<?= $user['id']; ?>">Modifier</a> /
                        <a href="admin_supprimer_utilisateurs.php?id=<?= $user['id']; ?>"
                            onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>