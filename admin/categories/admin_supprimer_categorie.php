<?php
    require_once __DIR__ . '/../../config/config.php';

    //1.verifier que user est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../../public/connexion.php');
        exit();
    }

    //2. recuperer l'id du produit a supprimer depuis le parametre GET
    $categorie_id = intval($_GET['id'] ?? 0);
    if($categorie_id === 0){
        header('Location: admin_categories.php');
        exit();
    }

    //3. fetch produit
    $query = "SELECT * FROM categories WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $categorie_id]);
    $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

    //4. verifie existence produit
    if(!$categorie){
        $_SESSION['error_message'] = "Catégorie introuvable.";
        header('Location: admin_categories.php');
        exit();
    }

    //5. supprimer la categorie
    $query = "DELETE FROM categories WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $categorie_id]);
    $_SESSION['success_message'] = "Catégorie supprimée avec succès.";
    header('Location: admin_categories.php');
    exit();

?>
