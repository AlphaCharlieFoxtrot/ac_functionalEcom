<?php
    require_once '../../config/config.php';

    //controle admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header("Location: ../public/connexion.php");
        exit();
    }

    //recuperer et controler id user à delete
    $user_id = intval($_GET['id'] ?? 0);

    if($user_id === 0){
        $_SESSION['error_message'] = "Utilisateur introuvable.";
        header("Location: admin_utilisateurs.php");
        exit();
    }

    //fetch et controle id user à delete
    $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id' => $user_id
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //controle user id ne soit pas vide
    if(!$user){
        $_SESSION['error_message'] = "Utilisateur introuvable.";
        header("Location: admin_utilisateurs.php");
        exit();
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //controle que user id != session user id pour delete
        if($user_id === $_SESSION['user']['id']){
            $_SESSION['error_message'] = "Un admin ne peut pas supprimer son propre compte.";
            header("Location: admin_utilisateurs.php");
            exit();
        }

        //COUNT et controle que user id n'ait pas de commandes en cours
        $query = "SELECT COUNT(*) FROM commandes WHERE utilisateur_id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'id' => $user_id
        ]);
        $commande_user = $stmt->fetchColumn();

        if($commande_user > 0){
            $_SESSION['error_message'] = "Cet utilisateur a une ou plusieurs commandes non cloturé.";
            header("Location: admin_utilisateurs.php");
            exit();
        }

        //Delete request
        $deletequery = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($deletequery);
        $stmt->execute([
            'id' => $user_id
        ]);

        $_SESSION['success_message'] = "Utilisateur supprimé avec succès.";
        header("Location: admin_utilisateurs.php");
        exit();    
    }
    
?>

<?php include '../../includes/header.php'; ?>

    <h1>
        Voulez-vous vraiment supprimer cet utilisateur ?
    </h1>

    <p>
        <strong>
            <?= htmlspecialchars($user['username']); ?>
        </strong>
    </p>
    <p>
        <strong>
            <?= htmlspecialchars($user['email']); ?>
        </strong>
    </p>

    <form method="post">
        <button type="submit" style = "background:red;color:white">CONFIRMER</button>
    </form>
    <a style="text-decoration:none;" href="admin_utilisateurs.php">
        ANNULER
    </a>

<?php include '../../includes/footer.php'; ?>

    

    