<?php
    require_once '../../config/config.php';

    //1. verifier que user est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../../public/connexion.php');
        exit();
    }

    //2.initialiser variable vide donnee formulaire + message erreur
    $nom = '';
    $error_message = '';
    
    //POST
    //1. controle methode post
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //2. recuperer valeur reçus
        $nom = trim($_POST['nom'] ?? '');

        if($nom === ''){
            $error_message = "Le nom de la catégorie ne peut pas être vide.";
        }else{
            //5. INSERT INTO categories
            $query = "INSERT INTO categories (nom) 
                    VALUES (:nom)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'nom' => $nom
            ]);

            //6. redirection admin_liste_categorie.php avec message succès
            $_SESSION['success_message'] = "Catégorie ajoutée avec succès.";
            header('Location: admin_categories.php');
            exit();
        }
    }
?>
<?php include '../../includes/header.php'; ?>

    <h1>Ajouter une Catégorie</h1>

    <?php if($error_message): ?>
        <p style="color: red;"><?= $error_message; ?></p>
    <?php endif ; ?>
    <form method="POST">
        <div>
            <label for="nom">Nom de la catégorie :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom); ?>" required>
        </div>
        <div>
            <button type="submit">Ajouter la catégorie</button>
        </div>
    </form>

<?php include '../../includes/footer.php'; ?>