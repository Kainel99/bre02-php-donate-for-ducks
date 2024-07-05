<?php

require_once '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../config/');
$dotenv->load();

$stripe = new \Stripe\StripeClient($_ENV["API__SECRET_KEY"]);

function calculateOrderAmount(int $amount): int {
    $safeAmount = htmlspecialchars($amount);
    return $safeAmount * 100;
}

header('Content-Type: application/json');

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr, true);

    $amount = calculateOrderAmount($jsonObj["amount"]);
    
    // TODO : Create a PaymentIntent with amount and currency in '$paymentIntent'
 $paymentIntent = $stripe->paymentIntents->create([
        'amount' => $amount,
        'currency' => 'eur',
    ]);

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];

    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

