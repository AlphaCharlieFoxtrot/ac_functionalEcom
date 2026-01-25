<?php
require_once __DIR__ . '/../config/config.php';


// Vérifier sélection produit
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: panier.php");
    exit;
}

// Récupérer l'ID de la ligne à modifier et l'action
$ligne_id =(int) $_GET['id'];
$action = $_GET['action'];

// Récupérer la quantité actuelle
$stmt = $pdo->prepare("SELECT quantite FROM panier WHERE id = :id");
$stmt->execute(['id' => $ligne_id]);
$ligne = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si la ligne existe
if (!$ligne){
    header("Location: panier.php");
    exit;
}

//controle de l'action
$quantite = (int) $ligne['quantite'];

if ($action === "plus") {
    $quantite++;
} elseif($action === "moins"){
    $quantite--;
}

//controle quantite
if ($quantite <= 0){
    $del = $pdo->prepare("DELETE FROM panier WHERE id = :id");
    $del->execute(['id' => $ligne_id]);
}else{
    $upd = $pdo->prepare("UPDATE panier SET quantite = :q WHERE id = :id");
    $upd->execute(['q' => $quantite, 'id' => $ligne_id]);
}

header("Location: panier.php");
exit;
