<?php
    require_once __DIR__ . '/../../config/config.php';

    //1. vérifier que l'utilisateur est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../../public/connexion.php');
        exit();
    }

    //2. récupérer l'id et 3. si id invalide → redirection
    $categorie_id = intval($_GET['id'] ?? 0);

    if($categorie_id === 0){
        header('Location: admin_categories.php');
        exit();
    }

    //4. fetch de la catégorie
    $query = "SELECT * FROM categories WHERE id = :cid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'cid' => $categorie_id,
    ]);
    $categories = $stmt->fetch(PDO::FETCH_ASSOC);

    $error_message = '';

    //5. si catégorie introuvable → redirection
    if(!$categories){
        $_SESSION['error_message'] = "Catégorie introuvable.";
        header('Location: admin_categories.php');
        exit();
    }

    $nom = $categories['nom'];

    //POST
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nom = trim($_POST['nom'] ?? '');

        //2. vérifier champs obligatoires non vides
        if($nom === ''){
            $error_message = "Le nom de la catégorie ne peut pas être vide.";
        } 
        
        if ($error_message === '') {
            //3. mettre à jour la catégorie dans la bdd
            $query = "UPDATE categories SET nom = :nom WHERE id = :cid";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'nom' => $nom,
                'cid' => $categorie_id,
            ]);

            $_SESSION['success_message'] = "Catégorie mise à jour avec succès.";
            header('Location: admin_categories.php');
            exit();
        }
    }
?>
<?php include '../../includes/header.php'; ?>

<h1>Modifier la Catégorie</h1>
<?php if($error_message): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
<?php endif; ?>
    <form method="POST">
        <div>
            <label for="nom">Nom de la Catégorie:</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom); ?>" required>
        </div>
        <div>
            <button type="submit">Mettre à Jour la Catégorie</button>
        </div>
    </form>
<?php include '../../includes/footer.php'; ?>