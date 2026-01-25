<?php
    require_once '../../config/config.php';

    //1.verifier que user est admin
    if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
        header('Location: ../public/connexion.php');
        exit();
    }

    //2. recuperer l'id du produit a supprimer depuis le parametre GET
    $produit_id = intval($_GET['id'] ?? 0);
    if($produit_id === 0){
        header('Location: admin_produits.php');
        exit();
    }

    //3. fetch produit
    $query = "SELECT * FROM produits WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $produit_id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    //4. verifie existence produit
    if(!$produit){
        $_SESSION['error_message'] = "Produit introuvable.";
        header('Location: admin_produits.php');
        exit();
    }else{
        //5. delete produit de la bdd
        $deleteQuery = "DELETE FROM produits WHERE id = :id";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute(['id' => $produit_id]);

        $_SESSION['success_message'] = "Produit supprimé avec succès.";
        header('Location: admin_produits.php');
        exit();
    }

?>
