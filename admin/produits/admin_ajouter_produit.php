<?php
    require_once __DIR__ . '/../../config/config.php';

//1. vérifier que utilisateur est admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header('Location: ../public/connexion.php');
    exit();
}

//2. Récupérer la liste des catégories (SELECT * FROM categories) ← important
$query = "SELECT * FROM categories";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

//3.initialiser variable vide donnee formulaire + message erreur
$nom = '';
$description = '';
$prix = '';
$categorie_id = '';
$stock = '';

$error_message = '';

//POST
//1. controle methode post
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //2. récupérer les valeurs envoyées (nom, description, prix, categorie_id, stock)
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $categorie_id = intval($_POST['categorie_id'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);

    $id_valides = array_column($categories, 'id');

    /*3. vérifier champs obligatoires non vides
      - champs non vides
      - prix numérique
      - stock numérique et >= 0
      - categorie_id existe dans categories*/
    if($nom === '' || $description === '' || $stock < 0){
        $error_message = "Les champs nom, description et stock doivent être remplis et valides.";
    }

    if(!is_numeric($prix)){
        $error_message = "Le prix doit être numérique.";
    }

    if(!in_array($categorie_id, $id_valides)){
        $error_message = "La catégorie sélectionnée n'est pas valide.";
    }

    //4. Si erreur → réafficher formulaire + message
    if($error_message === ''){
        //5.  INSERT INTO produits (...)
        $query = "INSERT INTO produits (nom, description, prix, categorie_id, stock, created_at) 
                  VALUES (:nom, :description, :prix, :categorie_id, :stock, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'categorie_id' => $categorie_id,
            'stock' => $stock,
        ]);

        //6. Redirection admin_produits.php
        header('Location: admin_produits.php');
        exit();
    }
}

?>
<?php include '../../includes/header.php'; ?>
    <h2>Ajouter un produit</h2>
    <!--Affichage message erreur-->
    <?php if($error_message): ?>
    <p style="color:red"><?= $error_message ?></p>
    <?php endif; ?>
    <!--Affichage formulaire(champs vide)-->
    <form method="POST">
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>"><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea><br><br>

        <label for="prix">Prix (ex: 19.99):</label><br>
        <input type="text" id="prix" name="prix" value="<?= htmlspecialchars($prix) ?>"><br><br>

        <select name="categorie_id" id="categorie_id">
            <option value="">--Choisir une catégorie--</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                <?= ($categorie_id == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="stock">Stock:</label><br>
        <input type="number" id="stock" name="stock" value="<?= $stock ?>"><br><br>

        <input type="submit">
    </form>

<?php include '../../includes/footer.php'; ?>