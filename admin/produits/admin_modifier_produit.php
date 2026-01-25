<?php
    require_once '../../config/config.php';

    //1. vérifier que l'utilisateur est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../public/connexion.php');
        exit();
    }

    //2. récupérer l'id et 3. si id invalide → redirection
    $produit_id = intval($_GET['id'] ?? 0);
    if($produit_id === 0){
        header('Location: admin_produits.php');
        exit();
    }

    //id nom description prix categorie_id image stock created_at
    //4. fetch du produit
    $query = "SELECT * FROM produits WHERE id = :pid LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'pid' => $produit_id,
    ]);

    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    //5. si produit introuvable → redirection
    if(!$produit){
        header('Location: admin_produits.php');
        exit();
    }

    //6. Récupérer la liste des catégories (SELECT * FROM categories) ← important
    $query = "SELECT * FROM categories";
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $error_message = '';

    
    //1. récupérer les valeurs envoyées (nom, description, prix, categorie_id, stock)
    $nom = $produit['nom'];
    $description = $produit['description'];
    $prix = $produit['prix'];
    $categorie_id = $produit['categorie_id'];
    $stock = $produit['stock'];

    //POST
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix = trim($_POST['prix'] ?? '');
        $categorie_id = intval($_POST['categorie_id'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);

        $id_valides = array_column($categories, 'id');

        //2. vérifier champs obligatoires non vides
        if($nom === '' || $description === ''){
            $error_message = "Tous les champs doivent être remplis et valides.";
        } 

        //3. valider prix et stock numériques
        if(!is_numeric($prix)){
            $error_message = "Le prix doit être un nombre.";
        }

        if($stock < 0 || !is_numeric($stock)){
            $error_message = "Le stock doit être un nombre positif.";
        }

        if(!in_array($categorie_id, $id_valides)){
        $error_message = "La catégorie sélectionnée n'est pas valide.";
        }
        
        //si aucune erreur
        if($error_message === ''){
            //4. préparer update sans changer l'image
            $query = "UPDATE produits SET nom = :nom, description = :dcrptn, prix = :prix, categorie_id = :ctg_id, stock = :stk WHERE id = :pid";
            $stmt = $pdo->prepare($query);
            
            //5. exécuter update
            $stmt->execute([
                'nom' => $nom,
                'dcrptn' => $description,
                'prix' => $prix,
                'ctg_id' => $categorie_id,
                'stk' => $stock,
                'pid' => $produit_id,
            ]);

            //6. rediriger vers liste produits
            header('Location: admin_produits.php');
            exit();
        }

    }

?>  
<?php include '../../includes/header.php'; ?>

    <!--6. afficher formulaire pré-rempli(variables $produit)-->
    <h2>Modification du produit:</h2>
    <?php if($error_message !== ''): ?>
        <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>"><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea><br><br>

        <label for="prix">Prix (ex: 19.99):</label><br>
        <input type="text" id="prix" name="prix" value="<?= htmlspecialchars($prix) ?>"><br><br>

        <select name="categorie_id" id="categorie_id">
            <option value="" <?= ($categorie_id == 0) ? 'selected' : '' ?>>--Choisir une catégorie--</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                <?= ($categorie_id == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="stock">Stock:</label><br>
        <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($stock) ?>"><br><br>

        <input type="submit">
    </form>

<?php include '../../includes/footer.php'; ?>