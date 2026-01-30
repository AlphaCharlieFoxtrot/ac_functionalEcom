<?php 
require_once __DIR__ . '/../../config/config.php';

// Vérifier admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin'){
    header("Location: ../../public/connexion.php");
    exit();
}

$commande_id = intval($_GET['id'] ?? 0);

// Vérifier id
if($commande_id === 0){
    header("Location: admin_commandes.php");
    exit();
}

// Récupérer infos commande
$query = "
    SELECT utilisateur_id, statut, total, date_commande
    FROM commandes
    WHERE id = :cid
    LIMIT 1
";
$stmt = $pdo->prepare($query);
$stmt->execute(['cid' => $commande_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header("Location: admin_commandes.php");
    exit();
}

// Statuts valides
$statuts_valides = [
    "en_attente",
    "en_preparation",
    "expediee",
    "livree",
    "annulee"
];

$error_message = '';

// POST traitement
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nouveau_statut = $_POST['statut'] ?? '';
    
    if(in_array($nouveau_statut, $statuts_valides)){
        
        $query = "UPDATE commandes SET statut = :statut WHERE id = :cid";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'statut' => $nouveau_statut,
            'cid'    => $commande_id,
        ]);

        header("Location: admin_commandes.php");
        exit();
    }

    $error_message = "Statut invalide.";
}
?>

<?php include "../../includes/header.php"; ?>

<h1>Modifier le statut de la commande n°<?= htmlspecialchars($commande_id) ?> (Admin)</h1>

<?php if($error_message !== ''): ?>
    <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
<?php endif; ?>

<article>
    <p><strong>Client ID :</strong> <?= htmlspecialchars($commande_id) ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
    <p><strong>Total :</strong> <?= htmlspecialchars($commande['total']) ?> €</p>
    <p><strong>Statut actuel :</strong> <?= htmlspecialchars($commande['statut']) ?></p>

    <form method="POST">
        <label for="statut">Nouveau statut :</label>
        <select name="statut" id="statut">
            <?php foreach($statuts_valides as $statut): ?>
                <option value="<?= $statut ?>" 
                    <?= $commande['statut'] === $statut ? 'selected' : '' ?>>
                    <?= ucfirst(str_replace('_',' ',$statut)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Mettre à jour</button>
    </form>
</article>

<?php include BASE_PATH . '/includes/footer.php'; ?>
