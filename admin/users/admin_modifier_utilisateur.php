<?php
require_once '../../config/config.php';

// 1. Vérifier que user est admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../public/connexion.php');
    exit();
}

// 2. Récupérer ID utilisateur
$user_id = intval($_GET['id'] ?? 0);

if ($user_id === 0) {
    $_SESSION['error_message'] = "ID utilisateur invalide.";
    header("Location: admin_utilisateurs.php");
    exit();
}

// 3. Récupérer infos user
$query = "SELECT * FROM users WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error_message'] = "Utilisateur introuvable.";
    header("Location: admin_utilisateurs.php");
    exit();
}

$error_message = '';

// 4. Préremplissage des champs
$name = $user['username'];
$email = $user['email'];
$role = $user['role'];

// Rôles autorisés
$valid_roles = ['user', 'admin'];

// 5. POST — Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération valeurs POST
    $name = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Vérifier si email déjà utilisé par un autre user
    $query = "SELECT COUNT(*) FROM users WHERE email = :email AND id != :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'email' => $email,
        'id' => $user_id
    ]);
    $email_exist = $stmt->fetchColumn();

    // Validations
    if ($name === '' || $email === '' || $role === '') {
        $error_message = "Tous les champs sont obligatoires.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Email invalide.";
    }
    elseif ($email_exist > 0) {
        $error_message = "Cet email est déjà utilisé.";
    }
    elseif (!in_array($role, $valid_roles)) {
        $error_message = "Rôle invalide.";
    }
    // Empêche un admin de se retirer son propre rôle admin
    elseif ($user_id == $_SESSION['user']['id'] && $role !== 'admin') {
        $error_message = "Tu ne peux pas retirer ton rôle admin.";
    }
    // Empêche un admin standard de rétrograder un autre admin
    elseif ($user['role'] === 'admin' && $user_id != $_SESSION['user']['id'] && $role !== 'admin') {
        $error_message = "Tu ne peux pas retirer le rôle admin à un autre administrateur.";
    }

    // Si aucune erreur : UPDATE
    if ($error_message === '') {
        $query = "UPDATE users 
                  SET username = :username, email = :email, role = :role 
                  WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'username' => $name,
            'email' => $email,
            'role' => $role,
            'id' => $user_id
        ]);

        $_SESSION['success_message'] = "Utilisateur modifié avec succès.";
        header("Location: admin_utilisateurs.php");
        exit();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<h2>Modifier l'utilisateur</h2>

<?php if ($error_message): ?>
    <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<form method="POST">

    <label for="nom">Nom :</label><br>
    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($name) ?>"><br><br>

    <label for="email">Email :</label><br>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>"><br><br>

    <label for="role">Rôle :</label><br>
    <select name="role" id="role">
        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select><br><br>

    <button type="submit">Modifier</button>
</form>

<div>
    <a href="admin_utilisateurs.php" style="text-decoration:none;">ANNULER</a>
</div>

<?php include '../../includes/footer.php'; ?>
