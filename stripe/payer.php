<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$commande_id = (int) ($_GET['id'] ?? 0);

if ($commande_id <= 0) {
    header('Location: ' . BASE_URL . 'public/panier.php');
    exit();
}

$stmt = $pdo->prepare(
    "SELECT total, statut_payement FROM commandes WHERE id = :id"
);
$stmt->execute(['id' => $commande_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande || $commande['statut_payement'] !== 'en_attente') {
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
        ],
        'quantity' => 1,
    ]],
    'success_url' => 'http://localhost/ecommerce/stripe/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'  => 'http://localhost/ecommerce/stripe/cancel.php',
    
]);

header('Location: ' . $session->url);
exit();
