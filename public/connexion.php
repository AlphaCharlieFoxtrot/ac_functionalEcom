<?php 
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1) Récupérer l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :e LIMIT 1");
    $stmt->execute(['e' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])) {
        // 1) Créer la session
        $_SESSION['user'] = $user;

        if(isset($_SESSION['invite_id'])){
            // Fusionner le panier invité avec le panier utilisateur
            $stmt = $pdo->prepare("UPDATE panier SET utilisateur_id = :user_id, invite_id = NULL WHERE invite_id = :invite_id");
            $stmt->execute(['user_id' => $user['id'], 'invite_id' => $_SESSION['invite_id']]);
        }

        // 2) Vérifier le rôle admin
        if($user['role'] === 'admin'){
            header("Location: ../admin/admin_dashboard.php");
            exit();
        }

        // 3) Connexion ok
        header("Location: index.php");
        exit;

    }else{
        $error = "Email ou mot de passe incorrect.";
    }

}

?>

<?php
include "../includes/header.php";
?>

<div class="warning">
    <?php if (isset($error)): ?>
        <p><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
</div>

<h2>Connexion</h2>

<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required>

    <label >Mot de passe</label>
    <input type="password" name="password" required>
    <button type="submit">Connexion</button>
</form>

<?php include "../includes/footer.php"; ?>