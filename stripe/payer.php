<?php
require_once '../config/config.php';
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$commande_id = (int) ($_GET['id'] ?? 0);

if ($commande_id <= 0) {
    header('Location: ' . BASE_URL . 'public/panier.php');
    exit();
}

$stmt = $pdo->prepare(
    "SELECT total, payment_status FROM commandes WHERE id = :id"
);
$stmt->execute(['id' => $commande_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande || $commande['payment_status'] !== 'pending') {
    header('Location: panier.php');
    exit;
}

$session = \Stripe\Checkout\Session::create([
'mode' => 'payment',
'line_items' => [[
    'price_data' => [
        'currency' => 'eur',
        'product_data' => [
            'name' => 'Commande #' . $commande_id,
        ],
        'unit_amount' => (int) ($commande['total'] * 100),
        'quantity' => 1,
        'success_url' => 'http://localhost/stripe/success.php?id=' . $commande_id,
        'cancel_url'  => 'http://localhost/stripe/cancel.php?id=' . $commande_id,
    ]
]]
]);

header('Location: ' . $session->url);
exit();