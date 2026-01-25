<?php 
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/panier_context.php';

// 2) Récupérer les lignes du panier
$query= "SELECT pa.id AS ligne_id, p.nom, p.prix, pa.quantite, p.id AS prod_id  FROM panier pa JOIN produits p ON pa.produit_id = p.id WHERE (pa.utilisateur_id = :uid AND :uid IS NOT NULL
)
OR
(
    pa.invite_id = :iid AND :iid IS NOT NULL
)";
$stmt = $pdo->prepare($query);
$stmt->execute([
    'uid' => $utilisateur_id,
    'iid' => $invite_id
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include "../includes/header.php"; ?>

<h2>Mon Panier</h2>

<?php if(empty($items)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>

<table>
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Total</th>
    </tr>

    <?php
    $total = 0;
    foreach ($items as $i):
        $ligne_total = $i['prix'] * $i['quantite'];
        $total += $ligne_total;
    ?>
    <tr>
        <td><?= htmlspecialchars($i['nom'])?></td>
        <td><?= htmlspecialchars($i['prix'])?></td>
        <td>
        <a href="modifier_quantite.php?id=<?= $i['ligne_id'] ?>&action=moins">-</a>
        <?= htmlspecialchars($i['quantite'])?>
        <a href="modifier_quantite.php?id=<?= $i['ligne_id'] ?>&action=plus">+</a>
        </td>
        <td><?= $ligne_total //number_format($ligne_total)?// ?></td>
        <td>
            <a href="supprimer_ligne.php?id=<?= $i['ligne_id'] ?>">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Prix total du panier : <?= $total ?> €</h3>
<a href="vider_panier.php">Vider le panier</a>

<form action="validation.php" method="POST">

    <button type="submit">Valider la commande</button>
</form>

<?php endif; ?>

<?php include "../includes/footer.php"; ?>