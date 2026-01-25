<?php 
require_once __DIR__ . '/../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['password_confirm'];

    // 1) Vérificatio mots de passe
    if ($password !== $confirm){
        $error = "Les mots de passe ne correspondent pas.";
        return $error;
    }
    // 2) Vérification email valide
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Email invalide.";
        return $error;
    }

    else {
        // 3)Vérifier si email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowcount() > 0){
            $error = "Cet email est déja utilisé.";
            return $error;
        } else {
            // 4) Insertion
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (:u, :e, :p, 0, NOW())");
            $stmt->execute([
                'u' => $username,
                'e' =>$email,
                'p' => password_hash($password, PASSWORD_DEFAULT)
            ]);

            // 5) Redirection vers connexion
            header("Location: connexion.php?inscrit=1" );
            exit;
        }
    }
}
?>
<?php
include "../includes/header.php"; 
?>

<div class="warning">
    <?php if (isset($error)): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</div>

<h2>Inscription</h2>
<form method="POST">
    <label for="">Nom d'utilisateur</label>
    <input type="text" name="username" required>

    <label for="">Email</label>
    <input type="email" name="email" required>

    <label for="">Mot de passe</label>
    <input type="password" name="password" required>

    <label for="">Confirmer mot de passe</label>
    <input type="password" name="password_confirm" required>

    <button type="submit">Créer mon compte</button>
</form>

<?php include "../includes/footer.php"; ?>